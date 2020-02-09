<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Conversion;
use App\Models\Customer;
use App\Services\TrackingService;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TrackingControllerTest extends TestCase
{

  // start distribute_revenue endpoint tests
  /** @test */
  public function distribute_revenue_valid_request(): void
  {
    $request = $this->mockRequest(TrackingService::$customerId, 6);
    create(Customer::class);
    $cookie = [
      'mhs_tracking' => mockCookieData()
    ];
    $this->call('GET', '/api/distribute-revenue', $request, $cookie)
      ->assertStatus(Response::HTTP_OK)
      ->assertJson([
        'status' => true,
      ]);
  }

  /** @test */
  public function distribute_revenue_has_no_cookie(): void
  {
    $request = $this->mockRequest(TrackingService::$customerId, 6);
    create(Customer::class);
    $this->call('GET', '/api/distribute-revenue', $request)
      ->assertStatus(Response::HTTP_NOT_ACCEPTABLE)
      ->assertJson([
        'status' => false,
      ]);
  }

  /** @test */
  public function distribute_revenue_invalid_cookie_customer_id(): void
  {
    $request = $this->mockRequest(TrackingService::$customerId, 6);
    create(Customer::class);
    // here I changed placements first placement customer_id to 1234
    $cookie = [
      'mhs_tracking' => mockCookieData(false)
    ];
    $this->call('GET', '/api/distribute-revenue', $request, $cookie)
      ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
      ->assertJson([
        'status' => false,
      ]);
  }

  /** @test */
  public function distribute_revenue_requires_a_customer_id_that_exists(): void
  {
    $request = $this->mockRequest(null,6);
    $this->json('GET', '/api/distribute-revenue', $request)
      ->assertJsonValidationErrors(['customerId'])
      ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
  }

  /** @test */
  public function distribute_revenue_requires_a_revenue_that_exists(): void
  {
    create(Customer::class);
    $request = $this->mockRequest(TrackingService::$customerId);
    $this->json('GET', '/api/distribute-revenue', $request)
      ->assertJsonValidationErrors(['revenue'])
      ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
  }

  /** @test */
  public function distribute_revenue_requires_a_booking_number_that_exists(): void
  {
    create(Customer::class);
    $this->json('GET', '/api/distribute-revenue', [
      'customerId' => TrackingService::$customerId,
      'revenue' => 30
    ])
      ->assertJsonValidationErrors(['bookingNumber'])
      ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
  }
  // end distribute_revenue endpoint tests

  // start most_attracted_platform endpoint tests
  /** @test */
  public function most_attracted_platform(): void
  {
    create(Customer::class);
    $conversion = create(Conversion::class, ['platform' => 'trivago']);
    $this->json('GET', '/api/most-attracted-platform')
      ->assertJsonFragment(['platform' => $conversion->platform])
      ->assertStatus(Response::HTTP_OK);

  }

  /** @test */
  public function most_attracted_platform_is_false_when_database_is_empty(): void
  {
    $this->json('GET', '/api/most-attracted-platform')
      ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
      ->assertJson([
        'status' => false,
      ]);
  }
  // end most_attracted_platform endpoint tests

  // start platform_revenue endpoint tests
  /** @test */
  public function get_platform_revenue_requires_a_platform_that_exists(): void
  {
    create(Customer::class);
    $this->json('GET', '/api/platform-revenue')
      ->assertJsonValidationErrors(['platform'])
      ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
  }

  /** @test */
  public function get_platform_revenue_for_non_existed_platform(): void
  {
    $this->json('GET', '/api/platform-revenue?platform=trivago')
      ->assertJsonFragment(['trivago' => 0])
      ->assertStatus(Response::HTTP_OK);
  }

  /** @test */
  public function get_platform_total_revenue(): void
  {
    create(Customer::class);
    create(Conversion::class, ['platform' => 'trivago', 'revenue' => 10]);
    create(Conversion::class, ['platform' => 'trivago', 'revenue' => 20]);
    $this->json('GET', '/api/platform-revenue?platform=trivago')
      ->assertJsonFragment(['trivago' => 30])
      ->assertStatus(Response::HTTP_OK);
  }
  // end platform_revenue endpoint tests

  // start platform_conversions endpoint tests
  /** @test */
  public function get_platform_conversions_requires_a_platform_that_exists(): void
  {
    create(Customer::class);
    $this->json('GET', '/api/platform-conversions')
      ->assertJsonValidationErrors(['platform'])
      ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
  }

  /** @test */
  public function get_platform_conversions_for_non_existed_platform(): void
  {
    $this->json('GET', '/api/platform-conversions?platform=trivago')
      ->assertJsonFragment(['trivago' => 0])
      ->assertStatus(Response::HTTP_OK);
  }

  /** @test */
  public function get_platform_conversions_count(): void
  {
    $times = 3;
    create(Customer::class);
    create(Conversion::class, ['platform' => 'trivago', 'revenue' => 10], $times);
    $this->json('GET', '/api/platform-conversions?platform=trivago')
      ->assertJsonFragment(['trivago' => $times])
      ->assertStatus(Response::HTTP_OK);
  }

  // end platform_revenue endpoint tests

  /**
   * @return array
   */
  private function mockRequest(?int $customerId = null, ?int $revenue = null, ?string $bookingNumber = null): array
  {
    $request = [];
    !is_null($customerId) ? $request['customerId'] = $customerId : null;
    !is_null($revenue) ? $request['revenue'] = $revenue : null;
    $request['bookingNumber'] = !is_null($bookingNumber) ? $request['bookingNumber'] : Str::random();
    return $request;
  }
}
