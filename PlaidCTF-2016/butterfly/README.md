butterfly
=========

This challenge was a "pwnable" worth 150 points:

> Sometimes the universe smiles upon you. And sometimes, well, you just have to roll your  sleeves up and do [things yourself](https://github.com/burlingpwn/Writeups/raw/butterfly/PlaidCTF-2016/butterfly/butterfly_33e86bcc2f0a21d57970dc6907867bed). Running at butterfly.pwning.xxx:9999

Reversing
---------

Running the binary reveals this cryptic prompt:

```
brian@katahdin:butterfly$ ./butterfly_33e86bcc2f0a21d57970dc6907867bed
THOU ART GOD, WHITHER CASTEST THY COSMIC RAY?
```

When you type something, the program reveals that it calls ```mprotect```:

```
brian@katahdin:butterfly$ ./butterfly_33e86bcc2f0a21d57970dc6907867bed
THOU ART GOD, WHITHER CASTEST THY COSMIC RAY?
huh?
mprotect1: Cannot allocate memory
brian@katahdin:butterfly$
```

In case you don't know, ```mprotect``` is a function that changes the read/write/execute permissions of memory mappings, similarly to Unix file permissions. So far, so good.

Looking at the disassembly, ```main``` does a bounded read from ```stdin``` to a buffer at the top of the stack, and falls through to the following basic block on success:

```
  4007d8:       48 8d 3c 24             lea    rdi,[rsp]
  4007dc:       31 f6                   xor    esi,esi
  4007de:       31 d2                   xor    edx,edx
  4007e0:       e8 6b fe ff ff          call   400650 <strtol@plt>
  4007e5:       48 89 c3                mov    rbx,rax
  4007e8:       48 89 dd                mov    rbp,rbx
  4007eb:       48 c1 fd 03             sar    rbp,0x3
  4007ef:       49 89 ef                mov    r15,rbp
  4007f2:       49 81 e7 00 f0 ff ff    and    r15,0xfffffffffffff000
  4007f9:       be 00 10 00 00          mov    esi,0x1000
  4007fe:       ba 07 00 00 00          mov    edx,0x7
  400803:       4c 89 ff                mov    rdi,r15
  400806:       e8 55 fe ff ff          call   400660 <mprotect@plt>
  40080b:       85 c0                   test   eax,eax
  40080d:       75 5c                   jne    40086b <main+0xe3>
```

There's a few things going on here. First, it's using ```strtol``` to convert the input into a signed integer, doing some funky arithmetic on the result, and using that as the ```address``` argument to a call to ```mprotect``` with ```PROT_READ|PROT_WRITE|PROT_EXEC```. The effect of this is that, if we can account for the arithmetic, we should be able to set some page of memory to have RWX permissions.

I'll spare you the trouble and just tell you up front that the arithmetic is this:

```
address = (input >> 3) & 0xfffffffffffff000;
```

which is shifting off the 3 least significant bits of the input, and then masking the result to a page boundary. So, if, after the shift, we end up with an address that is in the middle of the page, the mask will give us the page's base address. (This is a pretty standard usage of ```mprotect```, actually.)

The question is, what about those 3 bits that we threw away? The next basic block provides the answer:

```
  40080f:       80 e3 07                and    bl,0x7
  400812:       41 be 01 00 00 00       mov    r14d,0x1
  400818:       b8 01 00 00 00          mov    eax,0x1
  40081d:       88 d9                   mov    cl,bl
  40081f:       d3 e0                   shl    eax,cl
  400821:       0f b6 4d 00             movzx  ecx,BYTE PTR [rbp+0x0]
  400825:       31 c1                   xor    ecx,eax
  400827:       88 4d 00                mov    BYTE PTR [rbp+0x0],cl
  40082a:       be 00 10 00 00          mov    esi,0x1000
  40082f:       ba 05 00 00 00          mov    edx,0x5
  400834:       4c 89 ff                mov    rdi,r15
  400837:       e8 24 fe ff ff          call   400660 <mprotect@plt>
  40083c:       85 c0                   test   eax,eax
  40083e:       75 37                   jne    400877 <main+0xef>
```

This takes the intermediate result from above (```input >> 3```, just before the mask), and uses it as the address for another funky operation. It will help if we use some variable names. How about
```
addr = input >> 3;
bit = input & 0x7;
```

The basic block then does ```*addr ^= bit;```. What this means is that we can flip a single bit at any address in memory, just by providing the right integer as input. The trick is finding the right bit.

The basic block then uses ```mprotect``` to set the page back to RX permissions.

Exploitation
------------

Rather than use our brains, we opted to write a script (```brutus.py```) to flip every single bit in the text segment and log the result. The output was a bit hard to parse, so we zeroed in on bits in the two most interesting instructions that occurred after the flip: the calls to ```mprotect``` and ```puts```.

We noticed that when we flipped one of the bits in the second ```mprotect``` call, the program cycled back up to somewhere in the initialization routines, and printed the cryptic message about cosmic rays again. This would allow us to flip more bits. The integer input that caused this to happen was 33571270. Try it yourself!

At this point, we had everything we needed. We grabbed a pretty basic local ```execve``` shellcode off [exploit-db](https://www.exploit-db.com/) and xored it with the beginning of the text segment at ```0x400000```. The 1's in the output were the bits we needed to flip. We also calculated the integer inputs corresponding to these bits.

We also turned a later instruction into a ```jmp 0x400000``` using the same technique. Finally, we needed to undo the loop by un-flipping that original bit, and the program would fall through to our ```jmp``` instruction. Pwnage would be ours!!!

```
brian@katahdin:butterfly$ ./exploit.py
33571270
WAS IT WORTH IT???
ls
butterfly
flag
wrapper
cat flag
PCTF{b1t_fl1ps_4r3_0P_r1t3}
^C
brian@katahdin:butterfly$
```

In summary, here's the overall structure of our exploit:
- Flip a bit that turns most of ```main()``` into an infinite loop.
- Flip all the bits that will transform the start of the text segment into our shellcode.
- Flip the bits that will transform a later instruction into a ```jmp``` into the shellcode.
- Un-flip the bit that caused the infinite loop.
- Capture the flag.

The title
---------
By the way, in case it wasn't obvious, the reference to cosmic rays in the programs' prompt is referring to the fact that cosmic rays can flip bits inside a computer (that's one reason servers use ECC RAM). Butterfly, though, that's a reference to [this](https://xkcd.com/378/) xkcd comic about programming using butterflies and cosmic rays: ![xkcd comic](https://imgs.xkcd.com/comics/real_programmers.png).
