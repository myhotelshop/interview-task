<?php

namespace App\Controller;

use App\Entity\Conversion;
use App\Entity\Visit;
use App\Entity\VisitCollection;
use DateTime;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\OffsetRepresentation;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class ConversionController extends AbstractApiController
{
    /**
     * Create a new conversion
     *
     * @Route("/conversions/new", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Create a conversion"
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
     *     ref="#/parameters/revenue"
     * )
     * @SWG\Parameter(
     *     ref="#/parameters/customerId"
     * )
     * @SWG\Parameter(
     *     ref="#/parameters/bookingNumber"
     * )
     * @SWG\Tag(name="Conversions")
     */
    public function postConversion(Request $request): Response
    {
        $requestParams = $request->query->all();

        $hasRequired = $this->hasRequiredParams([
            self::REQUEST_PARAM_CUSTOMER_ID,
            self::REQUEST_PARAM_REVENUE,
            self::REQUEST_PARAM_BOOKING_NUMBER
        ], $requestParams);

        if (!$hasRequired) {
            return new JsonResponse([
                'code' => Response::HTTP_BAD_REQUEST,
                'reason' => 'Bad request',
                'message' => 'required parameters are missing'
            ], Response::HTTP_BAD_REQUEST);
        }


        $customerId = (int)$requestParams[self::REQUEST_PARAM_CUSTOMER_ID];

        if ($customerId === 123) {
            $conversion = $this->createConversion($requestParams);

            $this->enhanceConversion($conversion);

            $this->conversionModel->post($conversion);
        }

        return new Response("", Response::HTTP_OK, []);
    }

    /**
     * Get conversions
     *
     * @Route("/conversions", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns a collection of conversions",
     *     @SWG\Schema(ref="#/definitions/conversionCollection")
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
     *     ref="#/parameters/platform"
     * )
     * @SWG\Tag(name="Conversions")
     * @param Request $request
     * @return JsonResponse
     */
    public function getConversions(Request $request): JsonResponse
    {
        $requestParams = $request->query->all();

        $platform = $requestParams[self::REQUEST_PARAM_PLATFORM] ?? null;

        $filteredParams = [];

        if ($platform) {
            $filteredParams = [
                self::REQUEST_PARAM_PLATFORM => $requestParams[self::REQUEST_PARAM_PLATFORM]
            ];
        }

        //Quick approach to fetch counts, even though not ideal
        $total = $this->conversionModel->countBy($filteredParams);

        $resource = new CollectionRepresentation($this->conversionModel->getBy($filteredParams));
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
     * Get one conversion by id
     *
     * @Route("/conversions/{id}", methods={"GET"})
     * @SWG\Response(
     *     response=501,
     *     description="Internal server error",
     *     @SWG\Schema(ref="#/definitions/errorResponse")
     * )
     * @SWG\Parameter(
     *     ref="#/parameters/idPath"
     * )
     * @SWG\Tag(name="Conversions")
     */
    public function getConversionById(): JsonResponse
    {
        $conversion = null;

        if (!$conversion) {
            throw new HttpException(Response::HTTP_NOT_IMPLEMENTED);
        }

        return $this->json($conversion);
    }

    /**
     * Get the platform which was the first place of contact before a conversion the most times
     *
     * @Route("/conversions/performance/most-attractive-platform", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns a most popular platform document",
     *     @SWG\Schema(ref="#/definitions/mostAttractivePlatform")
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
     * @SWG\Tag(name="Conversions")
     */
    public function getMostAttractivePlatform(): JsonResponse
    {
        return $this->respondOk($this->conversionModel->getMostAttractivePlatform());
    }

    /**
     * @param array $requiredKeys
     * @param array $params
     * @return bool
     */
    private function hasRequiredParams(array $requiredKeys, array $params): bool
    {
        $has = true;
        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $params)) {
                $has = false;
                break;
            }
        }

        return $has;
    }

    /**
     * @param array $requestParams
     * @return Conversion
     */
    private function createConversion(array $requestParams): Conversion
    {
        $customerId = (int)$requestParams[self::REQUEST_PARAM_CUSTOMER_ID];
        $revenue = round((float)$requestParams[self::REQUEST_PARAM_REVENUE]);
        $bookingNumber = $customerId . '_' . $requestParams[self::REQUEST_PARAM_BOOKING_NUMBER];

        $conversion = new Conversion();
        $conversion->setBookingNumber($bookingNumber);
        $conversion->setConversationDate(new DateTime('now'));
        $conversion->setCustomerId($customerId);
        $conversion->setRevenue($revenue);

        return $conversion;
    }

    /**
     * @param Conversion $conversion
     */
    private function enhanceConversion(Conversion $conversion)
    {
        $visits = $this->getVisitsFromCookie();

        $this->setConversionPlatform($conversion, $visits);
        $this->addConversionRevenueDistributions($conversion, $visits);
    }

    /**
     * @param Conversion $conversion
     * @param VisitCollection $visits
     */
    private function setConversionPlatform(Conversion $conversion, VisitCollection $visits)
    {
        //Last visit is the place where conversion happened
        $latestVisit = $visits->getLatest();
        $platform = $latestVisit ? $latestVisit->getPlatform() : "";
        $conversion->setPlatform($platform);
    }

    /**
     * @param Conversion $conversion
     * @param VisitCollection $visits
     */
    private function addConversionRevenueDistributions(Conversion $conversion, VisitCollection $visits)
    {
        $distributions = $this->revenueModel->distribute($conversion, $visits);

        foreach ($distributions as $distribution) {
            $conversion->addRevenueDistribution($distribution);
        }
    }

    /**
     * Simulation of eventual cookie to get visited platforms before conversion.
     * Returns hardcoded values
     *
     * @return VisitCollection
     */
    private function getVisitsFromCookie(): VisitCollection
    {
        $visits = [];

        $visits[] = (new Visit())
            ->setPlatform('trivago')
            ->setCustomerId(123)
            ->setDate(new DateTime('2018-01-01 14:00:00'));

        $visits[] = (new Visit())
            ->setPlatform('tripadvisor')
            ->setCustomerId(123)
            ->setDate(new DateTime('2018-01-03 14:00:00'));

        $visits[] = (new Visit())
            ->setPlatform('kayak')
            ->setCustomerId(123)
            ->setDate(new DateTime('2018-01-05 14:00:00'));

        return new VisitCollection($visits);
    }
}