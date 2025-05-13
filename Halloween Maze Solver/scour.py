class Scour:
    def __init__(self, num_list):
        self._nlist = num_list
        self._p_eaten = 0
        self._is_sewer = False
        self._hole_y = 0
        self._hole_x = 0
    def scour_maze(self, y, x):
        if self._nlist[y-1][x] == 0 or self._nlist[y-1][x] == 9 or self._nlist[y-1][x] == 7:#up
            if y != 0:
                if self._nlist[y-1][x] == 7:
                    self._is_sewer = True
                    self._hole_y = y-1
                    self._hole_x = x
                if self._nlist[y][x] == 7 or self._nlist[y][x] == 5:
                    self._nlist[y][x] = 5
                elif self._nlist[y][x] == 9:
                    self._nlist[y][x] = 1
                    self._p_eaten += 1
                else:
                    self._nlist[y][x] = 1
                self.scour_maze(y-1, x)
        if x != len(self._nlist[0])-1:
            if self._nlist[y][x+1] == 0 or self._nlist[y][x+1] == 9 or self._nlist[y][x+1] == 7: #right
                if self._nlist[y][x+1] == 7:
                    self._is_sewer = True
                    self._hole_y = y
                    self._hole_x = x+1
                if self._nlist[y][x] == 7:
                    self._nlist[y][x] = 5
                elif self._nlist[y][x] == 9:
                    self._nlist[y][x] = 1
                    self._p_eaten += 1
                else:
                    self._nlist[y][x] = 1
                self.scour_maze(y, x+1)
        if y != len(self._nlist)-1:
            if self._nlist[y+1][x] == 0 or self._nlist[y+1][x] == 9 or self._nlist[y+1][x] == 7: #down
                if self._nlist[y+1][x] == 7:
                    self._is_sewer = True
                    self._hole_y = y+1
                    self._hole_x = x
                if self._nlist[y][x] == 7:
                    self._nlist[y][x] = 5
                elif self._nlist[y][x] == 9:
                    self._nlist[y][x] = 1
                    self._p_eaten += 1
                else:
                    self._nlist[y][x] = 1
                self.scour_maze(y+1, x)
        if self._nlist[y][x-1] == 0 or self._nlist[y][x-1] == 9 or self._nlist[y][x-1] == 7: #left
            if x != 0:
                if self._nlist[y][x-1] == 7:
                    self._is_sewer = True
                    self._hole_y = y
                    self._hole_x = x-1
                if self._nlist[y][x] == 7:
                    self._nlist[y][x] = 5
                elif self._nlist[y][x] == 9:
                    self._nlist[y][x] = 1
                    self._p_eaten += 1
                else:
                    self._nlist[y][x] = 1
                self.scour_maze(y, x-1)
        else: #cant move, revert or return
            if y != len(self._nlist) - 1:
                if self._nlist[y+1][x] == 1 or self._nlist[y+1][x] == 5:
                    if self._nlist[y][x] == 5 or self._nlist[y][x] == 7:
                        self._nlist[y][x] = 5
                        self.scour_maze(y+1, x)
                    elif self._nlist[y][x] == 9:
                        self._p_eaten += 1
                        self._nlist[y][x] = 2
                        self.scour_maze(y, x-1)
                    else:
                        self._nlist[y][x] = 2
                        self.scour_maze(y+1, x)
            if x != len(self._nlist[0]) - 1:
                if self._nlist[y][x+1] == 1 or self._nlist[y][x+1] == 5:
                    if self._nlist[y][x] == 5 or self._nlist[y][x] == 7:
                        self._nlist[y][x] = 5
                        self.scour_maze(y, x+1)
                    elif self._nlist[y][x] == 9:
                        self._p_eaten += 1
                        self._nlist[y][x] = 2
                        self.scour_maze(y, x-1)
                    else:
                        self._nlist[y][x] = 2
                        self.scour_maze(y, x+1)
            if self._nlist[y-1][x] == 1 or self._nlist[y-1][x] == 5:
                if y != 0:
                    if self._nlist[y][x] == 5 or self._nlist[y][x] == 7:
                        self._nlist[y][x] = 5
                        self.scour_maze(y-1, x)
                    elif self._nlist[y][x] == 9:
                        self._p_eaten += 1
                        self._nlist[y][x] = 2
                        self.scour_maze(y, x-1)
                    else:
                        self._nlist[y][x] = 2
                        self.scour_maze(y-1, x)
            if self._nlist[y][x-1] == 1 or self._nlist[y][x-1] == 5:
                if x != 0:
                    if self._nlist[y][x] == 5 or self._nlist[y][x] == 7:
                        self._nlist[y][x] = 5
                        self.scour_maze(y, x-1)
                    elif self._nlist[y][x] == 9:
                        self._p_eaten += 1
                        self._nlist[y][x] = 2
                        self.scour_maze(y, x-1)
                    else:
                        self._nlist[y][x] = 2
                        self.scour_maze(y, x-1)
            else:
                if self._is_sewer == True:
                    transport = []
                    transport = self.fine_hole(self._hole_y, self._hole_x)
                    if transport[0] != -1:
                        self._hole_y = transport[0]
                        self.hole_x = transport[1]
                        self.scour_maze(transport[0], transport[1])
                return self._nlist
    def fine_hole(self, y, x):
        length = len(self._nlist)
        width = len(self._nlist[0])
        origin_x = x
        origin_y = y
        lid = []
        x += 1
        while y < length:
            while x < width:
                if self._nlist[y][x] == 7:
                    lid.append(y)
                    lid.append(x)
                    return lid
                x += 1
            y += 1
            x = 0
        while y < origin_y:
            while x < origin_x:
                if self._nlist[y][x] == 7:
                    lid.append(y)
                    lid.append(x)
                    return lid
                x += 1
            y += 1
            x = 0
        lid.append(-1)
        lid.append(-1)
        return lid
    def get_maze(self):
        return self._nlist
    def get_eaten(self):
        return self._p_eaten
            
