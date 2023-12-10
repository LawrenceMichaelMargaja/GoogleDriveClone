# Debugging Logs

A detailed compilation of debugged issues containing why they caused the bug, how it was solved, if Lawrence was able to solve it himself and if not, why?

# 1 - Creating a SubFolder in MyFiles Bug
Reason - The Auth method in the StoreFolderRequest was overriding the Auth of the ParentIdBaseRequest.

Solution - The Auth method in the StoreFolderRequest was commented out.

Was Lawrence able to solve it himself? - No.

Why was Lawrence unable to solve it? - He lacked the knowledge of how the code base works together. He didn't fully grasp the concept of the Auth methods including when they were used. He had a basic or overview understanding of what they did but it clearly wasn't enough to allow him to navigate his way to solving this issue by himself.

What should Lawrence do? - He's going to make notes about the things taught to him by demby and do his best to practice them in order to consolidate those teachings as to not make the same mistakes again.
