<?php
namespace App\Tests\unit\controller;

use App\tracking\api\v1\Service\TrackingService;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\ClientException;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TrackingControllerTest
 * @package App\Tests\unit\controller
 */
class TrackingControllerTest extends KernelTestCase
{
    /**
     * @var string The API URL
     */
    const API_URL = 'http://localhost:8088/api/v1';

    /**
     * @var int the request timeout in seconds
     */
    const API_TIMEOUT = 10;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $token;

    /**
     * Booking seeder
     * @return array
     * @throws \Exception
     */
    public function seedBookings()
    {
        return [
            [[
                'customerId' => 2,
                'bookingReference' => (Uuid::uuid4())->toString()
            ]],
            [[
                'revenue' => 6,
                'bookingReference' => (Uuid::uuid4())->toString()
            ]],
            [[
                'revenue' => 6,
                'customerId' => 2
            ]],
        ];
    }

    /**
     * Initialize the adapter
     */
    public function setUp()
    {
        $config = [
            'timeout' => self::API_TIMEOUT
        ];
        $this->client = new Client($config);
        self::bootKernel();
        $container = self::$kernel->getContainer();
        $this->token = $container->get('lexik_jwt_authentication.encoder')->encode(['username' => 'mohammed']);
    }

    /**
     * Health checking
     * @test
     */
    public function apiIsHealthy()
    {
        $url = self::API_URL . '/health';
        $response = $this->client->request('GET', $url, [
            'headers' => [
                'Authorization' => ('Bearer ' . $this->token)
            ]
        ]);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @test
     * @depends apiIsHealthy
     */
    public function platform()
    {
        $url = self::API_URL . '/platform';
        $response = $this->client->request('GET', $url, [
            'headers' => [
                'Authorization' => ('Bearer ' . $this->token)
            ]
        ]);
        $content = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('TripAdvisor', $content['platform']);
    }

    /**
     * @test
     * @depends apiIsHealthy
     */
    public function revenue()
    {
        $url = self::API_URL . '/revenue';
        $response = $this->client->request(
            'GET',
            $url,
            [
                'query' => [
                    'platform' => 3
                ],
                'headers' => [
                    'Authorization' => ('Bearer ' . $this->token)
                ]
            ]
        );
        $content = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals(10, $content['revenue']);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        try {
            $this->client->request(
                'GET',
                $url,
                [
                    'query' => [
                        'platform' => 4
                    ],
                    'headers' => [
                        'Authorization' => ('Bearer ' . $this->token)
                    ]
                ]
            );
        } catch (ClientException $e) {
            $content = json_decode($e->getResponse()->getBody()->getContents(), true);
            $this->assertEquals(TrackingService::MESSAGE_PLATFORM_NOT_FOUND, $content['message']);
            $this->assertEquals(Response::HTTP_NOT_FOUND, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * @test
     * @depends apiIsHealthy
     */
    public function conversion()
    {
        $url = self::API_URL . '/conversion';
        $response = $this->client->request(
            'GET',
            $url,
            [
                'query' => [
                    'platform' => 1
                ],
                'headers' => [
                    'Authorization' => ('Bearer ' . $this->token)
                ]
            ]
        );
        $content = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals(2, $content['conversion']);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        try {
            $this->client->request(
                'GET',
                $url,
                [
                    'query' => [
                        'platform' => 4
                    ],
                    'headers' => [
                        'Authorization' => ('Bearer ' . $this->token)
                    ]
                ]
            );
        } catch (ClientException $e) {
            $content = json_decode($e->getResponse()->getBody()->getContents(), true);
            $this->assertEquals(TrackingService::MESSAGE_PLATFORM_NOT_FOUND, $content['message']);
            $this->assertEquals(Response::HTTP_NOT_FOUND, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * @test
     * @depends apiIsHealthy
     */
    public function track()
    {
        $booking = [
            'revenue' => 6,
            'customerId' => 2,
            'bookingReference' => (Uuid::uuid4())->toString()
        ];


        $url = self::API_URL . '/track';
        $cookie = CookieJar::fromArray([
            'tracking' => '{
                "placements": [
                    {"platform": 1, "customer_id": 2, "date_of_contact": "2018-01-01 14:00:00"}, 
                    {"platform": 2, "customer_id": 2, "date_of_contact": "2018-01-03 14:00:00"}, 
                    {"platform": 3, "customer_id": 2, "date_of_contact": "2018-01-05 14:00:00"}
                ]
            }'
        ], 'localhost');

        $response = $this->client->request(
            'GET',
            $url,
            [
                'cookies' => $cookie,
                'query' => $booking,
                'headers' => [
                    'Authorization' => ('Bearer ' . $this->token)
                ]
            ]
        );
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        try {
            $this->client->request(
                'GET',
                $url,
                [
                    'query' => $booking,
                    'headers' => [
                        'Authorization' => ('Bearer ' . $this->token)
                    ]
                ]
            );
        } catch (ClientException $e) {
            $content = json_decode($e->getResponse()->getBody()->getContents(), true);
            $this->assertEquals(TrackingService::MESSAGE_COOKIE_NOT_FOUND, $content['message']);
            $this->assertEquals(Response::HTTP_BAD_REQUEST, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * @test
     * @depends apiIsHealthy
     * @dataProvider seedBookings
     */
    public function wrongTrack($booking)
    {
        $url = self::API_URL . '/track';
        try {
            $this->client->request(
                'GET',
                $url,
                [
                    'query' => $booking,
                    'headers' => [
                        'Authorization' => ('Bearer ' . $this->token)
                    ]
                ]
            );
        } catch (ClientException $e) {
            $this->assertEquals(Response::HTTP_BAD_REQUEST, $e->getResponse()->getStatusCode());
        }
    }
}