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