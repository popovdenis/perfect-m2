<?php
namespace Aheadworks\EventTickets\Model\Product\Attribute\Backend;

use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class VenueId
 *
 * @package Aheadworks\EventTickets\Model\Product\Attribute\Backend
 */
class VenueId extends AbstractBackend
{
    /**
     * {@inheritdoc}
     */
    public function validate($object)
    {
        $attribute = $this->getAttribute();
        $attrCode = $attribute->getAttributeCode();
        $value = $object->getData($attrCode);

        if ($attribute->getIsVisible()
            && $attribute->getIsRequired()
            && $attribute->isValueEmpty($value)
            && $attribute->isValueEmpty($attribute->getDefaultValue())
        ) {
            throw new LocalizedException(
                __('Scroll down to "Event Ticket Options" and click "Select Space Configuration" button.')
            );
        }

        return true;
    }
}
