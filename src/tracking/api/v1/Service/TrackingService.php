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

class TrackingService
{
    const MESSAGE_CUSTOMER_NOT_FOUND = 'Customer not found';
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
     * @param int $customerId
     * @return Customer
     */
    private function getCustomer(int $customerId)
    {
        return $this->customerRepository->find($customerId);
    }

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
                $platform = $this->platformRepository->find($platformPlacement['platform']);
                $revenueRecord = $this->createRevenue(
                    $customer,
                    $platform,
                    $bookingReference,
                    $revenueShare,
                    new \DateTimeImmutable($platformPlacement['date_of_contact'])
                );
                $this->platformRevenueRepository->save($revenueRecord);
            }
        }
    }

    /**
     * @param Customer $customer
     * @param Platform $platform
     * @param string $bookingReference
     * @param int $revenueShare
     * @param \DateTimeImmutable $created
     * @return PlatformRevenue
     */
    private function createRevenue(
        Customer $customer,
        Platform $platform,
        string $bookingReference,
        int $revenueShare,
        \DateTimeImmutable $created
    ): PlatformRevenue {
        $record = new PlatformRevenue();
        $record->setBookingReference($bookingReference);
        $record->setCreated($created);
        $record->setCustomer($customer);
        $record->setPlatform($platform);
        $record->setRevenue($revenueShare);
        return $record;
    }
}
