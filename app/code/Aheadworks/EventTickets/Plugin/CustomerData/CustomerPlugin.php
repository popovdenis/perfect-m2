<?php
namespace Aheadworks\EventTickets\Plugin\CustomerData;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\CustomerData\Customer;

/**
 * Class CustomerPlugin
 *
 * @package Aheadworks\EventTickets\Plugin\CustomerData
 */
class CustomerPlugin
{
    /**
     * @var CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @param CurrentCustomer $currentCustomer
     */
    public function __construct(
        CurrentCustomer $currentCustomer
    ) {
        $this->currentCustomer = $currentCustomer;
    }

    /**
     * @param Customer $subject
     * @param string[] $result
     * @return string[]
     */
    public function afterGetSectionData($subject, $result)
    {
        if (!$this->currentCustomer->getCustomerId()) {
            return $result;
        }
        $customer = $this->currentCustomer->getCustomer();
        $result['email'] = $customer->getEmail();
        $result['phone'] = $this->resolveCustomerPhone($customer);

        return $result;
    }

    /**
     * Resolve customer phone
     *
     * @param CustomerInterface $customer
     * @return string
     */
    private function resolveCustomerPhone($customer)
    {
        $phone = '';
        $addresses = $customer->getAddresses();
        if (is_array($addresses)) {
            foreach ($addresses as $address) {
                if ($address->isDefaultBilling()) {
                    $phone = $address->getTelephone();
                    break;
                }
            }
        }

        return $phone;
    }
}
