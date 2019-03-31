<?php
namespace App\Tests\unit\service;

use App\tracking\api\v1\Repository\CustomerRepository;
use App\tracking\api\v1\Repository\PlatformRepository;
use App\tracking\api\v1\Repository\PlatformRevenueRepository;
use App\tracking\api\v1\Service\TrackingService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\DBAL\DBALException;

class TrackingServiceTest extends KernelTestCase
{
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

    public function testCheckPlatform()
    {
        $this->expectException(NotFoundHttpException::class);
        $this->generateMocks()->checkPlatform(1000);
    }

    /**
     * @throws DBALException
     */
    public function testGetConversionsOfPlatform()
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
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function testGetRevenueByPlatform()
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
     * @throws DBALException
     */
    public function testGetMostAttractedPlatform()
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