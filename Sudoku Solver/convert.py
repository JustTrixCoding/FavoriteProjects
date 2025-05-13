class Convert:
    def __init__(self): #nothing needed
        pass
    def make_num(self, m): #converts the matrix into a matrix of ints
        y = 0 #sets y to 0
        while y < 9: #loops 9 times
            x = 0 #sets x to 0
            while x < 9: #loops 9 times
                if m[y][x] == "_": #converts empty spaces to 0s
                    m[y][x] = 0 #sets space to 0
                else: #num exists in the space
                    m[y][x] = int(m[y][x]) #set the string to an int
                x += 1 #increments x
            y += 1#increments y
        return m #returns num matrix
    def find_original(self, m): #creates a set of all the originally placed nums
        origin = set() #creates a set origin
        y = 0 #sets y to 0
        while y < 9: #loops 9 times
            x = 0 #sets x to 0
            while x < 9: #loops 9 times
                if m[y][x] != 0: #if the number isnt 0
                    origin.add((y,x)) #add the location to origin
                x += 1#increments x
            y += 1#increments y
        return origin#returns the set of locations of originally placed numbers
