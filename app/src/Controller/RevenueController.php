<?php

namespace App\Controller;

use App\Model\Scope\ScopeModel;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class RevenueController extends AbstractController
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
     */
    public function getRevenuesDistributions(): JsonResponse
    {
        $distributions = null;

        if (!$distributions) {
            throw new HttpException(Response::HTTP_NOT_IMPLEMENTED);
        }

        return $this->json($distributions);
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
    public function getRevenuesDistributionsTotalSumByPlatform(): JsonResponse
    {
        $totalSum = null;

        if (!$totalSum) {
            throw new HttpException(Response::HTTP_NOT_IMPLEMENTED);
        }

        return $this->json($totalSum);
    }
}