<?php
declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Conversion;
use App\Models\Customer;
use App\Repositories\ConversionRepository;
use Tests\TestCase;

class ConversionRepositoryTest extends TestCase
{
  private $trackingService;
  public function setUp()
  {
    parent::setUp();
    $this->trackingService = new ConversionRepository(new Conversion());
  }
  // start distribute_revenue tests

  /**
   * @test
   * @covers \App\Repositories\ConversionRepository::distributeRevenue()
   */
  public function distribute_revenue(): void
  {

    $customer = create(Customer::class);
    $cookie = mockCookieData();
    $response = $this->trackingService->distributeRevenue($customer->id, 'abc123', 30, $cookie);
    $this->assertTrue($response);
  }

  // start get_most_attracted_platform tests

  /**
   * @test
   * @covers \App\Repositories\ConversionRepository::getMostAttractedPlatform()
   */
  public function get_most_attracted_platform(): void
  {
    create(Customer::class);
    create(Conversion::class, ['platform' => 'trivago'], 10);
    create(Conversion::class, ['platform' => 'tripadvisor'], 5);
    $result = $this->trackingService->getMostAttractedPlatform();
    $this->assertEquals($result->platform, 'trivago');
  }

  /**
   * @test
   * @covers \App\Repositories\ConversionRepository::getMostAttractedPlatform()
   */
  public function get_most_attracted_platform_if_database_is_empty(): void
  {
    $result = $this->trackingService->getMostAttractedPlatform();
    $this->assertNull($result);
  }
  // end get_most_attracted_platform

  // start get_platform_revenue tests
  /**
   * @test
   * @covers \App\Repositories\ConversionRepository::getPlatformRevenue()
   */
  public function get_total_platform_revenue(): void
  {
    create(Customer::class);
    create(Conversion::class, ['platform' => 'trivago', 'revenue' => 10]);
    create(Conversion::class, ['platform' => 'trivago', 'revenue' => 25]);
    $result = $this->trackingService->getPlatformRevenue('trivago');
    $this->assertEquals($result, 35);
  }

  /**
   * @test
   * @covers \App\Repositories\ConversionRepository::getPlatformRevenue()
   */
  public function get_platform_revenue_if_database_is_empty(): void
  {
    $result = $this->trackingService->getPlatformRevenue('trivago');
    $this->assertEquals(0, $result);
  }
  // end get_platform_revenue


  // start get_platform_conversions tests
  /**
   * @test
   * @covers \App\Repositories\ConversionRepository::getPlatformConversions()
   */
  public function get_platform_conversions_count(): void
  {
    create(Customer::class);
    create(Conversion::class, ['platform' => 'trivago'], 20);
    $result = $this->trackingService->getPlatformConversions('trivago');
    $this->assertEquals($result, 20);
  }

  /**
   * @test
   * @covers \App\Repositories\ConversionRepository::getPlatformConversions()
   */
  public function get_platform_conversions_count_if_database_is_empty(): void
  {
    $result = $this->trackingService->getPlatformConversions('trivago');
    $this->assertEquals($result, 0);
  }
}