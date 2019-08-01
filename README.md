# Development
## Run application

In order to start the application for manual testing, do the following steps:
 
- start the web server by running command:
    -`docker-compose up -d webserver`
- install composer dependencies
    - `docker-compose exec app composer install`
- build DB tables
    - `docker-compose exec app php bin/console doctrine:migrations:migrate`
    - type `y` on prompt
- import fixtures
    - `docker-compose exec app php bin/console doctrine:fixtures:load`
    - type `yes` on prompt
- open in browser:
    - `localhost:8888`

## Stop application

Run command `docker-compose down --remove-orphans`


## Run tests:

**Important**
For convenience reasons, it is necessary to follow the steps in "run application" in order to run the tests

run command `docker-compose up tests```


# Documentation

## Database
![Database tables](./database.jpg)
