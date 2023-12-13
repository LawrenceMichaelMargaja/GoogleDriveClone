## Installation instructions

Most of these instructions are written with the context of using a MAC,
sorry _Windows_ users.

### Prerequisites
1. Make sure your APP_URL is in sync with your /etc/hosts

![img.png](img.png)

How to start on your local machine
1. ./vendor/bin/sail up
2. execute **php artisan migrate:refresh --seed** in the _containerized environment_
3. execute **composer install** in the _containerized environment_
4. execute **npm install** in the _containerized environment_
 5. execute **npm run dev** in the _containerized environment or the regular terminal_
6. to login, goto APP_URL/login, and type your login credentials (the email is in your seeder and the password is in the UserFactory)
