# Additional Use Cases 2018

# Status

| Use Case          | Implemented |
|-------------------|-------------|
| UC5 Show Forum    | YES         |
| UC6 View Thread   | YES         |
| UC7 Create Thread | YES         |
| UC8 Delete Thread | YES         |
| UC9 Edit Thread   | NO          |
| UC10 Make Post    | YES         |
| UC11 Edit Post    | NO          |
| UC12 Delete Post  | YES         |

# UC5 Show Forum
## Main Scenario
 1. Starts when a user wants to view the forum.
 2. System presents a list of threads.

# UC6 View Thread
## Main Scenario
 1. Starts when a user wants to view a specific thread.
 2. System presents the thread and all related posts.

## Alternate Scenario
 * 2a. Thread does not exist
   1. System presents an error view with information about the issue.
 * 2b. User provided illegal thread identifier
   1. System presents an error view with information about the issue.

# UC7 Create Thread
## Preconditions
 1. User is logged in.

## Main Scenario
 1. Starts when a user wants to create a new thread.
 2. System asks for thread title and body.
 3. User provides the requested information.
 4. System verifies the input, creates a new thread, and returns the user to the main forum view.

## Alternate Scenarios
 * 4a. Input verification fails
   1. System presents an error message containing details of the issue(s).
   2. Go to step 2 in main scenario.

# UC8 Delete Thread
## Preconditions
 1. User is logged in.
 2. User is either Admin or thread creator.

## Main Scenario
 1. Starts when a user wants to delete a thread.
 2. System deletes the thread and returns the user to the main forum view.

## Alternate Scenario
 * 2a. Thread does not exist
   1. System presents an error view with information about the issue.
 * 2b. User provided illegal thread identifier
   1. System presents an error view with information about the issue.

# UC9 Edit Thread
TBA

# UC10 Make Post
## Preconditions
 1. User is logged in
 2. User is viewing a thread

## Main Scenario
 1. Starts when a user wants to create a new post
 2. System asks for post body.
 3. User provides the requested information.
 4. System verifies the input, adds the post to the thread, and refreshes the view.

## Alternate Scenarios
 * 4a. Input verification fails
   1. System presents an error message containing details of the issue(s).
   2. Go to step 2 in main scenario.

# UC11 Edit Post
TBA

# UC12 Delete Post
## Preconditions
 1. User is logged in.
 2. User is viewing a thread.
 3. User is either Admin or thread creator.

## Main Scenario
 1. Starts when a user wants to delete a post.
 2. System deletes the thread and returns the user to the thread view.

## Alternate Scenario
 * 2a. Post does not exist
   1. System presents an error view with information about the issue.
 * 2b. User provided illegal post identifier
   1. System presents an error view with information about the issue.