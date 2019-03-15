I managed to run a SQL injection attack throught the signup form by setting the password to `pass', 'aaa', '', '1'); --`
This creates a user with the password `pass` who is admin. Username is a normal username.
Not sure why I wasn't able to do it through the username field, but the check for if the username is unique crashed when trying.

