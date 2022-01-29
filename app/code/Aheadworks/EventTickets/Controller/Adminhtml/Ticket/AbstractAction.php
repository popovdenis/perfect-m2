<?php
namespace Aheadworks\EventTickets\Controller\Adminhtml\Ticket;

use Magento\Framework\Controller\Result\Redirect;
use Aheadworks\EventTickets\Ui\Component\Listing\Column\TicketActions;

/**
 * Class AbstractAction
 *
 * @package Aheadworks\EventTickets\Controller\Adminhtml\Ticket
 */
abstract class AbstractAction extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_EventTickets::tickets';

    /**
     * Execute action
     *
     * @return Redirect
     */
    public function execute()
    {
        $ticketNumber = $this->getTicketNumber();
        if (isset($ticketNumber)) {
            try {
                if ($this->performAction($ticketNumber)) {
                    $this->messageManager->addSuccessMessage($this->getSuccessMessage());
                } else {
                    $this->messageManager->addErrorMessage($this->getErrorMessage());
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        return $this->getPreparedRedirect();
    }

    /**
     * Retrieve ticket number from the request
     *
     * @return string|null
     */
    protected function getTicketNumber()
    {
        return trim($this->getRequest()->getParam(TicketActions::TICKET_NUMBER_URL_PARAM_KEY));
    }

    /**
     * Execute action inner logic
     *
     * @param string $ticketNumber
     * @return bool
     * @throws \Exception
     */
    abstract protected function performAction($ticketNumber);

    /**
     * Retrieve action success message
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getSuccessMessage()
    {
        return __('Ticket has been changed.');
    }

    /**
     * Retrieve action error message
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getErrorMessage()
    {
        return __('We can\'t update ticket.');
    }

    /**
     * Retrieve redirect to the tickets grid in the current state
     *
     * @return Redirect
     */
    protected function getPreparedRedirect()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setRefererOrBaseUrl();
    }
}
