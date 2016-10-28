# Congested service - Misc 100

```
Try to connect to the NULL service and retrieve the flag!

7e0a98bb084ec0937553472e7aafcf68ff96baf4.ctf.site 20000
(Please don't flood the service, it works as expected)

Hint
Find the adequate protocol, it is not TCP but D**P, find it with the title 
```

It's DCCP. `nc -Z 7e0a98bb084ec0937553472e7aafcf68ff96baf4.ctf.site 20000`. I hade to get out from behind my NAT and mess with iptables to get this to work. It sent the flag over the connection when I connected, just like TCP.
