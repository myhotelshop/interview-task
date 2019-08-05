<?php


namespace App\Model;

use App\Entity\Conversion;
use App\Entity\RevenueDistribution;
use App\Entity\TotalRevenueDistribution;
use App\Entity\Visit;
use App\Entity\VisitCollection;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RevenueModelIntegrationTest extends WebTestCase
{
    /**
     * @var RevenueModel
     */
    private $model;

    protected function setUp()
    {
        self::bootKernel();
        $this->model = self::$container->get('App\Model\RevenueModelInterface');
    }

    /**
     *
     */
    public function testGetTotalRevenueDistributionByPlatform()
    {
        $result = $this->model->getTotalRevenueDistributionByPlatform('test_tripadvisor');

        $this->assertInstanceOf(TotalRevenueDistribution::class, $result);
        $this->assertEquals(350, $result->getAmount());
        $this->assertEquals('test_tripadvisor', $result->getPlatform());
    }

    public function testGetTotalRevenueDistributionByPlatformWithNotFound()
    {
        $result = $this->model->getTotalRevenueDistributionByPlatform('bla');

        $this->assertNull($result);
    }

    public function testDistributeWithOneVisit()
    {
        $visits = [
            (new Visit())
                ->setPlatform("platformA")
                ->setCustomerId(1)
                ->setDate(new DateTime('2019-01-05 23:00'))
        ];

        $conversion = (new Conversion)
            ->setRevenue(1000);

        $expectedDistribution = (new RevenueDistribution())
            ->setPlatform('platformA')
            ->setConversion($conversion)
            ->setAmount(1000);

        $result = $this->model->distribute($conversion, new VisitCollection($visits));

        $this->assertCount(1, $result);

        /** @var RevenueDistribution $distribution */
        $distribution = $result[0];
        $this->assertInstanceOf(RevenueDistribution::class, $distribution);
        $this->assertEquals($expectedDistribution, $distribution);
    }

    public function testDistributeWithTwoVisits()
    {
        $visits = [
            (new Visit())
                ->setPlatform("platformA")
                ->setCustomerId(1)
                ->setDate(new DateTime('2019-01-05 23:00')),
            (new Visit())
                ->setPlatform("platformB")
                ->setCustomerId(1)
                ->setDate(new DateTime('2019-01-01 23:00')),
        ];

        $conversion = (new Conversion)
            ->setRevenue(1000);

        $expectedDistributions = [
            'platformA' => (new RevenueDistribution())
                ->setPlatform('platformA')
                ->setConversion($conversion)
                ->setAmount(450)
            ,
            'platformB' => (new RevenueDistribution())
                ->setPlatform('platformB')
                ->setConversion($conversion)
                ->setAmount(550)
            ,
        ];

        $result = $this->model->distribute($conversion, new VisitCollection($visits));

        $this->assertCount(count($expectedDistributions), $result);

        /** @var RevenueDistribution $distribution */
        foreach ($result as $distribution) {
            $expectedDistribution = $expectedDistributions[$distribution->getPlatform()];

            $this->assertInstanceOf(RevenueDistribution::class, $distribution);
            $this->assertEquals($expectedDistribution, $distribution);
        }
    }

    public function testDistributeWithFourVisits()
    {
        $visits = [
            (new Visit())
                ->setPlatform("platformA")
                ->setCustomerId(1)
                ->setDate(new DateTime('2019-01-05 23:00')),
            (new Visit())
                ->setPlatform("platformB")
                ->setCustomerId(1)
                ->setDate(new DateTime('2019-01-01 23:00')),
            (new Visit())
                ->setPlatform("platformC")
                ->setCustomerId(1)
                ->setDate(new DateTime('2019-01-03 23:00')),
            (new Visit())
                ->setPlatform("platformD")
                ->setCustomerId(1)
                ->setDate(new DateTime('2019-01-03 22:00')),
        ];

        $conversion = (new Conversion)
            ->setRevenue(1000);

        $expectedDistributions = [
            'platformA' => (new RevenueDistribution())
                ->setPlatform('platformA')
                ->setConversion($conversion)
                ->setAmount(350),
            'platformB' => (new RevenueDistribution())
                ->setPlatform('platformB')
                ->setConversion($conversion)
                ->setAmount(450),
            'platformC' => (new RevenueDistribution())
                ->setPlatform('platformC')
                ->setConversion($conversion)
                ->setAmount(100),
            'platformD' => (new RevenueDistribution())
                ->setPlatform('platformD')
                ->setConversion($conversion)
                ->setAmount(100),
        ];

        $result = $this->model->distribute($conversion, new VisitCollection($visits));

        $this->assertCount(count($expectedDistributions), $result);

        foreach ($result as $distribution) {
            /** @var RevenueDistribution $distribution */
            $this->assertInstanceOf(RevenueDistribution::class, $distribution);
            $this->assertEquals($expectedDistributions[$distribution->getPlatform()], $distribution);
        }
    }
}