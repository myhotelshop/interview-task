<?php
namespace App\tracking\api\v1\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;

/**
 * Class HealthController
 * @package App\tracking\api\v1\Controller
 * @author Mohammed Yehia <firefoxegy@gmail.com>
 */
class AuthController extends AbstractFOSRestController
{
    /**
     * Tracks the user conversion and distribute revenue on platforms
     * @Rest\Get("/auth")
     * @param JWTEncoderInterface $encoder
     * @return Response
     * @throws JWTEncodeFailureException ;
     */
    public function index(JWTEncoderInterface $encoder)
    {
        $token = 'Bearer ' . $encoder->encode(['username' => 'mohammed']);
        return new JsonResponse(
            ['token' => $token]
        );
    }
}
