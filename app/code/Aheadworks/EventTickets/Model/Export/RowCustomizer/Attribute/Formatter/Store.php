<?php
namespace Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Store
 *
 * @package Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter
 */
class Store implements FormatterInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormattedValue($value)
    {
        try {
            $formattedValue = $this->storeManager->getStore($value)->getName();
        } catch (NoSuchEntityException $exception) {
            $formattedValue = '';
        }
        return $formattedValue;
    }
}
