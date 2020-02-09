<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PlatformConversionRequest;
use App\Http\Requests\PlatformRevenueRequest;
use App\Http\Requests\TrackingRequest;
use App\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TrackingController extends Controller
{
  /**
   * @var RepositoryInterface
   */
  private $repository;

  /**
   * TrackingController constructor.
   * @param RepositoryInterface $repository
   */
  public function __construct(RepositoryInterface $repository)
  {
    $this->repository = $repository;
  }

  /**
   * @param TrackingRequest $request
   * @return JsonResponse
   */
  public function distributeRevenue(TrackingRequest $request): JsonResponse
  {
    $revenue = (int)$request->revenue;
    $customerId = (int)$request->customerId;
    $bookingNumber = $request->bookingNumber;

    //check if request has no cookie return 406 code status
    if (!$request->hasCookie('mhs_tracking'))
      return response()->json(['status' => false, 'message' => 'kindly sent cookie in the request'], Response::HTTP_NOT_ACCEPTABLE);

    $cookie = $request->cookie('mhs_tracking');
    $results = $this->repository->distributeRevenue($customerId, $bookingNumber, $revenue, $cookie);

    // I returned this status because distributeRevenue may return false because customerId may not equal 123
    if (!$results)
      return response()->json(['status' => false], Response::HTTP_UNPROCESSABLE_ENTITY);

    return response()->json(['status' => true], Response::HTTP_OK);
  }

  /**
   * @return JsonResponse
   */
  public function getMostAttractedPlatform(): JsonResponse
  {
    $platform = $this->repository->getMostAttractedPlatform();

    if (is_null($platform))
      return response()->json(['status' => false], Response::HTTP_UNPROCESSABLE_ENTITY);

    return response()->json([
      'status' => true,
      'platform' => $platform->platform,
      'count' => $platform->platform_count
    ], Response::HTTP_OK);
  }

  /**
   * @param PlatformRevenueRequest $request
   * @return JsonResponse
   */
  public function getPlatformRevenue(PlatformRevenueRequest $request): JsonResponse
  {
    $platform = $request->platform;
    $revenue = $this->repository->getPlatformRevenue($platform);
    return response()->json(['status' => true, $platform => $revenue], Response::HTTP_OK);
  }

  /**
   * @param PlatformConversionRequest $request
   * @return JsonResponse
   */
  public function getPlatformConversions(PlatformConversionRequest $request): JsonResponse
  {
    $platform = $request->platform;
    $platformConversionsCount = $this->repository->getPlatformConversions($platform);
    return response()->json(['status' => true, $platform => $platformConversionsCount], Response::HTTP_OK);
  }
}
