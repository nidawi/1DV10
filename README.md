# Assignment 4 - 1DV610

## Comment about the commit history
Unfortunately, after having finished yet another one of my 18+ hour work sessions at 7 am I accidentally managed to push some things that shouldn't be on git.
Since git is... not the best service in the world, you cannot just casually walk in a fix an issue but instead you have to nuke the whole god damned branch to fix
one little issue. So instead of my nice 10+ commit history, you will be stuck with this... much smaller one and with no recollection of how this came to be.
I am sorry about that.

* [Project Checklist](https://github.com/nidawi/1DV610-login/blob/master/docs/Checklist.md)
* [Project Additonal Requirements / Use Cases](https://github.com/nidawi/1DV610-login/blob/master/docs/UseCases.md)
* [Project Setup Instructions](https://github.com/nidawi/1DV610-login/blob/master/docs/Setup.md)
* [Project Additional Test Cases](https://github.com/nidawi/1DV610-login/blob/master/docs/TestCases.md)

## $_SESSION in the view?!
I feel that I need to make a bit of a comment here. I am aware of that you want us to use $_SESSION only inside the model.
However, since I use the PRG-pattern to prevent duplicate POST requests, etc., I needed a way to store display messages and "pre-filled" values for input boxes, etc.
Because of this, my views depend on the session (lib/SessionStorage.php) for storing what I call "locals". These "locals" are the same as Node's Express' "locals" which are essentially
values that are stored in the session in order to be preserved after a redirect. The use of the session has been abstracted in view/ViewTemplate.php. This is motivated by the fact that the model, to my understanding, has absolutely nothing
to do with the view being able to store values such as this. All "magic indices" are stored in the environments file to prevent hidden dependencies.

## HTML on Github?!
I have noticed that while Github formats PHP just fine, it does not do quite as good of a job when it comes it HTML. The HTML text sometimes gets tossed around and horrifically disfigured. I have not found a solution for this issue and I hope it won't be too much of an issue. Update: apparently comments are messed up too.

## What's a "ViewTemplate" anyway?
Simply put, the ViewTemplate.php file represents a base class that all other views implement (they don't have to). It contains various helper functions (such as verifying request headers, etc.) and manages the locals used by that particular view (by being given a reference to the SessionStorage). This was done to reduce code duplication and eliminate string dependencies. Coupling should remain roughly the same, as in most cases the coupling gained from extending this base class would have been replaced by some kind of navigation view keeping track of the same values instead.

## Class Diagram
I threw together a class diagram, mostly to help myself keep track of what was going on as the codebase grew. I didn't exactly put in a lot of time in it, but I figured it might help as a basic idea so I will include it anyway. It only covers model/controller/lib interactions and does not deal with views. It's probably not entirely accurate but (!) it has made me realize that the coupling is actually a bit of a mess which I should look into. Sadly I am out of time for this assignment so... best of luck to me.
![Class diagram](https://1dv610.nidawi.me/login/docs/classdiagram.jpg)