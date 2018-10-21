# General Purpose Checklist

1. Prevent XSS in forum posts and titles - DONE (achieved by encoding output rather than stripping tags - users may want to post code snippets without being malicious)
2. Prevent XSS is user names - DONE (Username model class verifies the name has no tags or special characters)
3. Prevent CSRF by using csrf-tokens - NOT DONE (time commitment too crucial at this time)
4. Implement at least view, create, delete thread/post. Edit if enough time. - DONE (no edit)