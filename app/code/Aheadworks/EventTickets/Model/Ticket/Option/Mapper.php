<?php
namespace Aheadworks\EventTickets\Model\Ticket\Option;

use Aheadworks\EventTickets\Api\Data\TicketOptionInterface;
use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionInterface;
use Aheadworks\EventTickets\Model\ResourceModel\Ticket\Option\Repository as TicketOptionsRepository;
use Magento\Ui\Component\Form;

/**
 * Class Mapper
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Option
 */
class Mapper
{
    /**
     * @var array
     */
    private $dataTypeElementMap = [
        'default'                                                   => Form\Element\DataType\Text::NAME,
        ProductPersonalOptionInterface::OPTION_TYPE_NAME            => Form\Element\DataType\Text::NAME,
        ProductPersonalOptionInterface::OPTION_TYPE_EMAIL           => Form\Element\DataType\Text::NAME,
        ProductPersonalOptionInterface::OPTION_TYPE_PHONE_NUMBER    => Form\Element\DataType\Text::NAME,
        ProductPersonalOptionInterface::OPTION_TYPE_FIELD           => Form\Element\DataType\Text::NAME,
        ProductPersonalOptionInterface::OPTION_TYPE_DROPDOWN        => Form\Element\Select::NAME,
        ProductPersonalOptionInterface::OPTION_TYPE_DATE            => Form\Element\DataType\Date::NAME,
    ];

    /**
     * @var array
     */
    private $gridElementMap = [
        'default'                           => 'Magento_Ui/js/grid/columns/column',
        Form\Element\DataType\Text::NAME    => 'Magento_Ui/js/grid/columns/column',
        Form\Element\Select::NAME           => 'Magento_Ui/js/grid/columns/select',
        Form\Element\DataType\Date::NAME    => 'Magento_Ui/js/grid/columns/date'
    ];

    /**
     * @var array
     */
    private $gridFilterMap = [
        'default'                           => 'text',
        Form\Element\DataType\Text::NAME    => 'text',
        Form\Element\Select::NAME           => 'select',
        Form\Element\DataType\Date::NAME    => 'dateRange'
    ];

    /**
     * @var TicketOptionsRepository
     */
    private $ticketOptionsRepository;

    /**
     * @param TicketOptionsRepository $ticketOptionsRepository
     */
    public function __construct(
        TicketOptionsRepository $ticketOptionsRepository
    ) {
        $this->ticketOptionsRepository = $ticketOptionsRepository;
    }

    /**
     * Map custom field attribute
     *
     * @param array $customFieldData
     * @return array
     */
    public function map($customFieldData)
    {
        $result = [];
        $fieldType = $customFieldData[TicketOptionInterface::TYPE];
        $fieldDataType = $this->getFieldDataType($fieldType);
        $result['dataType'] = $fieldDataType;
        $result['component'] = $this->getFieldComponent($fieldDataType);
        $result['visible'] = true;
        $result['sortable'] = true;
        $result['filter'] = $this->getFieldFilter($fieldDataType);
        $result['label'] = $customFieldData[TicketOptionInterface::NAME];
        if ($fieldDataType == 'select') {
            $result['options'] = $this->getFieldOptions($customFieldData[TicketOptionInterface::KEY]);
        }
        return $result;
    }

    /**
     * Retrieve data type for specified field
     *
     * @param string $fieldType
     * @return string
     */
    private function getFieldDataType($fieldType)
    {
        return isset($this->dataTypeElementMap[$fieldType])
            ? $this->dataTypeElementMap[$fieldType]
            : $this->dataTypeElementMap['default'];
    }

    /**
     * Retrieve component for field with specified data type
     *
     * @param string $fieldDataType
     * @return string
     */
    private function getFieldComponent($fieldDataType)
    {
        return isset($this->gridElementMap[$fieldDataType])
            ? $this->gridElementMap[$fieldDataType]
            : $this->gridElementMap['default'];
    }

    /**
     * Retrieve filter type for field with specified data type
     *
     * @param string $fieldDataType
     * @return string
     */
    private function getFieldFilter($fieldDataType)
    {
        return isset($this->gridFilterMap[$fieldDataType])
            ? $this->gridFilterMap[$fieldDataType]
            : $this->gridFilterMap['default'];
    }

    /**
     * Retrieve options for select field
     *
     * @param string $key
     * @return array
     */
    private function getFieldOptions($key)
    {
        $optionValuesData = [];
        try {
            $optionValuesData = $this->ticketOptionsRepository->getAllValuesByOptionKey($key);
        } catch (\Exception $exception) {
        }

        $optionsData = [];
        foreach ($optionValuesData as $optionValuesDataRow) {
            $optionsData[] = [
                'value' => $optionValuesDataRow[TicketOptionInterface::VALUE],
                'label' => $optionValuesDataRow[TicketOptionInterface::VALUE]
            ];
        }

        return $optionsData;
    }
}
