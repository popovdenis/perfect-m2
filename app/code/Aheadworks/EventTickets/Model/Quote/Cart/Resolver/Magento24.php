<?php
namespace Aheadworks\EventTickets\Model\Quote\Cart\Resolver;

use Magento\Framework\ObjectManagerInterface;
use Magento\Quote\Model\Cart\CustomerCartResolver;
use Magento\Quote\Model\GuestCart\GuestCartResolver;

class Magento24 implements ResolverInterface
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @inheritdoc
     */
    public function getForGuest()
    {
        /** @var GuestCartResolver $guestCartResolver */
        $guestCartResolver = $this->objectManager->create(GuestCartResolver::class);
        return $guestCartResolver->resolve();
    }

    /**
     * @inheritdoc
     */
    public function getForCustomer(int $customerId)
    {
        /** @var CustomerCartResolver $customerCartResolver */
        $customerCartResolver = $this->objectManager->create(CustomerCartResolver::class);
        return $customerCartResolver->resolve($customerId);
    }
}
