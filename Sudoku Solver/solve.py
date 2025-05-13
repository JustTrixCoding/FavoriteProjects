import sys #imports sys
sys.setrecursionlimit(2000) #puzzle 5 a b**** (takes to long)
class Solve: #creates class Solve
    def __init__(self, matrix, orig_nums):#defines __init__
        self.m = matrix #creates a replica of the matrix
        self.on = orig_nums #creates a replica of the orig_nums
    def solve_puzzle(self, y, x, direct): #finds the solution to the puzzle, otherwise returns an unbeatable puzzle
        if direct == 'f': #shows the we are going forward
            if (y,x) in self.on: #num is a pre-placed num
                if x != 8: #checks to make sure we are not at the end of the row
                    return self.solve_puzzle(y, x+1, direct) #increments to the next value to the right
                elif y != 8:#we are at the end of the row, need to move down and back to starting x
                    return self.solve_puzzle(y+1, 0, direct) #increments to the start of the next row
                else:#we are at the end of the puzzle, and it is complete
                    return self.m #returns the matrix
            else: #num is a bot-placed num/empty num
                a = self.m[y][x] + 1 #sets a to the value in place
                while a < 10:#loops 10-a times
                    if self.can_place(y,x,a):#checks to see if the number can be placed
                        self.m[y][x] = a #place the number in the spot
                        if x != 8:#checks to make sure we are not at the end of the row
                            return self.solve_puzzle(y, x+1, direct)#increments to the next value to the right
                        elif y != 8: #we are at the end of the row, need to move down and back to starting x
                            return self.solve_puzzle(y+1, 0, direct)#increments to the start of the next row
                        else:#we are at the end of the puzzle, and it is complete
                            return self.m#returns the matrix
                    a += 1#increments a
                if a == 10: #must revert
                    direct = 'b'#sets the mode to moving backwards
                    if x != 0:#checks to make sure we are not at the start of a row
                        self.m[y][x] = 0#sets current position to 0
                        return self.solve_puzzle(y, x-1, direct)#increments to the previous value to the left
                    elif y != 0:#we are at the end of the row, need to move up and back to the end the previous row
                        self.m[y][x] = 0#sets current spot to 0
                        return self.solve_puzzle(y-1, 8, direct)#increments the the end of the previous row
                    else: #we are at the start of the puzzle, we can not solve the puzzle
                        return self.m#return the matrix
        else:#we are moving backwards
            if (y,x) in self.on: #num is a pre-placed num
                if x != 0:#checks to make sure we are not at the start of a row
                    return self.solve_puzzle(y, x-1, direct)#increments to the previous value to the left
                elif y != 0:#we are at the end of the row, need to move up and back to the end the previous row
                    return self.solve_puzzle(y-1, 8, direct)#increments the the end of the previous row
                else:#we are at the start of the puzzle, we can not solve the puzzle
                    return self.m#return the matrix
            else:#we can place a number here
                direct = 'f' #set the mode to forward
                a = self.m[y][x] + 1 #sets a to the value in place
                while a < 10:#loops 10-a times
                    if self.can_place(y,x,a):#checks to see if the number can be placed
                        self.m[y][x] = a #place the number in the spot
                        if x != 8:#checks to make sure we are not at the end of the row
                            return self.solve_puzzle(y, x+1, direct)#increments to the next value to the right
                        elif y != 8:#we are at the end of the row, need to move down and back to starting x
                            return self.solve_puzzle(y+1, 0, direct)#increments to the start of the next row
                        else:#we are at the end of the puzzle, and it is complete
                            return self.m#returns the matrix
                    a += 1#increments a
                if a == 10: #must revert
                    direct = 'b'#sets mode to backwards
                    if x != 0:#checks to make sure we are not at the start of a row
                        self.m[y][x] = 0#sets current position to 0
                        return self.solve_puzzle(y, x-1, direct)#increments to the previous value to the left
                    elif y != 0:#we are at the end of the row, need to move up and back to the end the previous row
                        self.m[y][x] = 0#sets current position to 0
                        return self.solve_puzzle(y-1, 8, direct)#increments the the end of the previous row
                    else:#we are at the start of the puzzle, we can not solve the puzzle
                        return self.m#return the matrix

            
                
    def can_place(self, y, x, num):#function that checks if a number can be placed in the given location
        sec1 = [abs(self.m[0][0]),abs(self.m[0][1]),abs(self.m[0][2]),abs(self.m[1][0]),abs(self.m[1][1]),abs(self.m[1][2]),abs(self.m[2][0]),abs(self.m[2][1]),abs(self.m[2][2])]#defines section1
        sec2 = [abs(self.m[0][3]),abs(self.m[0][4]),abs(self.m[0][5]),abs(self.m[1][3]),abs(self.m[1][4]),abs(self.m[1][5]),abs(self.m[2][3]),abs(self.m[2][4]),abs(self.m[2][5])]#defines section2
        sec3 = [abs(self.m[0][6]),abs(self.m[0][7]),abs(self.m[0][8]),abs(self.m[1][6]),abs(self.m[1][7]),abs(self.m[1][8]),abs(self.m[2][6]),abs(self.m[2][7]),abs(self.m[2][8])]#defines section3
        sec4 = [abs(self.m[3][0]),abs(self.m[3][1]),abs(self.m[3][2]),abs(self.m[4][0]),abs(self.m[4][1]),abs(self.m[4][2]),abs(self.m[5][0]),abs(self.m[5][1]),abs(self.m[5][2])]#defines section4
        sec5 = [abs(self.m[3][3]),abs(self.m[3][4]),abs(self.m[3][5]),abs(self.m[4][3]),abs(self.m[4][4]),abs(self.m[4][5]),abs(self.m[5][3]),abs(self.m[5][4]),abs(self.m[5][5])]#defines section5
        sec6 = [abs(self.m[3][6]),abs(self.m[3][7]),abs(self.m[3][8]),abs(self.m[4][6]),abs(self.m[4][7]),abs(self.m[4][8]),abs(self.m[5][6]),abs(self.m[5][7]),abs(self.m[5][8])]#defines section6
        sec7 = [abs(self.m[6][0]),abs(self.m[6][1]),abs(self.m[6][2]),abs(self.m[7][0]),abs(self.m[7][1]),abs(self.m[7][2]),abs(self.m[8][0]),abs(self.m[8][1]),abs(self.m[8][2])]#defines section7
        sec8 = [abs(self.m[6][3]),abs(self.m[6][4]),abs(self.m[6][5]),abs(self.m[7][3]),abs(self.m[7][4]),abs(self.m[7][5]),abs(self.m[8][3]),abs(self.m[8][4]),abs(self.m[8][5])]#defines section8
        sec9 = [abs(self.m[6][6]),abs(self.m[6][7]),abs(self.m[6][8]),abs(self.m[7][6]),abs(self.m[7][7]),abs(self.m[7][8]),abs(self.m[8][6]),abs(self.m[8][7]),abs(self.m[8][8])]#defines section9
        for item in self.m[y]: #iterates over the row
            if num == abs(item):#checks if the number is already in the row
                return False#it is, return false
        for row in self.m:#iterates over each row
            if num == abs(row[x]):#checks if the number is already in the collum
                return False#it is, return false
        if y <= 2:#checks if the location is in sections 1-3
            if x <= 2:#checks if the location is in sec1
                if num in sec1:#checks to see if the num exists in this section
                    return False#it is, return false
            elif x >= 3 and x <= 5:#checks if the location is in sec2
                if num in sec2:#checks to see if the num exists in this section
                    return False#it is, return false
            else:#the location is in sec3
                if num in sec3:#checks to see if the num exists in this section
                    return False#it is, return false
        if y >= 3 and y <= 5:#checks if the location is in sections 4-6
            if x <= 2:#checks if the location is in sec4
                if num in sec4:#checks to see if the num exists in this section
                    return False#it is, return false
            elif x >= 3 and x <= 5:#checks if the location is in sec5
                if num in sec5:#checks to see if the num exists in this section
                    return False#it is, return false
            else:#the location is in sec6
                if num in sec6:#checks to see if the num exists in this section
                    return False#it is, return false
        if y >= 6 and y <= 8:#checks if the location is in sections 7-9
            if x <= 2:#checks if the location is in sec7
                if num in sec7:#checks to see if the num exists in this section
                    return False#it is, return false
            elif x >= 3 and x <= 5:#checks if the location is in sec8
                if num in sec8:#checks to see if the num exists in this section
                    return False#it is, return false
            else:#the location is in sec9
                if num in sec9:#checks to see if the num exists in this section
                    return False#it is, return false
        return True#passed every test, return true
