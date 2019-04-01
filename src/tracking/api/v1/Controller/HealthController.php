<?php
namespace App\tracking\api\v1\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HealthController
 * @package App\tracking\api\v1\Controller
 * @author Mohammed Yehia <firefoxegy@gmail.com>
 */
class HealthController extends AbstractFOSRestController
{
    /**
     * Tracks the user conversion and distribute revenue on platforms
     * @Rest\Get("/health")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function index()
    {
        return $this->handleView($this->view(null, Response::HTTP_OK));
    }
}
