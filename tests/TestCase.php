<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
  use CreatesApplication, RefreshDatabase;

  protected function mockCookieData($isValidCookie = true)
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
}
