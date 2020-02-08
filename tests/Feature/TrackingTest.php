<?php

namespace Tests\Feature;

use App\Models\Conversion;
use App\Models\Customer;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TrackingTest extends TestCase
{
  // start distribute_revenue endpoint tests
  /** @test */
  public function distribute_revenue_valid_request()
  {
    $request = [
      'revenue' => 6,
      'customerId' => 123,
      'bookingNumber' => Str::random()
    ];
    create(Customer::class);
    $cookie = [
      'mhs_tracking' => '{
        "placements": [
            {"platform": "trivago", "customer_id": 123, "date_of_contact": "2018-01-01 14:00:00"}, 
            {"platform": "tripadvisor", "customer_id": 123, "date_of_contact": "2018-01-03 14:00:00"}, 
            {"platform": "kayak", "customer_id": 123, "date_of_contact": "2018-01-05 14:00:00"}
        ]
      }'
    ];
    $response = $this->call('GET', '/api/distribute-revenue', $request, $cookie);
    $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
  }

  /** @test */
  public function distribute_revenue_has_no_cookie()
  {
    $request = [
      'revenue' => 6,
      'customerId' => 123,
      'bookingNumber' => Str::random()
    ];
    create(Customer::class);
    $response = $this->call('GET', '/api/distribute-revenue', $request);
    $this->assertEquals(Response::HTTP_NOT_ACCEPTABLE, $response->getStatusCode());
  }

  /** @test */
  public function distribute_revenue_invalid_cookie_customer_id()
  {
    $request = [
      'revenue' => 6,
      'customerId' => 123,
      'bookingNumber' => Str::random()
    ];
    create(Customer::class);
    $cookie = [
      'mhs_tracking' => '{
        "placements": [
            {"platform": "trivago", "customer_id": 1234, "date_of_contact": "2018-01-01 14:00:00"}, 
            {"platform": "tripadvisor", "customer_id": 123, "date_of_contact": "2018-01-03 14:00:00"}, 
            {"platform": "kayak", "customer_id": 123, "date_of_contact": "2018-01-05 14:00:00"}
        ]
      }'
    ];
    $response = $this->call('GET', '/api/distribute-revenue', $request, $cookie);
    $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
  }

  /** @test */
  public function distribute_revenue_requires_a_customer_id_that_exists()
  {
    $this->json('GET', '/api/distribute-revenue', [
      'revenue' => 6,
      'bookingNumber' => Str::random()
    ])
      ->assertJsonValidationErrors(['customerId']);
  }

  /** @test */
  public function distribute_revenue_requires_a_revenue_that_exists()
  {
    create(Customer::class);
    $this->json('GET', '/api/distribute-revenue', [
      'customerId' => 123,
      'bookingNumber' => Str::random()
    ])
      ->assertJsonValidationErrors(['revenue']);
  }

  /** @test */
  public function distribute_revenue_requires_a_booking_number_that_exists()
  {
    create(Customer::class);
    $this->json('GET', '/api/distribute-revenue', [
      'customerId' => 123,
      'revenue' => 30
    ])
      ->assertJsonValidationErrors(['bookingNumber']);
  }
  // end distribute_revenue endpoint tests

  // start most_attracted_platform endpoint tests
  /** @test */
  public function most_attracted_platform()
  {
    create(Customer::class);
    $conversion = create(Conversion::class, ['platform' => 'trivago']);
    $this->json('GET', '/api/most-attracted-platform')
      ->assertJsonFragment(['platform' => $conversion->platform]);

  }

  /** @test */
  public function most_attracted_platform_is_false_when_database_is_empty()
  {
    $this->json('GET', '/api/most-attracted-platform')
      ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
  }
  // end most_attracted_platform endpoint tests

  // start platform_revenue endpoint tests
  /** @test */
  public function get_platform_revenue_requires_a_platform_that_exists()
  {
    create(Customer::class);
    $this->json('GET', '/api/platform-revenue')
      ->assertJsonValidationErrors(['platform']);
  }

  /** @test */
  public function get_platform_revenue_for_non_existed_platform()
  {
    $this->json('GET', '/api/platform-revenue?platform=trivago')
      ->assertJsonFragment(['trivago' => 0]);
  }

  /** @test */
  public function get_platform_total_revenue()
  {
    create(Customer::class);
    create(Conversion::class, ['platform' => 'trivago', 'revenue' => 10]);
    create(Conversion::class, ['platform' => 'trivago', 'revenue' => 20]);
    $this->json('GET', '/api/platform-revenue?platform=trivago')
      ->assertJsonFragment(['trivago' => 30]);
  }
  // end platform_revenue endpoint tests

  // start platform_conversions endpoint tests
  /** @test */
  public function get_platform_conversions_requires_a_platform_that_exists()
  {
    create(Customer::class);
    $this->json('GET', '/api/platform-conversions')
      ->assertJsonValidationErrors(['platform']);
  }

  /** @test */
  public function get_platform_conversions_for_non_existed_platform()
  {
    $this->json('GET', '/api/platform-conversions?platform=trivago')
      ->assertJsonFragment(['trivago' => 0]);
  }

  /** @test */
  public function get_platform_conversions_count()
  {
    create(Customer::class);
    create(Conversion::class, ['platform' => 'trivago', 'revenue' => 10]);
    create(Conversion::class, ['platform' => 'trivago', 'revenue' => 10]);
    create(Conversion::class, ['platform' => 'trivago', 'revenue' => 10]);
    $this->json('GET', '/api/platform-conversions?platform=trivago')
      ->assertJsonFragment(['trivago' => 3]);
  }
  // end platform_revenue endpoint tests
}
