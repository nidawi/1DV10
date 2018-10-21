# Test Cases

## Automatic API Tests
![Database diagram](https://1dv610.nidawi.me/login/docs/scoreonassignment.png)

## Manual Test Cases
All of the manual tests provided [here](https://github.com/dntoll/1dv610/blob/master/assignments/A2_resources/TestCases.md) have been performed. This includes Test Case 3.5 which wasn't included in the Automatic Tests. Most tests were also executed several times for the sake of reliance.

## Additional Test Cases Status
A few additional tests were introduced to deal with the new Use Cases after the implementation of the forum. Below is a list of all the new Test Cases, which Use Case they test, as well as their status.

| Test Case Id | Use Case Id | Status  |
|--------------|-------------|---------|
| 5.1          | 5           | Success |
| 5.2          | 5           | Success |
| 5.3          | 5           | Success |
| 5.4          | 5           | Success |
| 5.5          | 5           | Success |
| 6.1          | 6           | Success |
| 6.2          | 6           | Success |
| 6.3          | 6           | Success |
| 6.4          | 6           | Success |
| 6.5          | 6           | Success |
| 6.6          | 6           | Success |
| 7.1          | 7           | Success |
| 7.2          | 7           | Success |
| 7.3          | 7           | Success |
| 7.4          | 7           | Success |
| 7.5          | 7           | Success |
| 7.6          | 7           | Success |
| 7.7          | 7           | Success |
| 8.1          | 8           | Success |
| 8.2          | 8           | Success |
| 9.1          | 9           | -       |
| 10.1         | 10          | Success |
| 10.2         | 10          | Success |
| 10.3         | 10          | Success |
| 11.1         | 11          | -       |
| 12.1         | 12          | Success |
| 12.2         | 12          | Success |
| 13.1         | 13          | Success |
| 13.2         | 13          | Success |
| 13.3         | 13          | Success |
| API 1        | 7           | Success |
| API 2        | 8           | Success |
| API 3        | 8           | Success |
| API 4        | 10          | Success |
| API 5        | 12          | Success |
| API 6        | 12          | Success |

### Notices
 * These test cases all assume that all of the setup procedures have been finished successfully.
 * These tests do not fully cover the application (nor do they attempt to).
 * Several complementary unit and api tests would be necessary to even remotely test this properly.
   1. This is because several features rely on specific POST-messages that cannot reliably be replicated in a web browser outside of "intended" methods.
   2. I did however include a very small number of "manual api tests" using the Postman-application. Those are at the bottom of this document in the "API Tests" section.
 * Some tests inherit the output of the test that they include in their Input-section.

## Test Case 5.1: Navigate to Forum
Normal navigation to the page. The forum is shown.

### Input
 1. Navigate to site.
 2. Click the link labelled "Go to Forum".

### Output
 1. User is taken to the forum.
 2. A table containing the columns "Title", "Posts", "Poster", and "Posted at" is displayed.
     *  The table is populated with zero or more rows containing thread-related information in accordance to the columms.

### Result
Test was a success.

***

## Test Case 5.2: Show Forum when Logged out
Navigate to the forum.

### Pre-conditions
 * User is not logged in.

### Input
 1. Test Case 5.1.
 
### Output
 1. "Not logged in." is displayed.
 2. A black menu bar containing only "Home" is displayed.
 3. A table containing the columns "Title", "Posts", "Poster", and "Posted at" is displayed.
    * Optionally, the table is populated with one or more rows containing thread-related information.

### Result
Test was a success.

***

## Test Case 5.3: Show Forum when Logged in
Navigate to the forum.

### Pre-conditions
 * User is logged in.

### Input
 1. Test Case 5.1.
 
### Output
 1. "Logged in as [username] ([user type])" is displayed.
 2. A black menu bar containing "Home" and "New Thread" is displayed.
 3. A table containing the columns "Title", "Posts", "Poster", and "Posted at" is displayed.
    * Optionally, the table is populated with one or more rows containing thread-related information.

### Result
Test was a success.

***

## Test Case 5.4: Show Forum without any Threads
Navigate to the forum and view current threads.

### Pre-conditions
 * There are no stored threads.

### Input
 1. Test Case 5.1.
 
### Output
 1. The table has no entries.

### Result
Test was a success.

***

## Test Case 5.5: Show Forum with Threads
Navigate to the forum and view current threads.

### Pre-conditions
 * There are one or more stored threads.

### Input
 1. Test Case 5.1.
 
### Output
 1. The table is populated with zero or more rows containing thread-related information in accordance to the columms.

### Result
Test was a success.

***

## Test Case 6.1: View a Thread
View a thread.

### Pre-conditions
 * There are one or more stored threads.

### Input
 1. Test Case 5.5.
 2. Click on the title of a thread in the list of threads.
 
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
 * User is logged in.
 * There are one or more stored threads.

### Input
 1. Test Case 6.1.
 
### Output
 1. "Respond to thread" is displayed.
 2. A form for creating a post is displayed.

### Result
Test was a success.

***

## Test Case 6.3: View a thread that you made
View a thread.

### Pre-conditions
 * User is logged in.
 * There is at least one thread the user has posted.

### Input
 1. Test Case 6.2.
 
### Output
 1. Two buttons, "Edit" followed by "Delete" are displayed next to the opening post.

### Result
Test was a success.

***

## Test Case 6.4: View a thread as an admin
View a thread.

### Pre-conditions
 * User is logged in as an admin.
 * There is at least one thread the user has not posted.

### Input
 1. Test Case 6.2.
 
### Output
 1. Two buttons, "Edit" followed by "Delete" are displayed next to the opening post.

### Result
Test was a success.

***

## Test Case 6.5: View a thread that doesn't exist
View a thread.

### Input
 1. Navigate to site.
 2. Append "?forum&thread=847368034764380673408673" to the url in your browser.
 3. Press enter.
 
### Output
 1. User is taken to the forum.
 2. The text "Not Logged In" is displayed.
 3. The text "Error: 404" is displayed, followed by "Not Found".

### Result
Test was a success.

***

## Test Case 6.6: View a thread using an invalid identifier
View a thread.

### Input
 1. Navigate to site.
 2. Append "?forum&thread=0" to the url in your browser.
 3. Press enter.
 
### Output
 1. User is taken to the forum.
 2. The text "Not Logged In" is displayed.
 3. The text "Error: 400" is displayed, followed by "Bad Request".

### Result
Test was a success.

***

## Test Case 7.1: Navigate to "New Thread"
Post a thread.

### Pre-conditions
 * User is logged in.

### Input
 1. Test Case 5.3.
 2. Click the button labelled "New Thread".
 
### Output
 1. User is taken to the new thread view.
 2. "Create a new thread" header is displayed.
 3. A form for creating a thread is displayed.

### Result
Test was a success.

***

## Test Case 7.2: Create a thread when not logged in
Post a thread.

### Pre-conditions
 * User is not logged in.

### Input
 1. Navigate to site.
 2. Append "?forum&thread" to the url in your browser.
 3. Press enter.
 
### Output
 1. User is taken to the forum.
 2. The text "Not Logged In" is displayed.
 3. The text "Error: 401" is displayed, followed by "Unauthorized".

### Result
Test was a success.

***

## Test Case 7.3: Create a thread when logged in
Post a thread.

### Pre-conditions
 * User is logged in.

### Input
 1. Test Case 7.1.
 2. Enter "test title" into the box labelled "Title".
 3. Enter "test body" into the box labelled "Body".
 4. Click the button labelled "Create".
 
### Output
 1. User is taken to the main forum view.
 2. The message "Thread posted." is displayed.
 3. The new thread is displayed in the list of threads.

### Result
Test was a success.

***

## Test Case 7.4: Create a thread with too short title
Post a thread.

### Pre-conditions
 * User is logged in.

### Input
 1. Test Case 7.1.
 2. Enter "t" into the box labelled "Title".
 3. Enter "test body" into the box labelled "Body".
 4. Click the button labelled "Create".
 
### Output
 1. The message "Thread title is too short. Minimum 2 characters." is displayed.
 2. The box labelled "Title" contains "t".
 3. The box labelled "Body" contains "test body".

### Result
Test was a success.

***

## Test Case 7.5: Create a thread with too long title
Post a thread.

### Pre-conditions
 * User is logged in.

### Input
 1. Test Case 7.1.
 2. Enter the first paragraph of Lorem Ipsum into the box labelled "Title".
 3. Enter "test body" into the box labelled "Body".
 4. Click the button labelled "Create".
 
### Output
 1. The message "Thread title is too long. Maximum 100 characters." is displayed.
 2. The box labelled "Title" contains the first paragraph of Lorem Ipsum.
 3. The box labelled "Body" contains "test body".

### Result
Test was a success.

***

## Test Case 7.6: Create a thread with too short body
Post a thread.

### Pre-conditions
 * User is logged in.

### Input
 1. Test Case 7.1.
 2. Enter "test title" into the box labelled "Title".
 3. Enter "t" into the box labelled "Body".
 4. Click the button labelled "Create".
 
### Output
 1. The message "Thread body is too short. Minimum 2 characters." is displayed.
 2. The box labelled "Title" contains "test title".
 3. The box labelled "Body" contains "t".

### Result
Test was a success.

***

## Test Case 7.7: Create a thread with too long body
Post a thread.

### Pre-conditions
 * User is logged in.

### Input
 1. Test Case 7.1.
 2. Enter "test title" into the box labelled "Title".
 3. Paste the entirety of Lorem Ipsum into the box labelled "Body".
 4. Click the button labelled "Create".
 
### Output
 1. The message "Thread body is too long. Maximum 2000 characters." is displayed.
 2. The box labelled "Title" contains "test title".
 3. The box labelled "Body" contains the entirety of Lorem Ipsum.

### Result
Test was a success.

***

## Test Case 8.1: Delete thread that you made
Delete a thread.

### Pre-conditions
 * User is logged in.
 * There is at least one thread the user has posted.

### Input
 1. Test Case 6.3.
 2. Click the button labelled "Delete".
 
### Output
 1. User is taken to the main forum view.
 2. The message "Thread deleted." is displayed.
 3. The thread is no longer displayed in the list of threads.

### Result
Test was a success.

***

## Test Case 8.2: Delete thread as an admin
Delete a thread.

### Pre-conditions
 * User is logged in.
 * There is at least one thread the user has not posted.

### Input
 1. Test Case 6.4.
 2. Click the button labelled "Delete".
 
### Output
 1. User is taken to the main forum view.
 2. The message "Thread deleted." is displayed.
 3. The thread is no longer displayed in the list of threads.

### Result
Test was a success.

***

## Test Case 9.1: Edit thread
TBA

## Test Case 10.1: Create a post
Create a post.

### Pre-conditions
 * User is logged in.
 * There are one or more stored threads.

### Input
 1. Test Case 6.2.
 2. Enter "test body" into the textarea.
 3. Click the button labelled "Post".
 
### Output
 1. The message "Posted new message." is displayed.
 2. The new post is displayed in the list of posts.

### Result
Test was a success.

***

## Test Case 10.2: Create a post with too short body
Create a post.

### Pre-conditions
 * User is logged in.
 * There are one or more stored threads.

### Input
 1. Test Case 6.2.
 2. Enter "t" into the textarea.
 3. Click the button labelled "Post".
 
### Output
 1. The message "Post is too short. Minimum 2 characters." is displayed.
 2. The textarea contains the text "t".

### Result
Test was a success.

***

## Test Case 10.3: Create a post with too long body
Create a post.

### Pre-conditions
 * User is logged in.
 * There are one or more stored threads.

### Input
 1. Test Case 6.2.
 2. Enter the entirety of Lorem Ipsum into the textarea.
 3. Click the button labelled "Post".
 
### Output
 1. The message "Post is too long. Maximum 2000 characters." is displayed.
 2. The textarea contains the entirety of Lorem Ipsum.

### Result
Test was a success.

***

## Test Case 11.1: Edit post
TBA

## Test Case 12.1 Delete post that you made
Delete a post.

### Pre-conditions
 * User is logged in.
 * There are one or more stored threads.
 * The user has made at least one post in the selected thread.

### Input
 1. Test Case 6.2.
 2. Click the button labelled "Delete" next to the post of choice.
 
### Output
 1. The message "Post deleted." is displayed.
 2. The deleted post is no longer displayed in the list of posts.

### Result
Test was a success.

***

## Test Case 12.2 Delete post as an admin
Delete a post.

### Pre-conditions
 * User is logged in as an admin.
 * There are one or more stored threads.
 * There is at least one post in the selected thread.

### Input
 1. Test Case 6.4.
 2. Click the button labelled "Delete" next to the post of choice.
 
### Output
 1. The message "Post deleted." is displayed.
 2. The deleted post is no longer displayed in the list of posts.

### Result
Test was a success.

***

## Test Case 13.1: View a Post
View a thread.

### Pre-conditions
 * There are one or more stored posts.

### Input
 1. Navigate to site.
 2. Append "?forum&thread=id_of_post" to the url in your browser.
    * Replace "id_of_post" with the post you wish to view.
 3. Press enter.
 
### Output
 1. User is taken to the post view.
 2. The post's body as well as other relevant information (creator, date) is displayed.

### Alternative Scenario
 2. a. The post is a thread body
    1. Only the post body is displayed.

### Result
Test was a success.

***

## Test Case 13.2: View a post that doesn't exist
View a post.

### Input
 1. Navigate to site.
 2. Append "?forum&post=847368034764380673408673" to the url in your browser.
 3. Press enter.
 
### Output
 1. User is taken to the forum.
 2. The text "Not Logged In" is displayed.
 3. The text "Error: 404" is displayed, followed by "Not Found".

### Result
Test was a success.

***

## Test Case 13.3: View a post using an invalid identifier
View a post.

### Input
 1. Navigate to site.
 2. Append "?forum&post=0" to the url in your browser.
 3. Press enter.
 
### Output
 1. User is taken to the forum.
 2. The text "Not Logged In" is displayed.
 3. The text "Error: 400" is displayed, followed by "Bad Request".

### Result
Test was a success.

***

# API Tests
These are a few tests designed to be used with the [Postman](https://www.getpostman.com/) application and assumes basic understanding of their interface.

## API Test 1: Create a thread when not logged in
Create a thread despite the "Create Thread" interface options not being present.

### Pre-condition
 * There is no user logged in.

### Input
 1. Start a new Postman session and select "POST" in the method drop-down menu.
 2. Enter the address the application is hosted at into the address bar.
 3. Append "?forum&thread".
 4. Select "x-www-form-urlencoded" in the request Body-section.
 5. In the Body-section, add the key "NewThreadView::NewThread" without a value.
 6. In the Body-section, add the key "NewThreadView::Title" with the value "test title".
 7. In the Body-section, add the key "NewThreadView::Body" with the value "test body".
 8. Click the button labelled "Send".

![Database diagram](https://1dv610.nidawi.me/login/docs/APITest1-Input.png)

### Output
 1. A cookie named "PHPSESSID" is returned.
 2. The status "401 Unauthorized" is returned.
 3. In the "Preview" section of the result, the following is shown:
    * "Not logged in"
    * "Error: 401"
    * "Unauthorized"

### Result
Test was a success.

***

## API Test 2: Delete a thread when not logged in
Delete a thread despite the "Delete Thread" interface options not being present.

### Pre-condition
 * There is no user logged in.
 * There are one or more stored threads.

### Input
 1. Start a new Postman session and select "POST" in the method drop-down menu.
 2. Enter the address the application is hosted at into the address bar.
 3. Append "?forum&thread=id_of_a_thread".
    * Replace id_of_a_thread with a thread of your choice.
 4. Select "x-www-form-urlencoded" in the request Body-section.
5. In the Body-section, add the key "NewThreadView::NewThread" without a value.
6. Click the button labelled "Send".

### Output
 1. A cookie named "PHPSESSID" is returned.
 2. The status "401 Unauthorized" is returned.
 3. In the "Preview" section of the result, the following is shown:
    * "Not logged in"
    * "Error: 401"
    * "Unauthorized"

### Result
Test was a success.

***

## API Test 3: Delete a thread you did not create (and you're not an admin)
Delete a thread that you are not entitled to edit.

### Pre-condition
 * A user has been logged in through Postman.
 * There are one or more stored threads that the user has not created.

### Input
 1. Start a new Postman session and select "POST" in the method drop-down menu.
 2. Enter the address the application is hosted at into the address bar.
 3. Append "?forum&thread=id_of_a_thread".
    * Replace id_of_a_thread with a thread of your choice (that the user didn't create).
 4. Select "x-www-form-urlencoded" in the request Body-section.
 5. In the Body-section, add the key "ThreadView::DeleteThread" without a value.
 6. Click the button labelled "Send".

### Output
 1. A cookie named "PHPSESSID" is returned.
 2. The status "403 Forbidden" is returned.
 3. In the "Preview" section of the result, the following is shown:
    * "Logged in"
    * "Error: 403"
    * "Forbidden"

### Result
Test was a success.

***

## API Test 4: Create a post when not logged in
Create a post despite the "Create Post" interface options not being present.

### Pre-condition
 * There is no user logged in.
 * There are one or more stored threads.

### Input
 1. Start a new Postman session and select "POST" in the method drop-down menu.
 2. Enter the address the application is hosted at into the address bar.
 3. Append "?forum&thread=id_of_a_thread".
    * Replace id_of_a_thread with a thread of your choice.
 4. Select "x-www-form-urlencoded" in the request Body-section.
 5. In the Body-section, add the key "NewPostView::NewPost" without a value.
 6. In the Body-section, add the key "NewPostView::PostBody" with the value "test body".
 7. Click the button labelled "Send".

### Output
 1. A cookie named "PHPSESSID" is returned.
 2. The status "401 Unauthorized" is returned.
 3. In the "Preview" section of the result, the following is shown:
    * "Not logged in"
    * "Error: 401"
    * "Unauthorized"

### Result
Test was a success.

***

## API Test 5: Delete a post when not logged in
Delete a post despite the "Delete Post" interface options not being present.

### Pre-condition
 * There is no user logged in.
 * There are one or more stored threads.
 * There is at least one post in the selected thread.

### Input
 1. Start a new Postman session and select "POST" in the method drop-down menu.
 2. Enter the address the application is hosted at into the address bar.
 3. Append "?forum&post=id_of_a_post".
    * Replace id_of_a_post with a post of your choice.
 4. Select "x-www-form-urlencoded" in the request Body-section.
 5. In the Body-section, add the key "PostView::DeletePost" without a value.
 6. Click the button labelled "Send".

### Output
 1. A cookie named "PHPSESSID" is returned.
 2. The status "401 Unauthorized" is returned.
 3. In the "Preview" section of the result, the following is shown:
    * "Not logged in"
    * "Error: 401"
    * "Unauthorized"

### Result
Test was a success.

***

## API Test 6: Delete a post you did not create (and you're not an admin)
Delete a post that you are not entitled to edit.

### Pre-condition
 * A user has been logged in through Postman.
 * There are one or more stored threads.
 * There is at least one post in the selected thread that the user hasn't made.

### Input
 1. Start a new Postman session and select "POST" in the method drop-down menu.
 2. Enter the address the application is hosted at into the address bar.
 3. Append "?forum&post=id_of_a_post".
    * Replace id_of_a_post with a post of your choice (that the user didn't create).
 4. Select "x-www-form-urlencoded" in the request Body-section.
 5. In the Body-section, add the key "PostView::DeletePost" without a value.
 6. Click the button labelled "Send".

### Output
 1. A cookie named "PHPSESSID" is returned.
 2. The status "403 Forbidden" is returned.
 3. In the "Preview" section of the result, the following is shown:
    * "Logged in"
    * "Error: 403"
    * "Forbidden"

### Result
Test was a success.

***