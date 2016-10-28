# Congested service - Misc 100

```
Try to connect to the NULL service and retrieve the flag!

7e0a98bb084ec0937553472e7aafcf68ff96baf4.ctf.site 20000
(Please don't flood the service, it works as expected)

Hint
Find the adequate protocol, it is not TCP but D**P, find it with the title 
```

It's DCCP. `nc -Z 7e0a98bb084ec0937553472e7aafcf68ff96baf4.ctf.site 20000`. You might have to disable some firewalls to get this to work.
