# Test Cases

| Id  | Requirement | Test Focus                                      | Status  |
|-----|-------------|-------------------------------------------------|---------|
| 5.1 | 5           | Displaying forum when there are no threads      | Success |
| 5.2 | 5           | Displaying the forum when there are threads     | Success |
| 5.3 | 5           | Displaying the forum when the user is logged in | Success |
|     |             |                                                 |         |
|     |             |                                                 |         |
|     |             |                                                 |         |
|     |             |                                                 |         |

### Notices
 * These test cases all assume that all of the setup procedures have been finished successfully.

## Test Case 5.1: Show Forum without any Threads
Navigate to the forum and to view current threads.

### Pre-conditions
 * User is not logged in.
 * There are no stored threads.

### Input
 1. Navigate to site.
 2. Click the link labelled "Go to Forum".
 
### Output
 1. User is taken to the forum.
 2. "Not logged in." is displayed.
 3. A black menu bar containing only "Home" is displayed.
 4. A table containing the columns "Title", "Posts", "Poster", and "Posted at" is displayed.
   4.1. The table has no entries.

### Result
Test was a success.

***

## Test Case 5.2: Show Forum with Threads
Navigate to the forum and to view current threads.

### Pre-conditions
 * User is not logged in.
 * There are stored threads.

### Input
 1. Navigate to site.
 2. Click the link labelled "Go to Forum".
 
### Output
 1. User is taken to the forum.
 2. "Not logged in." is displayed.
 3. A black menu bar containing only "Home" is displayed.
 4. A table containing the columns "Title", "Posts", "Poster", and "Posted at" is displayed.
   4.1. The table is populated with one or more rows containing thread-related information.

### Result
Test was a success.

***

## Test Case 5.3: Show Forum when Logged in
Navigate to the forum and to view current threads.

### Pre-conditions
 * User is logged in.

### Input
 1. Navigate to site.
 2. Click the link labelled "Go to Forum".
 
### Output
 1. User is taken to the forum.
 2. "Logged in as [username] ([user type])" is displayed.
 3. A black menu bar containing "Home" and "New Thread" is displayed.
 4. A table containing the columns "Title", "Posts", "Poster", and "Posted at" is displayed.
   4.1. Optionally, the table is populated with one or more rows containing thread-related information.

### Result
Test was a success.

***