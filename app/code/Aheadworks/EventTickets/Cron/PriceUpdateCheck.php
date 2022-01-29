<?php
namespace Aheadworks\EventTickets\Cron;

use Aheadworks\EventTickets\Model\Flag;
use Aheadworks\EventTickets\Model\Ticket\Processor\PriceUpdate as PriceUpdateProcessor;

/**
 * Class PriceUpdateCheck
 *
 * @package Aheadworks\EventTickets\Cron
 */
class PriceUpdateCheck
{
    /**
     * @var Management
     */
    private $cronManagement;

    /**
     * @var PriceUpdateProcessor
     */
    private $priceUpdateProcessor;

    /**
     * @param Management $cronManagement
     * @param PriceUpdateProcessor $priceUpdateProcessor
     */
    public function __construct(
        Management $cronManagement,
        PriceUpdateProcessor $priceUpdateProcessor
    ) {
        $this->cronManagement = $cronManagement;
        $this->priceUpdateProcessor = $priceUpdateProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if (!$this->cronManagement->isLocked(Flag::AW_ET_PRICE_UPDATE_CHECK_LAST_EXEC_TIME, 600)) {
            $this->priceUpdateProcessor->process();
            $this->cronManagement->setFlagData(Flag::AW_ET_PRICE_UPDATE_CHECK_LAST_EXEC_TIME);
        }
    }
}
