<?php

namespace Perfect\EventService\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Perfect\EventService\Model\Service\Attribute\Source\ServiceType;

/**
 * Class ServiceSetup
 *
 * @package Perfect\EventService\Setup
 */
class ServiceSetup extends EavSetup
{
    /**
     * Default entities and attributes
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getDefaultEntities()
    {
        return [
            'perfect_service' => [
                'entity_model' => 'Perfect\EntityService\Model\ResourceModel\Service',
                'attribute_model' => 'Perfect\EntityService\Model\ResourceModel\Service\Attribute',
                'table' => 'perfect_service_entity',
                'increment_model' => \Magento\Eav\Model\Entity\Increment\NumericValue::class,
                'additional_attribute_table' => 'perfect_service_eav_attribute',
                'entity_attribute_collection' => 'Perfect\EntityService\Model\ResourceModel\EventService\Attribute\Collection',
                'attributes' => [
                    'service_name' => [
                        'type' => 'varchar',
                        'label' => 'Service Name',
                        'input' => 'text',
                        'required' => true,
                        'sort_order' => 10,
                        'position' => 10,
                        'validate_rules' => '{"min_text_length":1,"max_text_length":255}',
                        'group' => 'General Information'
                    ],
                    'service_quantity' => [
                        'type' => 'int',
                        'label' => 'Service Quantity',
                        'input' => 'text',
                        'required' => true,
                        'sort_order' => 20,
                        'position' => 20,
                        'group' => 'General Information'
                    ],
                    'service_price' => [
                        'type' => 'int',
                        'label' => 'Service Price',
                        'input' => 'price',
                        'sort_order' => 30,
                        'position' => 30,
                        'group' => 'General Information'
                    ],
                    'service_price_from' => [
                        'type' => 'int',
                        'label' => 'Service Price From',
                        'input' => 'price',
                        'sort_order' => 40,
                        'position' => 40,
                        'group' => 'General Information'
                    ],
                    'service_price_to' => [
                        'type' => 'int',
                        'label' => 'Service Price To',
                        'input' => 'price',
                        'sort_order' => 50,
                        'position' => 50,
                        'group' => 'General Information'
                    ],
                    'service_type' => [
                        'type' => 'int',
                        'label' => 'Service Type',
                        'input' => 'select',
                        'source' => ServiceType::class,
                        'sort_order' => 60,
                        'position' => 60,
                        'group' => 'General Information'
                    ],
                    'is_active' => [
                        'type' => 'int',
                        'label' => 'Is Active',
                        'input' => 'select',
                        'source' => Boolean::class,
                        'sort_order' => 70,
                        'position' => 70,
                        'group' => 'General Information'
                    ],
                    'created_at' => [
                        'type' => 'static',
                        'label' => 'Created At',
                        'input' => 'date',
                        'sort_order' => 80,
                        'position' => 80,
                        'required' => false,
                        'visible' => false,
                        'system' => false,
                    ],
                    'updated_at' => [
                        'type' => 'static',
                        'label' => 'Updated At',
                        'input' => 'date',
                        'sort_order' => 90,
                        'position' => 90,
                        'required' => false,
                        'visible' => false,
                        'system' => false,
                    ]
                ],
            ]
        ];
    }
}