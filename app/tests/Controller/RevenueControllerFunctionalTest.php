<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RevenueControllerFunctionalTest extends WebTestCase
{
    /**
     * @var KernelBrowser
     */
    protected $client;

    protected function setUp()
    {
        $this->client = static::createClient();
    }

    public function testGetRevenuesDistributionsTotalSumByPlatform()
    {
        $client = $this->client;
        $client->request('GET', '/revenues/distributions/total-sum/test_tripadvisor', [], [], []);

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $responseContent = json_decode($response->getContent(), true);

        $this->assertEquals('test_tripadvisor', $responseContent['platform']);
        $this->assertEquals(300, $responseContent['amount']);
    }

    public function testGetRevenuesDistributionsTotalSumByPlatformNotFound()
    {
        $client = $this->client;
        $client->request('GET', '/revenues/distributions/total-sum/asd', [], [], []);

        $response = $client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEmpty(json_decode($response->getContent(), true));
    }

    public function testGetRevenueDistributions()
    {
        $client = $this->client;
        $client->request('GET', '/revenues/distributions', [], [], []);

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty(json_decode($response->getContent(), true)['_embedded']['items']);
    }

    public function testGetRevenueDistributionsWithUnknownConversionId()
    {
        $client = $this->client;
        $client->request('GET', '/revenues/distributions', ['conversionId' => 5986], [], []);

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEmpty(json_decode($response->getContent(), true)['_embedded']['items']);
    }
}