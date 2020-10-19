import numpy as np
import os, binascii


def eecd(a, b): #return x,y satisfying ax + by = 1
    a_1 = a
    a_2 = b
    a_3 =  a_1 % a_2
    q_1 = (a_1- a_3) / a_2
    m = np.array([[q_1, 1], [1, 0]])
    minv = np.linalg.inv(m) # inverse matrix of m
    result = minv
    while (a_2 != 1 and a_3 != 0):
        a_1 = a_2
        a_2 = a_3
        a_3 = a_1 % a_2
        q_1 = (a_1 -a_3) / a_2
        m[0,0] = q_1
        minv = np.linalg.inv(m)
        result = np.dot(minv, result)
    return np.array([result[0,0], result[0,1]])


def inverse (x, p): # get the inverse element of x
    inv = eecd(p, x)
    result = inv[1]
    return result


def affine(x1, y1, x2, y2, p):
    mem1 = (y2 - y1) % p # y2 - y1
    mem2 = (x2 - x1) % p # x2 - x1
    ramda = (mem1 * inverse(mem2, p)) % p # (y2 - y1)/(x2 - x1) = ramda
    mem3 = ramda ** 2 % p # ramda^2
    x3 = (mem3 - x1) % p # ramda^2 -x1
    x3 = (mem3 - x2) % p # ramda^2 -x1 -x2
    y3 = (x1 - x3) % p # x1 - x3
    y3 = (ramda * y3)  % p # ramda(x1 - x3)
    y3 = (y3 - y1) % p # ramda(x1 - x3) - y1
    x3 = int(x3)
    y3 = int(y3)
    return [x3, y3]


def affine2(x1, y1, a, p):
    y3 = (x1 ** 2) % p # x1^2
    y3 = (3 * y3) % p # 3x1^2
    y3 = (y3 + a) % p # 3x1^2+a
    mem2 = (2 * y1) % p# 2y1
    y3 = (y3 * inverse(mem2, p)) % p # (3x1^2+a)/2y1=ramda
    x3= (y3 ** 2) % p # ramda^2
    mem2 = (x1 * 2) % p # 2x1
    x3 = (x3 - mem2) % p # ramda^2 - 2x1
    mem2 = (x1 - x3) % p #  x1 - x3
    y3 = (y3 * mem2) % p # ramda(x1 - x3)
    y3 = (y3 - y1) % p # ramda(x1 - x3) - y1
    x3 = int(x3)
    y3 = int(y3)
    return [x3, y3]


def scalar(gx, gy, a, b, p, k):
    # think as binary
    k = bin(k)[2:]
    l = int(k[0])
    if(l == 1):
        x = gx
        y = gy
    else:
        x = 0
        y = 0
    for i in range(len(k)-1):
        l = int(k[i+1])
        if(l==1):
            j = i
            n = affine2(gx, gy, a, p)
            while(j>0):
                n = affine2(n[0], n[1], a, p)
                j -= 1
            if (x == n[0]):
                n = affine2(x, y, a, p)
            else:
                n = affine(x, y, n[0], n[1], p)
            x = n[0]
            y = n[1]
    return [x,y]


def pub_key(x, G, a, b, p):
    gx = G[0]
    gy = G[1]
    return scalar(gx, gy, a, b, p, x)


# encrypt
def crypt(r, m, Y, a, b, p):
    V = scalar(Y[0], Y[1], a, b, p, r) # V = rY = (vx, vy)
    #print(V)
    vx = V[0]
    c = vx ^ m
    return c


#decrypt
def decrypt(c, r, x, G, a, b, p):
    U = scalar(G[0], G[1], a, b, p, r) # U = rG = (ux, uy)
    V = scalar(U[0], U[1], a, b, p, x) # xU = x(rG) = r(xG) = rY = V
    m = c ^ V[0]
    return m