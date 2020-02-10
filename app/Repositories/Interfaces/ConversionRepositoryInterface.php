<?php
declare(strict_types=1);

namespace App\Repositories\Interfaces;


interface ConversionRepositoryInterface
{

  public function distributeRevenue(int $customerId, string $bookingNumber, int $revenue, string $cookie): bool;

  public function getMostAttractedPlatform();

  public function getPlatformRevenue(string $platform): int;

  public function getPlatformConversions(string $platform):int;
}