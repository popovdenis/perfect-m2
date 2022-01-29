<?php
namespace Aheadworks\EventTickets\Model\Product\Status;

use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Model\Source\Product\Status;
use Aheadworks\EventTickets\Model\ResourceModel\Product\Collection as EventProductCollection;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Stdlib\DateTime;

/**
 * Class Resolver
 *
 * @package Aheadworks\EventTickets\Model\Product\Status
 */
class Resolver
{
    /**
     * Retrieve product status
     *
     * @param ProductInterface $product
     * @return int
     */
    public function getProductStatus($product)
    {
        $productStatus = Status::PAST;

        $awEtStartDate = $product->getAwEtStartDate();
        $awEtEndDate = $product->getAwEtEndDate();
        if (!empty($awEtStartDate) && !empty($awEtEndDate)) {
            $currentDate = new \DateTime('now', new \DateTimeZone('UTC'));
            $eventStartDate = new \DateTime($awEtStartDate);
            $eventEndDate = new \DateTime($awEtEndDate);

            if ($eventStartDate > $currentDate) {
                $productStatus = Status::UPCOMING;
            } elseif ($eventEndDate > $currentDate) {
                $productStatus = Status::RUNNING;
            }
        }

        return $productStatus;
    }

    /**
     * Add status filter to the specified collection
     *
     * @param EventProductCollection $collection
     * @param int $statusId
     * @return EventProductCollection
     */
    public function addStatusFilter($collection, $statusId)
    {
        $currentDate = new \DateTime('now', new \DateTimeZone('UTC'));
        $formattedCurrentDate = $currentDate->format(DateTime::DATETIME_PHP_FORMAT);

        switch ($statusId) {
            case Status::UPCOMING:
                $collection->addFieldToFilter(
                    ProductAttributeInterface::CODE_AW_ET_START_DATE,
                    ['date' => true, 'from' => $formattedCurrentDate]
                );
                break;
            case Status::RUNNING:
                $collection->addFieldToFilter(
                    ProductAttributeInterface::CODE_AW_ET_START_DATE,
                    ['date' => true, 'to' => $formattedCurrentDate]
                );
                $collection->addFieldToFilter(
                    ProductAttributeInterface::CODE_AW_ET_END_DATE,
                    ['date' => true, 'from' => $formattedCurrentDate]
                );
                break;
            case Status::PAST:
                $collection->addFieldToFilter(
                    ProductAttributeInterface::CODE_AW_ET_END_DATE,
                    ['date' => true, 'to' => $formattedCurrentDate]
                );
                break;
        }

        return $collection;
    }
}
