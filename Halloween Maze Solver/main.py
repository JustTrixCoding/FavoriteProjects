from executive import Executive
import os.path #checks if file exist in folder
def main():
    file_name = input("Enter a file name: ")
    if os.path.exists(file_name) == False:
        raise RuntimeError("File not found")
    process_file = Executive(file_name)
    process_file.run()
main()
