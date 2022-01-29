<?php
namespace Aheadworks\EventTickets\Model\Quote\Cart\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote;

interface ResolverInterface
{
    /**
     * Return quote for the guest
     *
     * @return Quote
     * @throws LocalizedException
     */
    public function getForGuest();

    /**
     * Return quote for the registered customer
     *
     * @param int $customerId
     * @return Quote
     * @throws LocalizedException
     */
    public function getForCustomer(int $customerId);
}
