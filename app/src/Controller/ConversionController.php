<?php

namespace App\Controller;

use App\Entity\Conversion;
use App\Entity\RevenueDistribution;
use App\Entity\Visit;
use App\Entity\VisitCollection;
use App\Model\ConversionModelInterface;
use App\Model\RevenueModelInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Hateoas\Hateoas;
use Hateoas\HateoasBuilder;
use Hateoas\UrlGenerator\SymfonyUrlGenerator;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ConversionController extends AbstractController
{
    /**
     * @var Hateoas
     */
    private $hateoas;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var ConversionModelInterface
     */
    private $conversionModel;
    /**
     * @var RevenueModelInterface
     */
    private $revenueModel;

    public function __construct(
        ConversionModelInterface $conversionModel,
        RevenueModelInterface $revenueModel,
        UrlGeneratorInterface $urlGenerator
    )
    {
        $this->urlGenerator = $urlGenerator;
        $builder = HateoasBuilder::create();
        $this->hateoas = $builder
            ->setDebug(false)
            ->setUrlGenerator(null, new SymfonyUrlGenerator($this->urlGenerator))
            ->build();

        $this->conversionModel = $conversionModel;
        $this->revenueModel = $revenueModel;
    }

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
        $customerId = (int)$requestParams['customerId'];

        if ($customerId === 123) {

            //logic to get last contact not working (either logic to get it or dates are not working at all)
            // tests (?)
            // distribution
            // make it possible to filter on loading
            // revenue endpoints
            // documentation
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
     */
    public function getConversions(): JsonResponse
    {
        return $this->respond($this->conversionModel->getBy([]));
    }

    /**
     * Get one conversion by id
     *
     * @Route("/conversions/{id}", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns a conversion document",
     *     @SWG\Schema(ref="#/definitions/conversion")
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
     * @param $resource
     *
     * @return JsonResponse
     */
    protected function respond($resource): JsonResponse
    {
        $json = $this->hateoas->serialize($resource, 'json');

        return new JsonResponse($json, 200, [], true);
    }

    /**
     * @param array $requestParams
     * @return Conversion
     */
    private function createConversion(array $requestParams): Conversion
    {
        $customerId = (int)$requestParams['customerId'];
        $revenue = round((float)$requestParams['revenue']);
        $bookingNumber = $customerId . '_' . $requestParams['bookingNumber'];

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
        $distributions = $this->revenueModel->distribute($conversion->getRevenue(), $visits);

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