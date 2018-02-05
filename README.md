# Conversion Tracking Model

Goal is to implement a conversion tracking model for advertisers that weights various points of contacts with their customers.

## Task Description

Fork this project, implement your solution and open a Pull Request at github.

### Implementation

Implement a GET web-endpoint that is accessed by advertisers to track a conversion (a customer buys a product) and connect it with the various internet placements the advertiser has deployed. 
The endpoint shall accept the following parameters:

 - **revenue** revenue of the conversion in €, full numbers (no cent-values). If cent values arrive, ceil of floor as appropriate
 - **customerId**
 - **bookingNumber** Booking number as given by the advertiser. Within the data-model, this needs to be unique per customer-platform composite key
 
On request, you need to read a cookie that holds points of contact of the requester with the advertiser's placements. For convenience, this cookie shall hold the following hard-coded json:
This endpoint must not have access restrictions.
```
$_COOKIE['mhs-tracking'] = '{"placements": [{"platform": "trivago", "customer_id": 123, "date_of_contact": "2018-01-01 14:00:00"}, {"platform": "tripadvisor", "customer_id": 123, "date_of_contact": "2018-01-03 14:00:00"}, {"platform": "kayak", "customer_id": 123, "date_of_contact": "2018-01-05 14:00:00"}]}';
```

This means the requester had contact with 3 placements of the advertiser with id "123" before actually buying something, first point of contact was platform "trivago" at January 1st.
On request with customerId=123, you shall now distribute the generated conversion revenue to all points of contact of the requester. If the request contains a customerId other then "123", do nothing.
As a result, the advertiser shall be able to ask the software via a RESTful interface about all generated conversions and how they distribute among her placements.
For this, you have to implement a distribution model alongside with an apprpriate database model to persist the data.
The distribution model shall honor the first- and last point of contact especially, with the first point of contact being the most important one.
So, common queries for the RESTful interface would read for example:
 - GET the platform that attracts the most customers the first time
 - GET the revenue by platform
 - GET the number of conversions (by platform)

The software must be executable at a local environment.

### Documentation

Document how your software has to be setup and executed. You may use any automation tools you want (or not).
Document your code.
Document your distribution model and database model. Choose a proper format, e.g. ERM for the database, UML for the distribution model. Only the distribution model allows plaintext as well ;)
Document the RESTful API.

### Test and execution

Explain how to test your software locally. This includes documentation on how to run automated tests, if any.
Provide a dataset to seed the database with a proper dataset that enables the RESTful interface to show what it can do.

## Tools

Implement the software in PHP. Apart from that, you may choose any tools, frameworks, libraries or infrastructure services you like. No Constraints! You may also implement everything in vanilla PHP.

## Optional Tasks

 - implement authentication for the RESTful interface
 - implement ACL for the RESTful  interface (e.g. only customer with id "123" is allowed to query for conversions with customerId "123")
 - show you DevOps skills: implement an automated (cloud) deployment of your software  