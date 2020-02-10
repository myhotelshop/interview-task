# Conversion Tracking Model

Goal is to implement a conversion tracking model for advertisers that weights various points of contacts with their customers.

### The project

The project has been developed using:

- Laravel Framework

To be able to run the application please make sure you have the following software/s on your machine:

- Docker and Docker Compose
- Composer package manager
 
	  
### How to run the application

- Clone the project to your machine ```https://github.com/ahmedswar68/interview-task.git```
- Run ```cd interview-task```
- Create .env file and copy .env.example content into it 
- Install project dependencies and run the applications by typing the following command ```docker-compose build && docker-compose up -d```
- Then Run these commands ``` docker-compose exec app bash ```
- ``` composer install ```
- ``` php artisan migrate --seed ```
- When The installation completes you can run the unit tests using the command ```php vendor/phpunit/phpunit/phpunit```
- You can also use Postman by importing the collection included with this folder but make sure to add a cookie with name ```mhs_tracking``` to be able to call the /distribute-revenue endpoint 
    with a value like so ```{"placements": [{"platform": "trivago", "customer_id": 123, "date_of_contact": "2018-01-01 14:00:00"}, {"platform": "tripadvisor", "customer_id": 123, "date_of_contact": "2018-01-03 14:00:00"}, {"platform": "kayak", "customer_id": 123, "date_of_contact": "2018-01-05 14:00:00"}]}```
- Postman collection file ```MyHotelShopTask.postman_collection.json``` is attached with projects files
- Also you can find collection online  follow this url ``` https://www.getpostman.com/collections/cfb84b272b9c725999d0 ```
- You can find API Documentation in this url ```https://documenter.getpostman.com/view/1337651/SWTHbvG7?version=latest```
