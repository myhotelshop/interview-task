<?php
namespace App\Controller\api\v1;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Response;

class TestController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/index")
     * @Rest\QueryParam(name="revenue", requirements="\d+", description="Page of the overview.")
     */
    public function index(ParamFetcher $paramFetcher)
    {
        $revenue = $paramFetcher->get('revenue');
        return $this->handleView($this->view(['revenue' => $revenue], Response::HTTP_CREATED));
    }
}
