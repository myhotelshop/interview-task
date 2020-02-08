<?php

namespace Tests\Unit;

use App\Models\Conversion;
use App\Models\Customer;
use App\Services\TrackingService;
use Tests\TestCase;

class TrackingServiceTest extends TestCase
{

  // start distribute_revenue tests
  /** @test */
  public function distribute_revenue()
  {
    $trackingService = new TrackingService();
    $customer = create(Customer::class);
    $cookie = '{"placements": [
      {"platform": "trivago", "customer_id": 123, "date_of_contact": "2018-01-01 14:00:00"}, 
      {"platform": "tripadvisor", "customer_id": 123, "date_of_contact": "2018-01-03 14:00:00"}, 
      {"platform": "kayak", "customer_id": 123, "date_of_contact": "2018-01-05 14:00:00"}
    ]}';
    $response = $trackingService->distributeRevenue($customer->id, 'abc123', 30, $cookie);
    $this->assertTrue($response);
  }

  // start get_most_attracted_platform tests

  /** @test */
  public function get_most_attracted_platform()
  {
    $trackingService = new TrackingService();
    create(Customer::class);
    create(Conversion::class, ['platform' => 'trivago'], 10);
    create(Conversion::class, ['platform' => 'tripadvisor'], 5);
    $result = $trackingService->getMostAttractedPlatform();
    $this->assertEquals($result->platform, 'trivago');
  }

  /** @test */
  public function get_most_attracted_platform_if_database_is_empty()
  {
    $trackingService = new TrackingService();
    $result = $trackingService->getMostAttractedPlatform();
    $this->assertNull($result);
  }
  // end get_most_attracted_platform

  // start get_platform_revenue tests
  /** @test */
  public function get_platform_revenue()
  {
    $trackingService = new TrackingService();
    create(Customer::class);
    create(Conversion::class, ['platform' => 'trivago', 'revenue' => 10]);
    create(Conversion::class, ['platform' => 'trivago', 'revenue' => 25]);
    $result = $trackingService->getPlatformRevenue('trivago');
    $this->assertEquals($result, 35);
  }

  /** @test */
  public function get_platform_revenue_if_database_is_empty()
  {
    $trackingService = new TrackingService();
    $result = $trackingService->getPlatformRevenue('trivago');
    $this->assertEquals(0, $result);
  }
  // end get_platform_revenue


  // start get_platform_conversions tests
  /** @test */
  public function get_platform_conversions_count()
  {
    $trackingService = new TrackingService();
    create(Customer::class);
    create(Conversion::class, ['platform' => 'trivago'], 20);
    $result = $trackingService->getPlatformConversions('trivago');
    $this->assertEquals($result, 20);
  }

  /** @test */
  public function get_platform_conversions_count_if_database_is_empty()
  {
    $trackingService = new TrackingService();
    $result = $trackingService->getPlatformConversions('trivago');
    $this->assertEquals($result, 0);
  }
}