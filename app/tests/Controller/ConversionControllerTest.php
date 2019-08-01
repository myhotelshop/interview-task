<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConversionControllerTest extends WebTestCase
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
        $client = $this->client;
        $client->request('GET', '/conversions/new', [], [], []);

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('application/json', $response->headers->get('Content-Type'));
    }

}