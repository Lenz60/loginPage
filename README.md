# CodeIgniter 4 Login Page Application

## What is this?

This is a simple functional login page that uses Json Web Token.
this is also built in smtp gmail too.

## What are the features ? Whats in it ?
### The UI/UX
1. The login page built with [Tailwindcss](https://tailwindcss.com/), it uses [laravel-mix](https://laravel-mix.com/) for the integration 
2. The dashboard is built with [SBadmin2](https://startbootstrap.com/theme/sb-admin-2) 
### The Backend
1. It built in with JWT or Json Web Token for the login and Cookie
2. It built with Gmail SMTP to send account activation confrimation and forgot password **make sure to check the spam folder in your mail** because gmail treat the SMTP as dangerous email even though it's their SMTP
3. Salted Passwords! the password is salted before it get encoded, the salt is declared in `.env` file
4. Upload avatar or profile pictures when registering new account

## Installation

1. Rename the `env` file to `.env`
2. Update the composer packages by running `composer update` in the console inside of directory of the project
3. Update the npm library by running `npm update` in the console inside of directory of the project
4. Run `npx mix` to ensure tailwindcss do its work!
5. Run the development server via `php spark serve`
6. To migrate the databases, run `php spark migrate` or `php spark migrate:refresh`

Thats all, Thankyou
