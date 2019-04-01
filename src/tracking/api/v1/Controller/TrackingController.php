<?php
namespace App\tracking\api\v1\Controller;

use App\tracking\api\v1\Service\TrackingService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Exception;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Class TrackingController
 * @package App\tracking\api\v1\Controller
 * @author Mohammed Yehia <firefoxegy@gmail.com>
 */
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
     * @Rest\QueryParam(name="revenue", strict=true, requirements="^[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?$")
     * @Rest\QueryParam(name="customerId", strict=true, requirements="\d+")
     * @Rest\QueryParam(name="bookingReference", strict=true,
     *     requirements="[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}")
     * @param Request $request
     * @param ParamFetcher $paramFetcher
     * @IsGranted("ROLE_USER")
     * @return Response
     * @throws Exception
     */
    public function track(Request $request, ParamFetcher $paramFetcher)
    {
        $revenue = $paramFetcher->get('revenue');
        $customerId = $paramFetcher->get('customerId');
        $bookingReference = $paramFetcher->get('bookingReference');

        $revenue = round($revenue);

        $cookie = $request->cookies->get('tracking');

        $this->trackingService->isValidRequest($customerId, $cookie);
        $this->trackingService->distributeRevenue($customerId, $cookie, $bookingReference, $revenue);

        return $this->handleView($this->view(null, Response::HTTP_OK));
    }

    /**
     * Get the platform that most attracts customers first
     * @Rest\Get("/platform")
     * @IsGranted("ROLE_USER")
     * @throws DBALException
     */
    public function platform()
    {
        return $this->handleView(
            $this->view(
                ['platform' => $this->trackingService->getMostAttractedPlatform()],
                Response::HTTP_OK
            )
        );
    }

    /**
     * Get the revenue of a platform
     * @Rest\Get("/revenue")
     * @Rest\QueryParam(name="platform", strict=true, requirements="\d+")
     * @param ParamFetcher $paramFetcher
     * @IsGranted("ROLE_USER")
     * @return Response
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function revenue(ParamFetcher $paramFetcher)
    {
        $platform = $paramFetcher->get('platform');

        $this->trackingService->checkPlatform($platform);

        return $this->handleView(
            $this->view(
                ['revenue' => $this->trackingService->getRevenueByPlatform((int) $platform)],
                Response::HTTP_OK
            )
        );
    }

    /**
     * Get the number of conversion for a given platform
     * @Rest\Get("/conversion")
     * @Rest\QueryParam(name="platform", strict=true, requirements="\d+")
     * @param ParamFetcher $paramFetcher
     * @IsGranted("ROLE_USER")
     * @return Response
     * @throws DBALException
     */
    public function conversion(ParamFetcher $paramFetcher)
    {
        $platform = $paramFetcher->get('platform');

        $this->trackingService->checkPlatform($platform);

        return $this->handleView(
            $this->view(
                ['conversion' => $this->trackingService->getConversionsOfPlatform((int) $platform)],
                Response::HTTP_OK
            )
        );
    }
}
