# Damaged - Forensics 75

```
All you have to do is to see this damaged image!

Attachment
for75_165560e4a08b23f7.zip 
```

We're given a broken BMP file. I can't open it with Eye of MATE, my normal system viewer.

I figured, why not try converting it to something else w/ ImageMagick (`convert damaged_image.bmp repaired_image.png`), and sure enough, ImageMagick doesn't care about the broken headers and outputs a valid PNG file:

![The flag](repaired_image.png)
