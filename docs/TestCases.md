# Test Cases

| Test Case Id | Use Case Id | Status  |
|--------------|-------------|---------|
| 5.1          | 5           | Success |
| 5.2          | 5           | Success |
| 5.3          | 5           | Success |
| 6.1          | 6           | Success |
| 6.2          | 6           | Success |
| 6.3          | 6           | Success |
| 6.4          | 6           | Success |
| 7.1          | 7           | Success |
| 7.2          | 7           | Success |
| 7.3          | 7           | Success |
| 7.4          | 7           | Success |
| 7.5          | 7           | Success |
| 7.6          | 7           | Success |
| 8.1          | 8           | Success |
| 8.2          | 8           | Success |
| 9.1          | 9           | TBA     |
| 10.1         | 10          | Success |
| 10.2         | 10          | Success |
| 10.3         | 10          | Success |
| 11.1         | 11          | TBA     |
| 12.1         | 12          | Success |
| 12.2         | 12          | Success |
| API 1        | 7           | Success |
| API 2        | 10          | Success |
| API 3        | 7           | Success |
| API 4        | 10          | Success |

### Notices
 * These test cases all assume that all of the setup procedures have been finished successfully.
 * These tests do not fully cover the application (nor do they attempt to).
 * Several complementary unit and api tests would be necessary to even remotely test this properly.
   1. This is because several features rely on specific POST-messages that cannot reliably be replicated in a web browser outside of "intended" methods.
   2. I did however include a very small number of "manual api tests" using the Postman-application. Those are at the bottom of this document in the "API Tests" section.

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

## Test Case 6.1: View a thread while logged out
View a thread.

### Pre-conditions
 * Test Case 5.2 was successful.
 * User is not logged in.
 * The user is currently on the forum page.
 * There is one or more threads currently posted.

### Input
 1. Click on the title of a thread in the list of threads.
 
### Output
 1. User is taken to the thread view.
 2. The thread's title as well as other relevant information (creator, date) is displayed.
 3. Zero or more posts are displayed below. Each containing body text as well as other relevant information (poster, date).

### Result
Test was a success.

***

## Test Case 6.2: View a thread while logged in
View a thread.

### Pre-conditions
 * Test Case 5.3 was successful.
 * User is logged in.
 * The user is currently on the forum page.
 * There is one or more threads currently posted.

### Input
 1. Click on the title of a thread in the list of threads.
 
### Output
 1. User is taken to the thread view.
 2. The thread's title as well as other relevant information (creator, date) is displayed.
 3. Zero or more posts are displayed below. Each containing body text as well as other relevant information (poster, date).
 4. "Respond to thread" is displayed below the final post along with a textarea and a button labelled "Post".

### Result
Test was a success.

***

## Test Case 6.3: View a thread that you made
## Test Case 6.4: View a thread as an admin

## Test Case 7.1: Create a thread when not logged in
append /?forum&thread

## Test Case 7.2: Create a thread when logged in
## Test Case 7.3: Create a thread with too short title
## Test Case 7.4: Create a thread with too long title
## Test Case 7.5: Create a thread with too short body
## Test Case 7.6: Create a thread with too long body

## Test Case 8.1: Delete thread that you made
## Test Case 8.2: Delete thread as an admin

## Test Case 9.1: Edit thread
TBA

## Test Case 10.1: Create a post
## Test Case 10.2: Create a post with too short body
## Test Case 10.3: Create a post with too long body

## Test Case 11.1: Edit post
TBA

## Test Case 12.1 Delete post that you made
## Test Case 12.2 Delete post as an admin


# API Tests
These are a few tests designed to be used with the [Postman](https://www.getpostman.com/) application and assumes basic understanding of their interface.

## API Test 1: Create a thread when not logged in

## API Test 2: Create a post when not logged in

## API Test 3: Delete a thread when not logged in

## API Test 4: Delete a post when not logged in