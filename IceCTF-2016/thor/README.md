## Thor's a hacker now, misc, 55 points

__Description__:

Thor has been staring at this for hours and he can't make any sense out of it, can you help him figure out what it is? [`thor.txt`](thor.txt)


__Solver__: [mut3](https://github.com/mut3)

This challenge gave a text file containing a hex dump. `xxd -r` reverses hex dumps into binary files.

```
# xxd -r thor.txt thor
# file thor
thor: lzip compressed data, version: 1
```

Lzip? the hexdump was of some obscure compression scheme.

```
# lzip -d thor
# file thor.out
thor.out: JPEG image data, JFIF standard 1.01, resolution (DPI), density 72x72, segment length 16, Exif Standard: [TIFF image data, little-endian, direntries=5, xresolution=74, yresolution=82, resolutionunit=2, software=GIMP 2.8.18], progressive, precision 8, 1600x680, frames 3
```

Of a JPEG.

```
mv thor.out thor.jpg
```

Containing the flag

![thor flag](thor.jpg)


#### Bonus Story about mut3 being stupid:
I actually got super stuck on this challenge because there's a gemfile utility tool named thor installed on my system, and thought this was a pwn hidden in a misc challenge for a bit.
