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


## Run automated tests:

**Important**
For convenience reasons, it is necessary to follow the steps in "run application" in order to run the tests

run command `docker-compose up tests```


# Test manually

## Add a new conversions

- call GET /conversions/new with valid parameters to create a new conversion
- call GET /conversions and find the conversion in the response
- the conversion will have a link to the revenue distributions
- use that link to fetch the distribution resources
- distribution resource will provide a link to get total revenue distributed by platform
- call that link to fetch the information

## Get total conversions by platform

- call GET /conversions?platform={platform} (use test_tripadvisor for example)

## GET most attractive platforms
`//ToDo`


# Documentation

## Database
![Database tables](./database.jpg)
