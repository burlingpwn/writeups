#!/bin/sh

#git cloned this first
cd exposed.vuln.icec.tf
cd .git/objects
for i in */*; do f=$(echo $i | sed 's/\///'); git cat-file -p $f ; done | grep IceCTF
