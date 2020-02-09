<?php


namespace App\Repositories\Interfaces;


interface RepositoryInterface
{

  public function distributeRevenue(int $customerId, string $bookingNumber, int $revenue, string $cookie): bool;

  public function getMostAttractedPlatform();

  public function getPlatformRevenue(string $platform): int;

  public function getPlatformConversions(string $platform):int;
}