'''
Program: Assignment 6
Description: Recursive algorithym that finds if Sudoku puzzle is solveable, and if so solves it
Inputs:
  make_num(matrix)
  find_origin(matrix)
  solve_puzzle(y,x, direction)
  can_place(y,x,number in question)
Outputs:
1)
[5, 3, 9, 8, 2, 4, 1, 7, 6]
[1, 8, 6, 5, 7, 9, 2, 4, 3]
[4, 7, 2, 1, 3, 6, 8, 9, 5]
[9, 2, 4, 3, 8, 7, 5, 6, 1]
[3, 1, 8, 6, 9, 5, 4, 2, 7]
[6, 5, 7, 4, 1, 2, 3, 8, 9]
[7, 4, 5, 2, 6, 1, 9, 3, 8]
[8, 9, 1, 7, 4, 3, 6, 5, 2]
[2, 6, 3, 9, 5, 8, 7, 1, 4]
2)
[5, 3, 9, 8, 2, 4, 1, 7, 6]
[1, 8, 6, 5, 7, 9, 2, 4, 3]
[4, 7, 2, 1, 3, 6, 8, 9, 5]
[9, 2, 4, 3, 8, 7, 5, 6, 1]
[3, 1, 8, 6, 9, 5, 4, 2, 7]
[6, 5, 7, 4, 1, 2, 3, 8, 9]
[7, 4, 5, 2, 6, 1, 9, 3, 8]
[8, 9, 1, 7, 4, 3, 6, 5, 2]
[2, 6, 3, 9, 5, 8, 7, 1, 4]
3)
[7, 8, 5, 3, 2, 6, 1, 4, 9]
[3, 4, 2, 5, 1, 9, 6, 7, 8]
[6, 1, 9, 8, 4, 7, 5, 2, 3]
[4, 9, 3, 6, 8, 5, 2, 1, 7]
[1, 2, 8, 9, 7, 4, 3, 6, 5]
[5, 7, 6, 1, 3, 2, 8, 9, 4]
[2, 5, 1, 4, 9, 3, 7, 8, 6]
[9, 3, 7, 2, 6, 8, 4, 5, 1]
[8, 6, 4, 7, 5, 1, 9, 3, 2]
4)
No solution found
[7, 3, 2, 0, 8, 4, 6, 9, 1]
[9, 1, 0, 3, 0, 0, 5, 2, 0]
[8, 0, 0, 9, 0, 2, 7, 3, 4]
[5, 4, 9, 0, 0, 0, 8, 6, 3]
[1, 0, 0, 0, 3, 0, 2, 0, 7]
[0, 2, 3, 0, 4, 8, 9, 1, 0]
[3, 9, 0, 8, 5, 0, 4, 7, 2]
[0, 7, 0, 4, 0, 3, 0, 0, 6]
[4, 6, 8, 0, 7, 0, 0, 5, 9]
5)
[7, 5, 3, 6, 8, 4, 2, 9, 1]
[4, 1, 2, 3, 7, 9, 6, 8, 5]
[9, 6, 8, 1, 2, 5, 7, 3, 4]
[6, 4, 1, 7, 3, 2, 8, 5, 9]
[3, 8, 9, 4, 5, 6, 1, 2, 7]
[2, 7, 5, 9, 1, 8, 3, 4, 6]
[5, 2, 4, 8, 6, 7, 9, 1, 3]
[8, 3, 6, 5, 9, 1, 4, 7, 2]
[1, 9, 7, 2, 4, 3, 5, 6, 8]
Name: Ben Renner
Creation Date: November 10, 2022
'''
from executive import Executive #imports executive
import os.path #checks if file exist in folder
def main(): #defines main
    file_name = input("Enter a file name: ") #allows user to input a txt file
    if os.path.exists(file_name) == False: #checks if the inputted file exists in the folder
        raise RuntimeError("File not found")#throws an error if file doesnt exist
    process_file = Executive(file_name)#defines the file we want to process as a part of the Executive Class
    process_file.run()#runs our program
main()
