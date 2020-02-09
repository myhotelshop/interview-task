<?php

function create($class, $attributes = [], $times = null)
{
  return factory($class, $times)->create($attributes);
}

function make($class, $attributes = [], $times = null)
{
  return factory($class, $times)->make($attributes);
}

function mockCookieData($isValidCookie = true)
{
  return $isValidCookie == true ? '{"placements": [
      {"platform": "trivago", "customer_id": 123, "date_of_contact": "2018-01-01 14:00:00"}, 
      {"platform": "tripadvisor", "customer_id": 123, "date_of_contact": "2018-01-03 14:00:00"}, 
      {"platform": "kayak", "customer_id": 123, "date_of_contact": "2018-01-05 14:00:00"}
    ]}' :
    '{
        "placements": [
            {"platform": "trivago", "customer_id": 1234, "date_of_contact": "2018-01-01 14:00:00"}, 
            {"platform": "tripadvisor", "customer_id": 123, "date_of_contact": "2018-01-03 14:00:00"}, 
            {"platform": "kayak", "customer_id": 123, "date_of_contact": "2018-01-05 14:00:00"}
        ]
      }';
}