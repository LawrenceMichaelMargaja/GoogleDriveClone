# Architecture Log

Here are relevant architecture information that _you must know_ in order to successfully work with this application:

- **You gotta create a new user from APP_URL/register**
  - then you **must confirm** its registration via the dummy email provider: MailPit
  - After that, actual creation occurs, and it creates a **new entry as well in the files table**. And that new creation serves as the **root** entry.
    - Expanding on the previous point, one of our old attempts to fix this was to make a seeder entry for the test user, because we saw that in the official codebase
    - But one of the problems with that is, it is misleading - as it doesn't communicate **the need** for us to create a brand new user, because the creation method **also creates the associated root file**.
    - So we really \*gotta\* remember that ^^
