# Conversion Tracking Model

Goal is to implement a conversion tracking model for advertisers that weights various points of contacts with their customers.

### The project

The project has been developed using:

- PHP7.2 
- MySQL 8
- Symfony Framework

To be able to run the application please make sure you have the following software/s on your machine:

- Docker and Docker Compose
- Composer package manager

The **Makefile** will make it easy for you to go through the whole scenarios. 
	  
### How to run the application

- Clone the project to your machine ```git clone git@github.com:engmohammedyehia/interview-task.git myhotelshop```
- Browser to the **myhotelshop** folder ```cd myhotelshop```
- Install project dependencies and run the applications by typing the following command ```make install run```
- When The installation completes you can run the unit tests using the command ```make unit-test```
- To be able to run the API testing run the command ```make api-test```
- You can also use Postman by importing the collection included with this folder but make sure to add a cookie to be able to call the /track endpoint with a value like so ```tracking={"placements": [{"platform": 1, "customer_id": 2, "date_of_contact": "2018-01-01 14:00:00"}, {"platform": 2, "customer_id": 2, "date_of_contact": "2018-01-03 14:00:00"}, {"platform": 3, "customer_id": 2, "date_of_contact": "2018-01-05 14:00:00"}]}```
- If you want to clean the project and start over you just have to run ```make clean install run```
- To view the API documentation please run the command ```make api-docs``` or browser to http://localhost:8088/docs/
- For more command please type ```make``` or ```make help```