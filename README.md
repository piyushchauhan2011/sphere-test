# Sphere Test

## Live Link
https://guarded-harbor-8917.herokuapp.com/

Please complete the following:

1. Find somewhere online where you can host a PHP application and
database. If you don't have a server to host this on, Heroku's free plan
( https://www.heroku.com/ ) should work.

2. Create a list of 10 random names in a database.

3. In PHP, generate a token of 10 random characters for each person in
the database. This should happen automatically every time a new person
is added to the database.

4. Turn the tokens into links (again automatically for each new token
generated). For example, if the token is abcde12345, it should be
possible to visit the link http://www.YOURHOST.com/sphere/abcde12345

5. On these automatically generated pages, have a small form with the
fields Name and Email. When that form is submitted, a new user should be
added to the database with the information submitted.

6. For that new user that was just created, in the database add a
Friends With column, and add the name of the person whose token was used
when submitting the form. For the user whose token was used, in that
user's Friends With column, the new user's name should be added.

7. When visiting http://www.YOURHOST.com/sphere/, display the list of
all names in the database with their tokens and their friends' names.

## Database Configuration

1. create table people (id serial primary key, name varchar(255) unique not null, token varchar(10));

2. create table users (id serial primary key, name varchar(255) not null, email varchar(255) unique, friends_with varchar(255));

## Repository Link

https://git.heroku.com/guarded-harbor-8917.git

## Running Locally

Make sure you have PHP, Apache and Composer installed.  Also, install the [Heroku Toolbelt](https://toolbelt.heroku.com/).

```sh
$ clone your own fork
$ cd sphere-test
$ composer update
$ foreman start web
```

Your app should now be running on [localhost:5000](http://localhost:5000/).

## Deploying to Heroku

```
$ heroku create
$ git push heroku master
$ heroku open
```

## Documentation

For more information about using PHP on Heroku, see these Dev Center articles:

- [PHP on Heroku](https://devcenter.heroku.com/categories/php)
