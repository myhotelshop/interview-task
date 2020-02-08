<?php

namespace Tests\Feature;

use App\Models\Conversion;
use App\Models\Customer;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TrackingTest extends TestCase
{
  use DatabaseMigrations;

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
}
