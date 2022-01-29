<?php
namespace Aheadworks\EventTickets\Block\Product\View;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Block\Product\View\Description;
use Aheadworks\EventTickets\Model\Ticket\Generator\FromOrderItem\Resolver\TicketEntity as TicketEntityResolver;
use Aheadworks\EventTickets\Api\Data\VenueInterface;
use Aheadworks\EventTickets\Api\Data\SpaceInterface;
use Aheadworks\EventTickets\Api\Data\StorefrontLabelsEntityInterface;

/**
 * Class Details
 *
 * @package Aheadworks\EventTickets\Block\Product\View
 */
class Details extends Description
{
    /**
     * @var TicketEntityResolver
     */
    private $ticketEntityResolver;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param TicketEntityResolver $ticketEntityResolver
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        TicketEntityResolver $ticketEntityResolver,
        array $data = []
    ) {
        $this->ticketEntityResolver = $ticketEntityResolver;
        parent::__construct($context, $registry, $data);
    }

    /**
     * Resolve attribute value according to the attribute config
     *
     * @param array $attributeConfig
     * @return string
     */
    public function resolveAttributeValue($attributeConfig)
    {
        $attributeValue = '';

        if (is_array($attributeConfig)) {
            if (isset($attributeConfig['entity_getter'])) {
                $currentProduct = $this->getProduct();
                /** @var StorefrontLabelsEntityInterface $attributeEntity */
                $entityGetter = (string)$attributeConfig['entity_getter'];
                $attributeEntity = $this->$entityGetter($currentProduct);
                if (isset($attributeConfig['value_getter'])) {
                    $valueGetter = (string)$attributeConfig['value_getter'];
                    $attributeValue = $attributeEntity->$valueGetter();
                } elseif (isset($attributeConfig['storefront_description_getter'])) {
                    $valueGetter = (string)$attributeConfig['storefront_description_getter'];
                    $attributeValue =
                        $attributeEntity->getCurrentLabels()->$valueGetter();
                }
            }
        }

        return $attributeValue;
    }

    /**
     * Retrieve venue from specified product
     *
     * @param ProductInterface $product
     * @return VenueInterface
     */
    public function getVenue($product)
    {
        $venueId = $product->getAwEtVenueId();
        return $this->ticketEntityResolver->resolveVenue($venueId, $this->getCurrentStoreId());
    }

    /**
     * Retrieve space from specified product
     *
     * @param ProductInterface $product
     * @return SpaceInterface
     */
    public function getSpace($product)
    {
        $spaceId = $product->getAwEtSpaceId();
        return $this->ticketEntityResolver->resolveSpace($spaceId, $this->getCurrentStoreId());
    }

    /**
     * Retrieve current store id
     *
     * @return int
     */
    private function getCurrentStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
}
