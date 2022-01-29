<?php
namespace Aheadworks\EventTickets\Api\Data;

/**
 * Interface IsAvailableResultInterface
 * @package Aheadworks\EventTickets\Api\Data
 * @api
 */
interface IsAvailableResultInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Check if product available
     *
     * @return bool
     */
    public function isSalable();

    /**
     * Get errors
     *
     * @return array
     */
    public function getErrors();

    /**
     * Retrieve existing extension attributes object
     *
     * @return \Aheadworks\EventTickets\Api\Data\IsAvailableResultExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\EventTickets\Api\Data\IsAvailableResultExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(
        \Aheadworks\EventTickets\Api\Data\IsAvailableResultExtensionInterface $extensionAttributes
    );
}
