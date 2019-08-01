<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConversionControllerFunctionalTest extends WebTestCase
{
    /**
     * @var KernelBrowser
     */
    protected $client;

    protected function setUp()
    {
        $this->client = static::createClient();
    }

    public function testGetConversionsNew()
    {
        $tests = [
            [
                'name' => 'test 1: request is valid, expects ok response',
                'path' => '/conversions/new?customerId=1&bookingNumber=1234&revenue=100',
                'expectedStatusCode' => 200
            ],
            [
                'name' => 'test 2: request has no customer Id, expects bad request response',
                'path' => '/conversions/new?bookingNumber=1234&revenue=100',
                'expectedStatusCode' => 400
            ],
            [
                'name' => 'test 3: request has no booking number, expects bad request response',
                'path' => '/conversions/new?customerId=1&revenue=100',
                'expectedStatusCode' => 400
            ],
            [
                'name' => 'test 4: request has no revenue, expects bad request response',
                'path' => '/conversions/new?bookingNumber=1234&customerId=1',
                'expectedStatusCode' => 400
            ],
        ];

        foreach ($tests as $test) {
            $client = $this->client;
            $client->request('GET', $test['path'], [], [], []);

            $response = $client->getResponse();
            $this->assertEquals($test['expectedStatusCode'], $response->getStatusCode());
        }
    }

    public function testGetConversions()
    {
        $client = $this->client;
        $client->request('GET', '/conversions', [], [], []);

        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty(json_decode($response->getContent(), true)['_embedded']['items']);
    }

    public function testGetConversionsWithPlatformParam()
    {
        $client = $this->client;
        $client->request('GET', '/conversions', ['platform' => 'test_tripadvisor'], [], []);

        $response = $client->getResponse();
        $conversions = json_decode($response->getContent(), true)['_embedded'];

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($conversions['items']);

        foreach ($conversions as $conversion) {
            $this->assertEquals('test_tripadvisor', $conversion['platform']);
        }
    }

    public function testGetConversionsWithUnknownPlatformParam()
    {
        $client = $this->client;
        $client->request('GET', '/conversions', ['platform' => 'sdfsdfsd'], [], []);

        $response = $client->getResponse();
        $conversions = json_decode($response->getContent(), true)['_embedded'];

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEmpty($conversions['items']);
    }

    public function testGetConversionsWithUnknownParam()
    {
        $client = $this->client;
        $client->request('GET', '/conversions', ['bla' => 'asd'], [], []);

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty(json_decode($response->getContent(), true)['_embedded']['items']);
    }

}