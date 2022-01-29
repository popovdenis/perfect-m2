<?php
namespace Aheadworks\EventTickets\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Ticket\Status\Resolver as TicketStatusResolver;

/**
 * Class TicketActions
 *
 * @package Aheadworks\EventTickets\Ui\Component\Listing\Column
 */
class TicketActions extends Column
{
    /** Url path */
    const AW_ET_URL_PATH_CHANGE_STATUS = 'aw_event_tickets/ticket/changeStatus';

    /**#@+
     * Url params
     */
    const TICKET_NUMBER_URL_PARAM_KEY = 'ticket_number';
    /**#@-*/

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var TicketStatusResolver
     */
    private $ticketStatusResolver;

    /**
     * @var string
     */
    private $changeStatusUrl;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param TicketStatusResolver $ticketStatusResolver
     * @param array $components
     * @param array $data
     * @param string $changeStatusUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        TicketStatusResolver $ticketStatusResolver,
        array $components = [],
        array $data = [],
        $changeStatusUrl = self::AW_ET_URL_PATH_CHANGE_STATUS
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->ticketStatusResolver = $ticketStatusResolver;
        $this->changeStatusUrl = $changeStatusUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item[TicketInterface::ID])) {
                    $ticketNumber = $item[TicketInterface::NUMBER];
                    $ticketStatus = $item[TicketInterface::STATUS];
                    $item[$name] = $this->getActionsDataForTicket($ticketNumber, $ticketStatus);
                }
            }
        }

        return $dataSource;
    }

    /**
     * Get available actions data for specified ticket
     *
     * @param int $ticketNumber
     * @param int $ticketStatus
     * @return array
     */
    private function getActionsDataForTicket($ticketNumber, $ticketStatus)
    {
        $actionsData = [];
        $actionsConfig = $this->getTicketActionsConfig();
        foreach ($actionsConfig as $actionName => $actionConfigData) {
            if ($this->isNeedToAddActionOnTicket($actionName, $ticketStatus)) {
                $actionsData[$actionName] = $this->getDataForActionOnTicket($actionConfigData, $ticketNumber);
            }
        }
        return $actionsData;
    }

    /**
     * Retrieve ticket actions config
     *
     * @return array
     */
    private function getTicketActionsConfig()
    {
        return $this->getData('config/actions');
    }

    /**
     * Check if need to add action on the ticket with specified status
     *
     * @param string $actionName
     * @param int $ticketStatus
     * @return bool
     */
    private function isNeedToAddActionOnTicket($actionName, $ticketStatus)
    {
        return $this->ticketStatusResolver->isActionAllowedForTicketStatus($actionName, $ticketStatus);
    }

    /**
     * Get array with action settings for specified ticket number
     *
     * @param array $actionConfigData
     * @param string $ticketNumber
     * @return array
     */
    private function getDataForActionOnTicket($actionConfigData, $ticketNumber)
    {
        $action = [
            'href' => $this->urlBuilder->getUrl(
                $actionConfigData['url_route'],
                [
                    self::TICKET_NUMBER_URL_PARAM_KEY => $ticketNumber,
                ]
            ),
            'label' => $actionConfigData['label']
        ];
        if (isset($actionConfigData['confirm'])
            && isset($actionConfigData['confirm']['title'])
            && isset($actionConfigData['confirm']['message'])
        ) {
            $action['confirm'] = [
                'title' => $actionConfigData['confirm']['title'],
                'message' => $actionConfigData['confirm']['message']
            ];
        }
        return $action;
    }
}
