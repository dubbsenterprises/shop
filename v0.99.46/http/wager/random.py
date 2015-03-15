#!/usr/bin/env python

import sys

def permsum(n, k):
    if k < 0 or n < 0:
        return "Error"
    if not k:
        return [[0] * n]

    if not n:
        return []
    if n == 1:
        return [[k]]

    return [[0] + val for val in permsum(n - 1, k)] + [[val[0] + 1] + val[1:]
                                                  for val in permsum(n, k -1)]
def main():
    n = int(sys.argv[2])
    k = int(sys.argv[1])

    solutions = permsum(n, k)
    for sl in solutions:
        sls = [str(num) for num in sl]
        ss = ','.join(sls)
        s = '%s,%s,%s,%s' % (sl[0], sl[1], sl[2], sl[3])
        print('%s' % (ss))


if __name__ == '__main__':
    main()