<?php
namespace App\tracking\api\v1\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackingController extends AbstractFOSRestController
{
    /**
     * Tracks the user conversion and distribute revenue on platforms
     * @Rest\Get("/track")
     * @Rest\QueryParam(name="revenue", strict=true, requirements="\d+")
     * @Rest\QueryParam(name="customerId", strict=true, requirements="\d+")
     * @Rest\QueryParam(name="bookingReference", strict=true, requirements="\d+")
     * @param Request $request
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function track(Request $request, ParamFetcher $paramFetcher)
    {
        $revenue = $paramFetcher->get('revenue');
        $customerId = $paramFetcher->get('customerId');
        $bookingReference = $paramFetcher->get('bookingReference');

        $cookie = $request->cookies->get('tracking');

        return $this->handleView(
            $this->view(
                [
                    'revenue' => $revenue,
                    'customerId' => $customerId,
                    'bookingReference' => $bookingReference,
                    'tracking' => $cookie
                ],
                Response::HTTP_CREATED
            )
        );
    }
}
