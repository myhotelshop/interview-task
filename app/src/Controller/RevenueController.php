<?php

namespace App\Controller;

use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class RevenueController extends AbstractApiController
{
    /**
     * Get Revenue distributions
     *
     * @Route("/revenues/distributions", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns a collection of revenue distributions",
     *     @SWG\Schema(ref="#/definitions/revenueDistributionCollection")
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Bad request",
     *     @SWG\Schema(ref="#/definitions/errorResponse")
     * )
     * @SWG\Response(
     *     response=500,
     *     description="Internal server error",
     *     @SWG\Schema(ref="#/definitions/errorResponse")
     * )
     * @SWG\Parameter(
     *     ref="#/parameters/conversionId"
     * )
     * @SWG\Tag(name="Revenues")
     * @param Request $request
     * @return JsonResponse
     */
    public function getRevenuesDistributions(Request $request): JsonResponse
    {
        $requestParams = $request->query->all();

        $platform = $requestParams[self::REQUEST_PARAM_CONVERSION_ID] ?? null;

        $filteredParams = [];

        if ($platform) {
            $filteredParams = [
                self::REQUEST_PARAM_PLATFORM => $requestParams[self::REQUEST_PARAM_PLATFORM]
            ];
        }

        //Quick approach to fetch counts, even though not ideal
        $total = $this->conversionModel->countBy($requestParams);

        $resource = new CollectionRepresentation($this->revenueModel->getDistributionsBy($filteredParams));
        $resource = new OffsetRepresentation(
            $resource,
            'app_conversion_getconversions',
            $requestParams,
            0,
            50,
            $total
        );

        return $this->respondOk($resource);
    }

    /**
     * Get one Revenue Distribution by id
     *
     * @Route("/revenues/distributions/{id}", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns a revenue distribution document",
     *     @SWG\Schema(ref="#/definitions/revenueDistribution")
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Not found",
     *     @SWG\Schema(ref="#/definitions/errorResponse")
     * )
     * @SWG\Response(
     *     response=500,
     *     description="Internal server error",
     *     @SWG\Schema(ref="#/definitions/errorResponse")
     * )
     * @SWG\Parameter(
     *     ref="#/parameters/idPath"
     * )
     * @SWG\Tag(name="Revenues")
     */
    public function getRevenueDistributionById(): JsonResponse
    {
        $distribution = null;

        if (!$distribution) {
            throw new HttpException(Response::HTTP_NOT_IMPLEMENTED);
        }

        return $this->json($distribution);
    }

    /**
     * Get total amount of distributed revenue for one platform
     *
     * @Route("/revenues/distributions/total-sum/{platform}", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns a platform total revenue distribution document",
     *     @SWG\Schema(ref="#/definitions/totalRevenueDistribution")
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Not found",
     *     @SWG\Schema(ref="#/definitions/errorResponse")
     * )
     * @SWG\Response(
     *     response=500,
     *     description="Internal server error",
     *     @SWG\Schema(ref="#/definitions/errorResponse")
     * )
     * @SWG\Parameter(
     *     ref="#/parameters/platformPath"
     * )
     * @SWG\Tag(name="Revenues")
     */
    public function getRevenuesDistributionsTotalSumByPlatform(Request $request): JsonResponse
    {
        dump($request);
        $totalDistribution = $this->revenueModel->getTotalRevenueDistributionByPlatform('tripadvisor');

        if (!$totalDistribution) {
            return new JsonResponse("", Response::HTTP_NOT_FOUND, []);
        }

        return $this->respondOk($totalDistribution);
    }
}