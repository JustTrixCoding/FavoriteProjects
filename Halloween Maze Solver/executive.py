from convert import Convert
from scour import Scour
class Executive:
    def __init__(self, file):
        self.file_name = file
    def run(self):
        new_conversion = Convert()
        new_file = open(self.file_name, "r")
        content = new_file.readlines()
        rowcol = content[0].strip().split(" ")
        if int(rowcol[0]) < 1 or int(rowcol[1]) < 1:
            raise ValueError("can't have a row or colum less than 1")
        starting_spot = content[1].strip().split(" ")
        if int(starting_spot[0]) < 0 or int(starting_spot[0]) >= int(rowcol[0]):
            raise ValueError("starting index out of range")
        if int(starting_spot[1]) < 0 or int(starting_spot[1]) >= int(rowcol[1]):
            raise ValueError("starting index out of range")
        num_matrix = new_conversion.num_matrix(content[2:int(rowcol[0])+2])
        scour = Scour(num_matrix)
        scour.scour_maze(int(starting_spot[0]), int(starting_spot[1]))
        scoured_maze = scour.get_maze()
        finished_maze = new_conversion.char_matrix(scoured_maze) #now for the output
        print(f"{rowcol[0]} {rowcol[1]}")#width and length
        print(f"{starting_spot[0]} {starting_spot[1]}")#starting spot
        for line in finished_maze:
            line = ''.join(line)
            print(line)
        total = scour.get_eaten()
        print(f"Total eaten: {total}")
            
        
