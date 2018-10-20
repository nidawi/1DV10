# Setup
These are some general-purpose guidelines on how to get this project running on your own machine of choice.

# Project Dependencies
This project was developed to be hosted on a system running Ubuntu 16.04 with the following packages:
* PHP, version 7.0.32.
* Zend Engine, version 3.0.0.
* mysql, version 14.14 (distrib 5.7.23).

# Installation Instructions
1. Verify that you have the required dependencies as listed in the section above. Please note that this application uses several features that are unavailable to PHP versions below 7.0.
   * Assuming that you are running Ubuntu 16.04, the following commands should help get you started:
     * "sudo apt-get install mysql-server" - Downloads and installs Mysql on your server.
     * "mysql_secure_installation" - Helps you set up your Mysql database.
     * "sudo apt-get install php-fpm php-mysql" - Downloads and installs the PHP processor "fastCGI process manager" and support for connecting PHP to the Mysql database using Mysqli.
   * You can refer to [this guide](https://www.digitalocean.com/community/tutorials/how-to-install-linux-nginx-mysql-php-lemp-stack-in-ubuntu-16-04) for more information (especially useful if you use Digitalocean as your host of choice).
2. Clone the repo / copy the files to the desired folder on the machine/webserver of your choice.
   * You may have to employ OS-appropriate techniques to claim ownership of the downloaded files.
3. Install and set up persistence of your choice. The application is designed to work "out of the box" being given a Mysql database and a user with "all privileges" on the designated database. If you choose not to use Mysql, you will have to make extensive changes to the source code of the application. Please refer to the "Database" section for more information about how to structure your Mysql database and "Custom Persistence" section for more information on how you can implement your own solution.
4. Create your Environment (_ENV.php_) file and provide it with the relevant information (see the "Environment Variables" section for more information).
5. You will most likely have to restart your machine.

The application should now be functional.

# Environment Variables
The project has an environment variable file (_ENV.php_) which has been added to the project's .gitignore file. It's important to keep these settings away from prying eyes.  
You will have to create your own version of this file if you wish to run the project without performing extensive changes.
Below are all the values present in the environment variables file, their types, and what they represent. Note that all
of these are supposed to be constants. Also note that if you choose a different method of persistence, the database-related
variables will, naturally, not be required.

| Environment Variable       | Type   | Description                                                                                |
|----------------------------|--------|--------------------------------------------------------------------------------------------|
| APPLICATION_STATUS         | string | The status of the application, such as "development" and "production".                     |
| SESSION_ID                 | string | The Id to use for the Session's unique handle.                                             |
| SESSION_CURRENT_USER_ID    | string | The Id to use for the Session entry where the currently logged in user will be stored.     |
| SESSION_DISPLAY_MESSAGE_ID | string | The Id to use for the Session entry where the current display message will be stored.      |
| SESSION_LOCALS_ID          | string | The Id to use for the Session entry where the application's "local values" will be stored. |
| COOKIE_ENCRYPTION_KEY      | string | The encryption key to use for cookie encryption/decryption.                                |
| DEFAULT_TIME_ZONE          | string | The time zone to use for the application. Default is "Europe/Stockholm".                   |
| DATABASE_ADDRESS           | string | The address of the database to use.                                                        |
| DATABASE_USER              | string | The name of the database user to use.                                                      |
| DATABASE_PASSWORD          | string | The password for the database user.                                                        |
| DATABASE_DB                | string | The name of the database to use.                                                           |

# Database
By default, the project uses a MySQL database for persistence. If you wish to use this project with little to no modification, you will need to recreate this database structure in a MySQL database of your choice and store the relevant details in the Environment file (see section above). The tables and keys used can be seen in the diagram below:
![Database diagram](https://1dv610.nidawi.me/login/docs/dbdiagram.png)

# Custom Persistence
This won't be an intricate description of how to implement your own persistence solution but rather a few tips on what you have to change in the source code in order to make your own implemention work. The following files depend on persistent storage (_lib/Database.php_):

1. _model/AccountRegister.php_
2. _model/TemporaryPasswordRegister.php_
3. _model/Forum.php_

These three will have to be replaced / heavily changed. To aid with this, _AccountRegister_ implements the interfaces _AccountRegisterDAO_ (full access) and _AccountInfo_ (limited read-only access). _Forum_ implements the interface _ForumDAO_ (full access). _TemporaryPasswordRegister_ is used by the _AccountRegister_ and can be safely replaced with your own Temporary Password solution (or, in theory, omitted completely).

By implementing the provided interfaces in your solution, you should be able simply to provide your classes in the respective constructors in _index.php_ and they should work out-of-the-box by their interface handles.