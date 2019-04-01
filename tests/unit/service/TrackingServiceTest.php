<?php
namespace App\Tests\unit\service;

use App\tracking\api\v1\Repository\CustomerRepository;
use App\tracking\api\v1\Repository\PlatformRepository;
use App\tracking\api\v1\Repository\PlatformRevenueRepository;
use App\tracking\api\v1\Service\TrackingService;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\DBAL\DBALException;

/**
 * Class TrackingServiceTest
 * @package App\Tests\unit\service
 */
class TrackingServiceTest extends TestCase
{
    /**
     * Creates a TrackingService mock for testing purposes
     * @param null $methodName
     * @param null $expected
     * @return TrackingService
     */
    private function generateMocks($methodName = null, $expected = null)
    {
        $customerRepository = $this->createMock(CustomerRepository::class);
        $platformRepository = $this->createMock(PlatformRepository::class);
        $platformRevenueRepository = $this->createMock(PlatformRevenueRepository::class);
        if ($methodName && $expected) {
            $platformRevenueRepository->method($methodName)->willReturn($expected);
        }
        return new TrackingService(
            $customerRepository,
            $platformRepository,
            $platformRevenueRepository
        );
    }

    /**
     * @test
     */
    public function checkPlatform()
    {
        $this->expectException(NotFoundHttpException::class);
        $this->generateMocks()->checkPlatform(1000);
    }

    /**
     * @test
     * @throws DBALException
     */
    public function getConversionsOfPlatform()
    {
        $this->assertEquals(
            4,
            $this->generateMocks(
                'getConversionOfPlatform',
                (object) ['conversion' => 4]
            )->getConversionsOfPlatform(1)
        );
    }

    /**
     * @test
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getRevenueByPlatform()
    {
        $this->assertEquals(
            8,
            $this->generateMocks(
                'getRevenueForPlatform',
                ['amount' => 8]
            )->getRevenueByPlatform(1)
        );
    }

    /**
     * @test
     * @throws DBALException
     */
    public function getMostAttractedPlatform()
    {
        $this->assertEquals(
            'TripAdvisor',
            $this->generateMocks(
                'getMostAttractedPlatform',
                (object) ['name' => 'TripAdvisor']
            )->getMostAttractedPlatform()
        );
    }
}