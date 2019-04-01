<?php
namespace App\tracking\api\v1\Service;

use App\tracking\api\v1\Entity\Customer;
use App\tracking\api\v1\Entity\Platform;
use App\tracking\api\v1\Entity\PlatformRevenue;
use App\tracking\api\v1\Repository\CustomerRepository;
use App\tracking\api\v1\Repository\PlatformRepository;
use App\tracking\api\v1\Repository\PlatformRevenueRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use FOS\RestBundle\Exception\InvalidParameterException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;
use DateTimeImmutable;

/**
 * Class TrackingService
 * @package App\tracking\api\v1\Service
 * @author Mohammed Yehia <firefoxegy@gmail.com>
 */
class TrackingService
{
    /**
     * @var string this message gets thrown if the customer is not registered in the tracking system
     */
    const MESSAGE_CUSTOMER_NOT_FOUND = 'Customer not found';

    /**
     * @var string this message gets thrown if the platform is not registered in the tracking system
     */
    const MESSAGE_PLATFORM_NOT_FOUND = 'Platform not found';

    /**
     * @var string this message gets thrown in case the tracking cookie is not provided
     */
    const MESSAGE_COOKIE_NOT_FOUND = 'Tracking cookie not found';
    /**
     * @var CustomerRepository
     */
    private $customerRepository;
    /**
     * @var PlatformRepository
     */
    private $platformRepository;
    /**
     * @var PlatformRevenueRepository
     */
    private $platformRevenueRepository;

    /**
     * TrackingService constructor.
     * @param CustomerRepository $customerRepository
     * @param PlatformRepository $platformRepository
     * @param PlatformRevenueRepository $platformRevenueRepository
     */
    public function __construct(
        CustomerRepository $customerRepository,
        PlatformRepository $platformRepository,
        PlatformRevenueRepository $platformRevenueRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->platformRepository = $platformRepository;
        $this->platformRevenueRepository = $platformRevenueRepository;
    }

    /**
     * Check if the customer is a valid customer
     * @param int $customerId
     * @return object
     */
    private function getCustomer(int $customerId)
    {
        return $this->customerRepository->find($customerId);
    }

    /**
     * Check if the platform is a valid platform
     * @param int $platform
     * @return bool
     */
    public function checkPlatform(int $platform)
    {
        if (!$this->platformRepository->find($platform)) {
            throw new NotFoundHttpException(self::MESSAGE_PLATFORM_NOT_FOUND);
        }
        return true;
    }

    /**
     * Check if the request to te /track endpoint is valid
     * @param int $customerId
     * @param $cookie
     */
    public function isValidRequest(int $customerId, $cookie)
    {
        if (!$cookie) {
            throw new InvalidParameterException(self::MESSAGE_COOKIE_NOT_FOUND);
        }

        if (!$this->getCustomer($customerId)) {
            throw new NotFoundHttpException(self::MESSAGE_CUSTOMER_NOT_FOUND);
        }
    }

    /**
     * Distribute the revenue on the platforms according to their point of contacts
     * @param int $customerId
     * @param string $cookie
     * @param string $bookingReference
     * @param int $revenue
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function distributeRevenue(int $customerId, string $cookie, string $bookingReference, int $revenue)
    {
        $customer = $this->getCustomer($customerId);
        $placements = json_decode($cookie, true);

        if (is_array($placements) &&
            array_key_exists('placements', $placements) &&
            !empty($placements['placements'])
        ) {
            $placementsData = $placements['placements'];
            array_multisort(
                array_column($placementsData, 'date_of_contact'),
                SORT_ASC,
                $placementsData
            );
            $revenueShare = floor(($revenue / count($placementsData)));
            foreach ($placementsData as $platformPlacement) {
                if ($customerId !== $platformPlacement['customer_id']) {
                    continue;
                }
                /** @var Platform $platform */
                $platform = $this->platformRepository->find($platformPlacement['platform']);
                $this->createRevenue(
                    $customer,
                    $platform,
                    $bookingReference,
                    $revenueShare,
                    new DateTimeImmutable($platformPlacement['date_of_contact'])
                );
            }
        }
    }

    /**
     * Creates a PlatformRevenue object
     * @param Customer $customer
     * @param Platform $platform
     * @param string $bookingReference
     * @param int $revenueShare
     * @param DateTimeImmutable $created
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function createRevenue(
        Customer $customer,
        Platform $platform,
        string $bookingReference,
        int $revenueShare,
        DateTimeImmutable $created
    ): void {
        $this->platformRevenueRepository->save(new PlatformRevenue(
            $customer,
            $platform,
            $bookingReference,
            $revenueShare,
            $created
        ));
    }

    /**
     * Return the platform that most attracts customers
     * @return string|null
     * @throws DBALException
     */
    public function getMostAttractedPlatform(): ?string
    {
        $platform = $this->platformRevenueRepository->getMostAttractedPlatform();
        return $platform !== null ? $platform->name : null;
    }

    /**
     * Return the revenue of a platform
     * @param int $platform
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getRevenueByPlatform(int $platform): int
    {
        $revenue = $this->platformRevenueRepository->getRevenueForPlatform($platform);
        return $revenue === null ? 0 : $revenue['amount'];
    }

    /**
     * Return the number of conversions for a given platform
     * @param int $platform
     * @return int
     * @throws DBALException
     */
    public function getConversionsOfPlatform(int $platform): int
    {
        $conversion = $this->platformRevenueRepository->getConversionOfPlatform($platform);
        return $conversion ? (int) $conversion->conversion : 0;
    }
}
