<?php
namespace Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\SectorConfig;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Api\Data\ProductSectorInterface;
use Aheadworks\EventTickets\Api\Data\ProductSectorTicketInterface;
use Magento\CatalogImportExport\Model\Import\Product as ImportProduct;
use Magento\ImportExport\Model\Import;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\SectorConfig;
use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\FormatterPool as AttributeFormatterPool;

/**
 * Class TicketType
 *
 * @package Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\SectorConfig
 */
class TicketType
{
    /**#@+
     * Constants defined for names of corresponding columns
     */
    const SECTOR_TICKET_TYPE_COLUMN_ID =
        ProductAttributeInterface::CODE_AW_ET_SECTOR_CONFIG . '_sector_ticket_type';
    const SECTOR_TICKET_TYPE_EARLY_BIRD_PRICE_COLUMN_ID =
        ProductAttributeInterface::CODE_AW_ET_SECTOR_CONFIG . '_sector_ticket_type_early_bird_price';
    const SECTOR_TICKET_TYPE_PRICE_COLUMN_ID =
        ProductAttributeInterface::CODE_AW_ET_SECTOR_CONFIG . '_sector_ticket_type_price';
    const SECTOR_TICKET_TYPE_LAST_DAYS_PRICE_COLUMN_ID =
        ProductAttributeInterface::CODE_AW_ET_SECTOR_CONFIG . '_sector_ticket_type_last_days_price';
    const SECTOR_TICKET_TYPE_POSITION_COLUMN_ID =
        ProductAttributeInterface::CODE_AW_ET_SECTOR_CONFIG . '_sector_ticket_type_position';
    const SECTOR_TICKET_TYPE_PERSONAL_OPTIONS_COLUMN_ID =
        ProductAttributeInterface::CODE_AW_ET_SECTOR_CONFIG . '_sector_ticket_type_personal_options';
    /**#@-*/

    /**
     * @var array
     */
    private $sectorConfigTicketTypeColumns = [
        self::SECTOR_TICKET_TYPE_COLUMN_ID,
        self::SECTOR_TICKET_TYPE_EARLY_BIRD_PRICE_COLUMN_ID,
        self::SECTOR_TICKET_TYPE_PRICE_COLUMN_ID,
        self::SECTOR_TICKET_TYPE_LAST_DAYS_PRICE_COLUMN_ID,
        self::SECTOR_TICKET_TYPE_POSITION_COLUMN_ID,
        self::SECTOR_TICKET_TYPE_PERSONAL_OPTIONS_COLUMN_ID,
    ];

    /**
     * @var AttributeFormatterPool
     */
    private $attributeFormatterPool;

    /**
     * @param AttributeFormatterPool $attributeFormatterPool
     */
    public function __construct(
        AttributeFormatterPool $attributeFormatterPool
    ) {
        $this->attributeFormatterPool = $attributeFormatterPool;
    }

    /**
     * Prepare data for export
     *
     * @param ProductSectorInterface[] $productSectorObjects
     * @return array
     */
    public function getPreparedProductData($productSectorObjects)
    {
        $preparedProductData = [];
        foreach ($this->sectorConfigTicketTypeColumns as $columnName) {
            $preparedProductData[$columnName] = '';
        }
        $ticketTypeFormatter = $this->attributeFormatterPool->getByAttributePath(
            ProductAttributeInterface::CODE_AW_ET_SECTOR_CONFIG . '/sector_tickets/type_id'
        );

        /** @var ProductSectorInterface $productSectorObject */
        foreach ($productSectorObjects as $productSectorObject) {
            /** @var ProductSectorTicketInterface $sectorTicket */
            foreach ($productSectorObject->getSectorTickets() as $sectorTicket) {
                $preparedProductData[self::SECTOR_TICKET_TYPE_COLUMN_ID] .=
                    $ticketTypeFormatter->getFormattedValue($sectorTicket->getTypeId())
                    . ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR;
                $preparedProductData[self::SECTOR_TICKET_TYPE_EARLY_BIRD_PRICE_COLUMN_ID] .=
                    $sectorTicket->getEarlyBirdPrice()
                    . ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR;
                $preparedProductData[self::SECTOR_TICKET_TYPE_PRICE_COLUMN_ID] .=
                    $sectorTicket->getPrice()
                    . ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR;
                $preparedProductData[self::SECTOR_TICKET_TYPE_LAST_DAYS_PRICE_COLUMN_ID] .=
                    $sectorTicket->getLastDaysPrice()
                    . ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR;
                $preparedProductData[self::SECTOR_TICKET_TYPE_POSITION_COLUMN_ID] .=
                    $sectorTicket->getPosition()
                    . ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR;
                $preparedProductData[self::SECTOR_TICKET_TYPE_PERSONAL_OPTIONS_COLUMN_ID] .=
                    $this->getPersonalOptionsFormattedValues($sectorTicket->getPersonalOptionUids())
                    . ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR;
            }
            foreach ($this->sectorConfigTicketTypeColumns as $columnName) {
                $preparedProductData[$columnName] .= SectorConfig::SECTORS_SEPARATOR;
            }
        }

        return $preparedProductData;
    }

    /**
     * Retrieve headers columns
     *
     * @return array
     */
    public function getHeaderColumns()
    {
        return $this->sectorConfigTicketTypeColumns;
    }

    /**
     * Retrieve string of formatted personal options
     *
     * @param array $personalOptionUids
     * @return string
     */
    private function getPersonalOptionsFormattedValues($personalOptionUids)
    {
        $personalOptionFormatter = $this->attributeFormatterPool->getByAttributePath(
            ProductAttributeInterface::CODE_AW_ET_SECTOR_CONFIG . '/sector_tickets/personal_option_uid'
        );
        $formattedPersonalOptionValues = [];
        foreach ($personalOptionUids as $optionUid) {
            $formattedPersonalOptionValues[] = $personalOptionFormatter->getFormattedValue($optionUid);
        }
        return implode(
            Import::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR,
            $formattedPersonalOptionValues
        );
    }
}
