<?php
namespace Aheadworks\EventTickets\Cron;

use Aheadworks\EventTickets\Model\Flag;
use Aheadworks\EventTickets\Model\Ticket\Processor\EndDate as EndDateProcessor;

/**
 * Class EndDateCheck
 *
 * @package Aheadworks\EventTickets\Cron
 */
class EndDateCheck
{
    /**
     * @var Management
     */
    private $cronManagement;

    /**
     * @var EndDateProcessor
     */
    private $endDateProcessor;

    /**
     * @param Management $cronManagement
     * @param EndDateProcessor $endDateProcessor
     */
    public function __construct(
        Management $cronManagement,
        EndDateProcessor $endDateProcessor
    ) {
        $this->cronManagement = $cronManagement;
        $this->endDateProcessor = $endDateProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if (!$this->cronManagement->isLocked(Flag::AW_ET_END_DATE_CHECK_LAST_EXEC_TIME)) {
            $this->endDateProcessor->process();
            $this->cronManagement->setFlagData(Flag::AW_ET_END_DATE_CHECK_LAST_EXEC_TIME);
        }
    }
}
