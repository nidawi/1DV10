# General Purpose Checklist

1. Prevent XSS in forum posts and titles - DONE (achieved by encoding output rather than stripping tags - users may want to post code snippets without being malicious)
2. Prevent XSS is user names - DONE (Username model class verifies the name has no tags or special characters)
3. Prevent CSRF by using csrf-tokens - NOT DONE (time commitment too crucial at this time)
4. Implement at least view, create, delete thread/post. Edit if enough time. - DONE (no edit)
5. Add a small javascript file that disables buttons after being clicked to prevent accidentally posting twice etc. - NOT DONE

# Assignment 4 Checklist
Mental checklist of Assignment 4 requirements.

## Code is Object Oriented.
* Code in classes : _check (I think)_
* Classes have dependencies : _check (I think)_
* Avoids using an array when something should be a class : _check_
* Code uses type-safety when possible : _check_
* Dependencies are encapsulated in a single class : _check_
* Databases, files, super-global arrays($_GET, $_SESSION...) etc : _check_
* No string dependencies (especially avoid on superglobal arrays) : _check_
* Code has an architecture eg. MVC : _check_
* Low coupling : _check, to some extent_
* Information Expert : _check, as well as I could_
* Law of Demeter : _check, but with exceptions_

## Code is Clean.
* Self explanatory code and well commented : _check (comments may be lacking)_
* Code is readable, no commented out code, indentation on GitHub looks good : _check (github indention has a mind of its own)_
* Meaningful names : _check_
* Errors are handled well (Validation, Exceptions) : _check (I think)_
* High and low abstraction levels are separated : _check (I think)_
* Read like a newspaper : _check (constructor -> public -> private)_
* Little code duplication : _check_

## Project is represented well
* Status: What is implemented, or not? : _check (UseCases.md)_
* Describe how to test? : _check (TestCases.md)_
* Describe how to install? : _check (Setup.md)_
* No passwords in git! : _check_
* There is a git history : _check (sadly I had a little accident and lost most of it)_
* Code is readable, no commented out code, indentation on GitHub looks good: _check (read above)_