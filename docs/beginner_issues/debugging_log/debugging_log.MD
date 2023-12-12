# Debugging Logs

A detailed compilation of debugged issues containing why they caused the bug, how it was solved, if Lawrence was able to solve it himself and if not, why?

# 1 - Creating a SubFolder in MyFiles Bug
Reason - The Auth method in the StoreFolderRequest was overriding the Auth of the ParentIdBaseRequest.

Solution - The Auth method in the StoreFolderRequest was commented out.

Was Lawrence able to solve it himself? - No.

Why was Lawrence unable to solve it? - He lacked the knowledge of how the code base works together. He didn't fully grasp the concept of the Auth methods including when they were used. He had a basic or overview understanding of what they did but it clearly wasn't enough to allow him to navigate his way to solving this issue by himself.

What should Lawrence do? - He's going to make notes about the things taught to him by demby and do his best to practice them in order to consolidate those teachings as to not make the same mistakes again.


# 2 - Wrong variable used in buildFileTree Method Bug
Reason - I was coding... pushing myself while I was already so sleepy and when I woke... I continued without knowing that there was a wrong variable used. I also panicked when I realized that I made another mistake as I did not want to disappoint Demby again.

Solution - I replaced the wrong variable with the right one.

Was Lawrence able to solve it himself? - Yes.

Why was Lawrence able to solve it? - After chatting with Demby... I remembered that most of my problems stem from fear and anxiety and self-doubt. They bind my arms and legs and force me to look at a version of myself that is weak and stupid and incompetent. So what I decided to do was to break the bindings on my arms and legs and just taking it slow and understand each line all the while believing that I can do this.

What should Lawrence do? - He's going to keep using this mindset moving forward. As well as practice the engineering thinking that Demby wants him to have.


# 3 - Folder and File Tree Creation Bug
Reason - To be determined.

Details - When I upload a new folder containing files and sub-folders, the uploaded folder doesn't match the way the folder is mapped on the local machine. I may be wrong but I've figured out that the error is not on the vue end but on the laravel end because I've consulted chatGPT4 with the code for the frontend and everything seems to be ok. However, when I dd the filePaths at the store method in the File Controller, you can see what I mean when I say that there's a mismatch.

Why was Lawrence not able to solve it? - Maybe you'd like to provide an answer for this gaw? and I can add some later

What should Lawrence do? - Same goes for here maybe? or maybe I should give time for the algos on leetcode again. like 1hr leet code a day. What do you think?
