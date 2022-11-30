# Function for find sum every char on the string
def sumCharOnTheStr(char, string):
    count = 0
    for i in range(len(string)):
        if char == string[i]:
            count = count + 1
        else:
            count = count;
    
    return count

# Function main, find text anagram
def DetectAnagram(inputan1,inputan2):
    if len(inputan1) == len(inputan2):
        for i in range(len(inputan1)):
            if sumCharOnTheStr(inputan1[i].lower(),inputan1.lower()) != sumCharOnTheStr(inputan1[i].lower(),inputan2.lower()):
                count = 1
                break
            else:
                count = 0
        if count == 1:
            print("ini bukan anagram")
        else:
            print("ini adalah anagram")
            
    else:
        print("ini bukan anagram")

# Main
DetectAnagram("kelapA","kepaLa")

