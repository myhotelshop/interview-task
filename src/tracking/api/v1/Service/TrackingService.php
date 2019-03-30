<?php
namespace App\tracking\api\v1\Service;

use App\tracking\api\v1\Repository\CustomerRepository;

class TrackingService
{
    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function getCustomers()
    {
        return $this->customerRepository->findAll();
    }
}
