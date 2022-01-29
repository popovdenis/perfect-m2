<?php
namespace Aheadworks\EventTickets\Model\Product\Type\EventTicket\Render;

use Aheadworks\EventTickets\Api\Data\ProductTypeRender\AdditionalProductRenderInterface;
use Aheadworks\EventTickets\Api\Data\ProductTypeRender\SectorRenderInterface;
use Magento\Framework\EntityManager\MapperInterface;

/**
 * Class Mapper
 * @package Aheadworks\EventTickets\Model\Product\Type\EventTicket\Render
 */
class Mapper implements MapperInterface
{
    /**
     * {@inheritdoc}
     */
    public function entityToDatabase($entityType, $data)
    {
        if (isset($data[SectorRenderInterface::ADDITIONAL_PRODUCTS])
            && is_array($data[SectorRenderInterface::ADDITIONAL_PRODUCTS])
        ) {
            foreach ($data[SectorRenderInterface::ADDITIONAL_PRODUCTS] as &$item) {
                $item[AdditionalProductRenderInterface::OPTION] =
                    \Zend_Json::decode($item[AdditionalProductRenderInterface::OPTION]);
            }
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function databaseToEntity($entityType, $data)
    {
        return $data;
    }
}
