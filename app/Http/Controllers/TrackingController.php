<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlatformConversionRequest;
use App\Http\Requests\PlatformRevenueRequest;
use App\Http\Requests\TrackingRequest;
use App\Services\TrackingService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TrackingController extends Controller
{
  /**
   * @var TrackingService
   */
  private $trackingService;

  /**
   * TrackingController constructor.
   * @param TrackingService $trackingService
   */
  public function __construct(TrackingService $trackingService)
  {
    $this->trackingService = $trackingService;
  }

  /**
   * @param TrackingRequest $request
   * @return JsonResponse
   */
  public function distributeRevenue(TrackingRequest $request)
  {
    $revenue = (int)$request->revenue;
    $customerId = $request->customerId;
    $bookingNumber = $request->bookingNumber;

    //check if request has no cookie return 406 code status
    if (!$request->hasCookie('mhs_tracking'))
      return response()->json(['status' => false], Response::HTTP_NOT_ACCEPTABLE);

    $cookie = $request->cookie('mhs_tracking');
    $results = $this->trackingService->distributeRevenue($customerId, $bookingNumber, $revenue, $cookie);

    if (!$results)
      return response()->json(['status' => false], Response::HTTP_UNPROCESSABLE_ENTITY);

    return response()->json(['status' => true], Response::HTTP_OK);
  }

  /**
   * @return JsonResponse
   */
  public function getMostAttractedPlatform()
  {
    $platform = $this->trackingService->getMostAttractedPlatform();
    if (is_null($platform)) {
      return response()->json(['status' => false], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
    return response()->json(['status' => true, 'platform' => $platform->platform], Response::HTTP_OK);
  }

  /**
   * @param PlatformRevenueRequest $request
   * @return JsonResponse
   */
  public function getPlatformRevenue(PlatformRevenueRequest $request)
  {
    $platform = $request->platform;
    $revenue = $this->trackingService->getPlatformRevenue($platform);
    return response()->json(['status' => true, $platform => $revenue], Response::HTTP_OK);
  }

  /**
   * @param PlatformConversionRequest $request
   * @return JsonResponse
   */
  public function getPlatformConversions(PlatformConversionRequest $request)
  {
    $platform = $request->platform;
    $count = $this->trackingService->getPlatformConversions($platform);
    return response()->json(['status' => true, $platform => $count], Response::HTTP_OK);
  }
}
