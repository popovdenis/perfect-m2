<?php
namespace Aheadworks\EventTickets\Model\Export\Product\Type;

use Magento\CatalogImportExport\Model\Export\Product\Type\AbstractType;
use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;

/**
 * Class EventTicket
 *
 * @package Aheadworks\EventTickets\Model\Export\Product\Type
 */
class EventTicket extends AbstractType
{
    /**
     * Array of attributes codes which are disabled for export by native export logic
     * Instead of exporting within 'additional_attributes' column, those attributes are rendered in separate columns
     *
     * @var string[]
     */
    protected $_disabledAttrs = [
        ProductAttributeInterface::CODE_AW_ET_SECTOR_CONFIG,
        ProductAttributeInterface::CODE_AW_ET_PERSONAL_OPTIONS,
    ];
}
