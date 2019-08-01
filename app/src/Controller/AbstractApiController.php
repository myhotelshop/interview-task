<?php


namespace App\Controller;

use App\Model\ConversionModelInterface;
use App\Model\RevenueModelInterface;
use Hateoas\Hateoas;
use Hateoas\HateoasBuilder;
use Hateoas\UrlGenerator\SymfonyUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AbstractApiController extends AbstractController
{
    protected const REQUEST_PARAM_CUSTOMER_ID = 'customerId';
    protected const REQUEST_PARAM_REVENUE = 'revenue';
    protected const REQUEST_PARAM_BOOKING_NUMBER = 'bookingNumber';
    protected const REQUEST_PARAM_PLATFORM = 'platform';

    /**
     * @var Hateoas
     */
    protected $hateoas;

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @var ConversionModelInterface
     */
    protected $conversionModel;
    /**
     * @var RevenueModelInterface
     */
    protected $revenueModel;

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
     * @param $resource
     *
     * @return JsonResponse
     */
    protected function respond($resource): JsonResponse
    {
        $json = $this->hateoas->serialize($resource, 'json');

        return new JsonResponse($json, 200, [], true);
    }
}