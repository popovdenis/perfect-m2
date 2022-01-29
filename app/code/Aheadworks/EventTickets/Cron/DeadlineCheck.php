<?php
namespace Aheadworks\EventTickets\Cron;

use Aheadworks\EventTickets\Model\Flag;
use Aheadworks\EventTickets\Model\Ticket\Processor\Deadline as DeadlineProcessor;

/**
 * Class DeadlineCheck
 *
 * @package Aheadworks\EventTickets\Cron
 */
class DeadlineCheck
{
    /**
     * @var Management
     */
    private $cronManagement;

    /**
     * @var DeadlineProcessor
     */
    private $deadlineProcessor;

    /**
     * @param Management $cronManagement
     * @param DeadlineProcessor $deadlineProcessor
     */
    public function __construct(
        Management $cronManagement,
        DeadlineProcessor $deadlineProcessor
    ) {
        $this->cronManagement = $cronManagement;
        $this->deadlineProcessor = $deadlineProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if (!$this->cronManagement->isLocked(Flag::AW_ET_DEADLINE_CHECK_LAST_EXEC_TIME)) {
            $this->deadlineProcessor->process();
            $this->cronManagement->setFlagData(Flag::AW_ET_DEADLINE_CHECK_LAST_EXEC_TIME);
        }
    }
}
