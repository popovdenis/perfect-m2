<?php
namespace Aheadworks\EventTickets\Model\Product\Attribute\Backend;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\ScheduleType;
use Magento\Eav\Model\Entity\Attribute\Backend\Datetime;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class EndDate
 *
 * @package Aheadworks\EventTickets\Model\Product\Attribute\Backend
 */
class EndDate extends Datetime
{
    /**
     * {@inheritdoc}
     */
    public function validate($object)
    {
        if ($object->getData(ProductAttributeInterface::CODE_AW_ET_SCHEDULE_TYPE) == ScheduleType::ONE_TIME) {
            parent::validate($object);

            $now = new \DateTime('now');
            $startDate = new \DateTime(
                $this->formatDate($object->getData(ProductAttributeInterface::CODE_AW_ET_START_DATE))
            );
            $endDate = new \DateTime(
                $this->formatDate($object->getData($this->getAttribute()->getName()))
            );

            if ($startDate >= $endDate) {
                throw new LocalizedException(__('Event Start Date cannot be later than Event End Date.'));
            }
            if ($now >= $endDate) {
                throw new LocalizedException(__('Event End Date in the past.'));
            }
        }

        return true;
    }
}
