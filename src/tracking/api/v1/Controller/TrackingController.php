<?php
namespace App\tracking\api\v1\Controller;

use App\tracking\api\v1\Service\TrackingService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackingController extends AbstractFOSRestController
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
     * Tracks the user conversion and distribute revenue on platforms
     * @Rest\Get("/track")
     * @Rest\QueryParam(name="revenue", strict=true, requirements="\d+")
     * @Rest\QueryParam(name="customerId", strict=true, requirements="\d+")
     * @Rest\QueryParam(name="bookingReference", strict=true,
     *     requirements="[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}")
     * @param Request $request
     * @param ParamFetcher $paramFetcher
     * @return Response
     * @throws \Exception
     */
    public function track(Request $request, ParamFetcher $paramFetcher)
    {
        $revenue = $paramFetcher->get('revenue');
        $customerId = $paramFetcher->get('customerId');
        $bookingReference = $paramFetcher->get('bookingReference');

        $cookie = $request->cookies->get('tracking');

        $this->trackingService->isValidRequest($customerId, $cookie);
        $this->trackingService->distributeRevenue($customerId, $cookie, $bookingReference, $revenue);

        return $this->handleView(
            $this->view(null, Response::HTTP_CREATED)
        );
    }
}
