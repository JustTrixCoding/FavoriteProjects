from solve import Solve #imports the class Solve
from convert import Convert #imports the class Convert
class Executive:#defines Executive Class
    def __init__(self, file):#defines __init__
        self.file_name = file #takes in the file from main and converts it to an executable file
    def run(self):#defines run
        conv = Convert() #initializes Convert class
        new_file = open(self.file_name, "r") #opens the inputted file as new_file
        content = new_file.readlines()#content now holds all the lines from the file along with white spaces
        x = 0 #sets x to 0
        col = [] #defines an empty collum
        while x < 9: #runs 9 times
            row = content[x].strip().split(" ")#defines row to be a list of chars with no white spaces
            col.append(row) #adds the row to col
            x += 1 #adds one to x
        num_matrix = conv.make_num(col)#calls make_num in Convert class to make all the _ 0s
        orig_num = conv.find_original(col) #calls find_original in Convert class to make a list of all pre-placed numbers
        s = Solve(num_matrix, orig_num)#initializes Solve class
        n_matrix = s.solve_puzzle(0,0,'f')#calls solve_puzzle in Solve class to find either a solution of if the puzzle has no solution
        check = True#sets check to True
        for r in n_matrix:#checks to see if puzzles was solved
            if 0 in r:#puzzle was not solved if 0's exist
                check = False#sets check to False
        if check == False:#checks if check is False
            print('No solution found') #print no solution
        for r in n_matrix:#iterates over matrix
            print(r)#prints out each row
        
