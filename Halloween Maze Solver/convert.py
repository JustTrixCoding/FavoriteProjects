class Convert:
    def __init__(self):
        pass
    def num_matrix(self, lines):
        row = []
        for line in lines: #P=9, #=8, S=0, @=7
            line = line.strip()
            col = []
            for car in line:
                if car == "P":
                    col.append(9)
                elif car == "#":
                    col.append(8)
                elif car == "@":
                    col.append(7)
                elif car == "S":
                    col.append(0)
                else:
                    raise RuntimeError("Unidentified Character")                
            row.append(col)
        return row
    def char_matrix(self, scoured_matrix):
        row = []
        for line in scoured_matrix:
            col = []
            for num in line:
                num = str(num)
                if num == "8":
                    col.append("#")
                elif num == "9":
                    col.append("P")
                elif num == "7" or num == "5":
                    col.append("@")
                elif num == "0":
                    col.append("S")
                elif num == "1":
                    col.append("B")
                elif num == "2":
                    col.append("B")
                else:
                    raise ValueError("invalid character")
            row.append(col)
        return row
    
