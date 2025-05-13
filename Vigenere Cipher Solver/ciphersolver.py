import time

def key_finder(seq, klen, fwlen):
    start_time = time.time()
    with open("dict.txt", 'r') as file:
        file_contents = file.read()

    numseq = [ord(char) - 65 for char in seq]
    fword = numseq[:fwlen]

    tempkey = [0] * klen

    while tempkey[0] != 26:
        a = 0
        tempfword = []
        while a < len(fword): #decrypts the first word
            b = 0
            while b < klen: #runs as many times as needed
                if fword[a] - tempkey[b] < 0:
                    tempfword.append(26 + fword[a] - tempkey[b]) #reverse encryption
                    b+=1
                    a+=1
                else:
                    tempfword.append(fword[a] - tempkey[b]) #reverse encryption
                    b+=1
                    a+=1
                if a == len(fword): #checks to make sure we dont go over the len of the fword
                    b = klen
        tempfword = [chr(char + 65) for char in tempfword]

        if ''.join(tempfword) in file_contents:
            password_cracker(numseq, tempkey)

        tempkey[-1] += 1
        for i in range(klen - 1, 0, -1):
            if tempkey[i] == 26 and i != 0:
                tempkey[i] = 0
                tempkey[i-1] += 1

    end_time = time.time()
    elapsed_time = end_time - start_time
    print(f"Time: {elapsed_time}")

def password_cracker(n_seq, n_key):
    m = 0
    temp = []
    for num in n_seq:
        temp.append(num)
    while m < len(temp):
        n = 0
        while n < len(n_key):
            if temp[m] - n_key[n] < 0:
                temp[m] = chr(26 + temp[m] - n_key[n] + 65)
            else:
                temp[m] = chr(65 + temp[m] - n_key[n])
            n+=1
            m+=1
            if m == len(temp):
                n = len(n_key)
    print(''.join(temp))
def main():
    i_seq = input("Enter a sequence: ")
    i_key = int(input("Enter key length: "))
    i_flen = int(input("Enter first word length: "))

    key_finder(i_seq, i_key, i_flen)

if __name__ == "__main__":
    main()
