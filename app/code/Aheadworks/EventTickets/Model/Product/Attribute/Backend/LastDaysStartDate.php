<?php
namespace Aheadworks\EventTickets\Model\Product\Attribute\Backend;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Backend\Datetime;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Aheadworks\EventTickets\Model\Stock\Resolver\TicketSellingDeadlineDate;

/**
 * Class LastDaysStartDate
 *
 * @package Aheadworks\EventTickets\Model\Product\Attribute\Backend
 */
class LastDaysStartDate extends Datetime
{
    /**
     * @var TicketSellingDeadlineDate
     */
    private $deadlineDateResolver;

    /**
     * @param TimezoneInterface $localeDate
     * @param TicketSellingDeadlineDate $deadlineDateResolver
     */
    public function __construct(
        TimezoneInterface $localeDate,
        TicketSellingDeadlineDate $deadlineDateResolver
    ) {
        parent::__construct($localeDate);
        $this->deadlineDateResolver = $deadlineDateResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($object)
    {
        parent::validate($object);

        if ($object->getData($this->getAttribute()->getName())) {
            $lastDaysStartDate = new \DateTime(
                $this->formatDate($object->getData($this->getAttribute()->getName()))
            );
            $deadlineDate = $this->deadlineDateResolver->resolve($object);
            if ($lastDaysStartDate >= $deadlineDate) {
                throw new LocalizedException(
                    __('Last Day(s) Price Start Date cannot be equal or later than Selling Deadline Date.')
                );
            }

            if ($object->getData(ProductAttributeInterface::CODE_AW_ET_EARLY_BIRD_END_DATE)) {
                $earlyBirdEndDate = new \DateTime(
                    $this->formatDate($object->getData(ProductAttributeInterface::CODE_AW_ET_EARLY_BIRD_END_DATE))
                );
                if ($lastDaysStartDate <= $earlyBirdEndDate) {
                    throw new LocalizedException(
                        __('Early Bird Tickets End Date cannot be equal or later than Last Day(s) Price Start Date.')
                    );
                }
            }
        }

        return true;
    }
}
