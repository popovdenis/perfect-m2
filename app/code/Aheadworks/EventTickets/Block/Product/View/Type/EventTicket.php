<?php
namespace Aheadworks\EventTickets\Block\Product\View\Type;

use Aheadworks\EventTickets\Api\StockManagementInterface;
use Aheadworks\EventTickets\Model\Product\Layout\Processor\View\SectorOptions\Resolver as SectorOptionsResolver;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\ScheduleType;
use Magento\Catalog\Block\Product\Context;
use Magento\Framework\Stdlib\ArrayUtils;
use Magento\Catalog\Block\Product\View\AbstractView;

/**
 * Class EventTicket
 *
 * @package Aheadworks\EventTickets\Block\Product\View\Type
 */
class EventTicket extends AbstractView
{
    /**
     * @var SectorOptionsResolver
     */
    private $sectorOptionsResolver;

    /**
     * @var StockManagementInterface
     */
    private $stockManagement;

    /**
     * @param Context $context
     * @param ArrayUtils $arrayUtils
     * @param SectorOptionsResolver $sectorOptionsResolver
     * @param StockManagementInterface $stockManagement
     * @param array $data
     */
    public function __construct(
        Context $context,
        ArrayUtils $arrayUtils,
        SectorOptionsResolver $sectorOptionsResolver,
        StockManagementInterface $stockManagement,
        array $data = []
    ) {
        parent::__construct($context, $arrayUtils, $data);
        $this->sectorOptionsResolver = $sectorOptionsResolver;
        $this->stockManagement = $stockManagement;
    }

    /**
     * Retrieve status
     *
     * @return string
     */
    public function getStatus()
    {
        if ($this->isRecurring()) {
            return '';
        }

        $productId = $this->getProduct()->getId();

        $status = $this->stockManagement->getTicketStatus($productId);
        $qtyAvailable = $this->stockManagement->getAvailableTicketQty($productId);

        return $this->sectorOptionsResolver->resolveStockStatusLabel($status, $qtyAvailable);
    }

    /**
     * Retrieve start date
     *
     * @return string
     */
    public function getStartDate()
    {
        if ($this->isRecurring()) {
            return '';
        }

        $product = $this->getProduct();
        /** @var \Aheadworks\EventTickets\Model\Product\Type\EventTicket $productType */
        $productType = $product->getTypeInstance();

        $startDate = $productType->getEventStartDate($product);

        return $this->formatDate($startDate, \IntlDateFormatter::MEDIUM, true);
    }

    /**
     * Check if product is recurring
     *
     * @return bool
     */
    private function isRecurring()
    {
        /** @var \Aheadworks\EventTickets\Model\Product\Type\EventTicket $productType */
        $product = $this->getProduct();
        $productType = $this->getProduct()->getTypeInstance();

        return $productType->getScheduleType($product) == ScheduleType::RECURRING;
    }
}
