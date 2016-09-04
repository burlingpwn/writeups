## Kitty, web, 70 points

__Description__:

They managed to secure their website this time and moved the hashing to the server :(. We managed to leak this hash of the admin's password though! `c7e83c01ed3ef54812673569b2d79c4e1f6554ffeb27706e98c067de9ab12d1a`. Can you get the flag? kitty.vuln.icec.tf


__Solver__: [mut3](https://github.com/mut3)

This web challenge supplied a password hash and a login portal. Looking at the html of the login portal revealed some input validation on the password field.

```
<input id="password" class="u-full-width" name="password" placeholder="Password" required="" pattern="[A-Z][a-z][0-9][0-9][\?%$@#\^\*\(\)\[\];:]" type="password">
```

It seems there is a very limited range of valid passwords, one uppercase letter `[A-Z]`, one lowercase letter `[a-z]`, two digits `[0-9]`, and one special character from the set `?%$@#^*()[]:;` `[\?%$@#\^\*\(\)\[\];:]`. This means there are only are only `26*26*10*10*13 = 878800` possible passwords. A script could be hacked together to try to collide with the supplied hash, but why bother when a better tool exists?

It turns out the challenge name 'Kitty' was a hint to use [hashcat](https://hashcat.net/hashcat/), a password cracking tool, which makes short work of this SHA 256 hash when given the custom pattern for this password.

```
# hashcat --hash-type=1400 --custom-charset1='%#^*{}[];:?' -a 3  hash.txt ?u?l?d?d?1
Initializing hashcat v2.00 with 4 threads and 32mb segment-size...

Added hashes from file hash.txt: 1 (1 salts)
Activating quick-digest mode for single-hash

c7e83c01ed3ef54812673569b2d79c4e1f6554ffeb27706e98c067de9ab12d1a:Vo83*

All hashes have been recovered

Input.Mode: Mask (?u?l?d?d?1) [5]
Index.....: 0/1 (segment), 676000 (words), 0 (bytes)
Recovered.: 1/1 hashes, 1/1 salts
Speed/sec.: - plains, 565.99k words
Progress..: 566876/676000 (83.86%)
Running...: 00:00:00:01
Estimated.: --:--:--:--

```

Logging into the web portal with admin:Vo83* gave the flag `Your flag is: IceCTF{i_guess_hashing_isnt_everything_in_this_world}`
