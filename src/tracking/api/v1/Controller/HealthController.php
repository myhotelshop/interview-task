<?php
namespace App\tracking\api\v1\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TrackingController
 * @package App\tracking\api\v1\Controller
 * @author Mohammed Yehia <firefoxegy@gmail.com>
 */
class HealthController extends AbstractFOSRestController
{
    /**
     * Tracks the user conversion and distribute revenue on platforms
     * @Rest\Get("/health")
     * @return Response
     */
    public function index()
    {
        return $this->handleView($this->view(null, Response::HTTP_OK));
    }
}
