<?php
namespace Aheadworks\EventTickets\Model\Product\Attribute\Backend;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\ScheduleType;
use Magento\Eav\Model\Entity\Attribute\Backend\Datetime;

/**
 * Class StartDate
 * @package Aheadworks\EventTickets\Model\Product\Attribute\Backend
 */
class StartDate extends Datetime
{
    /**
     * {@inheritdoc}
     */
    public function validate($object)
    {
        if ($object->getData(ProductAttributeInterface::CODE_AW_ET_SCHEDULE_TYPE) == ScheduleType::ONE_TIME) {
            parent::validate($object);
        }

        return true;
    }
}
