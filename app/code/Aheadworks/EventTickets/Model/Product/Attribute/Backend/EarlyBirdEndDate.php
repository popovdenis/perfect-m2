<?php
namespace Aheadworks\EventTickets\Model\Product\Attribute\Backend;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Backend\Datetime;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class EarlyBirdEndDate
 *
 * @package Aheadworks\EventTickets\Model\Product\Attribute\Backend
 */
class EarlyBirdEndDate extends Datetime
{
    /**
     * {@inheritdoc}
     */
    public function validate($object)
    {
        parent::validate($object);

        if ($object->getData($this->getAttribute()->getName())) {
            $startDate = new \DateTime(
                $this->formatDate($object->getData(ProductAttributeInterface::CODE_AW_ET_START_DATE))
            );
            $earlyBirdEndDate = new \DateTime(
                $this->formatDate($object->getData($this->getAttribute()->getName()))
            );
            if ($startDate <= $earlyBirdEndDate) {
                throw new LocalizedException(
                    __('Early Bird Tickets End Date cannot be equal or later than Event Start Date.')
                );
            }
        }

        return true;
    }
}
