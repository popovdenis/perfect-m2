<?php
namespace Aheadworks\EventTickets\Model\Quote\Cart\Resolver;

use Aheadworks\EventTickets\Model\Quote\Cart\Resolver\Magento23\GuestCartResolver;
use Aheadworks\EventTickets\Model\Quote\Cart\Resolver\Magento23\CustomerCartResolver;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote;

class Magento23 implements ResolverInterface
{
    /**
     * @var GuestCartResolver
     */
    private $guestCartResolver;

    /**
     * @var CustomerCartResolver
     */
    private $customerCartResolver;

    /**
     * @param GuestCartResolver $guestCartResolver
     * @param CustomerCartResolver $customerCartResolver
     */
    public function __construct(
        GuestCartResolver $guestCartResolver,
        CustomerCartResolver $customerCartResolver
    ) {
        $this->guestCartResolver = $guestCartResolver;
        $this->customerCartResolver = $customerCartResolver;
    }

    /**
     * @inheritdoc
     */
    public function getForGuest()
    {
        return $this->guestCartResolver->resolve();
    }

    /**
     * @inheritdoc
     */
    public function getForCustomer(int $customerId)
    {
        return $this->customerCartResolver->resolve($customerId);
    }
}
