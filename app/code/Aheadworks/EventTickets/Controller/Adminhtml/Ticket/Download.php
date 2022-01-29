<?php
namespace Aheadworks\EventTickets\Controller\Adminhtml\Ticket;

use Magento\Backend\App\Action\Context;
use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Api\TicketActionManagementInterface;
use Magento\Framework\App\Response\Http\FileFactory;

/**
 * Class Download
 *
 * @package Aheadworks\EventTickets\Controller\Adminhtml\Ticket
 */
class Download extends AbstractAction
{
    /**
     * @var TicketActionManagementInterface
     */
    private $ticketActionService;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @param Context $context
     * @param TicketActionManagementInterface $ticketActionService
     * @param FileFactory $fileFactory
     */
    public function __construct(
        Context $context,
        TicketActionManagementInterface $ticketActionService,
        FileFactory $fileFactory
    ) {
        parent::__construct($context);
        $this->ticketActionService = $ticketActionService;
        $this->fileFactory = $fileFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $ticketNumber = $this->getTicketNumber();
        if (isset($ticketNumber)) {
            try {
                $processedTickets = $this->performAction($ticketNumber);
                /** @var TicketInterface|\Aheadworks\EventTickets\Model\Ticket $currentTicket */
                $currentTicket = array_shift($processedTickets);
                if (isset($currentTicket)) {
                    $pdf = $currentTicket->getPdf(true);
                    return $this->fileFactory->create(
                        $pdf->getFileName(),
                        $pdf->getAttachment()
                    );
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        return $this->getPreparedRedirect();
    }

    /**
     * {@inheritdoc}
     */
    protected function performAction($ticketNumber)
    {
        return $this->ticketActionService->doAction(
            TicketInterface::DOWNLOAD_ACTION_NAME,
            [$ticketNumber]
        );
    }
}
