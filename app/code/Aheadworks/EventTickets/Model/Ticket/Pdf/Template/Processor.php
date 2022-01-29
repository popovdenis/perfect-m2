<?php
namespace Aheadworks\EventTickets\Model\Ticket\Pdf\Template;

use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Config;
use Aheadworks\EventTickets\Model\Source\Ticket\PdfVariables;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\EventTickets\Model\Ticket\Pdf\Template\VariableProcessor\Composite as VariableProcessorComposite;
use Magento\Framework\App\Area;

/**
 * Class Processor
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Pdf\Template
 */
class Processor
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var VariableProcessorComposite
     */
    private $variableProcessorComposite;

    /**
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param VariableProcessorComposite $variableProcessorComposite
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        VariableProcessorComposite $variableProcessorComposite
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->variableProcessorComposite = $variableProcessorComposite;
    }

    /**
     * Process
     *
     * @param TicketInterface $ticket
     * @return array
     */
    public function process($ticket)
    {
        $storeId = $ticket->getStoreId();

        return [
            $this->getTemplateId($storeId),
            $this->getTemplateOptions($storeId),
            $this->prepareTemplateVariables($ticket)
        ];
    }

    /**
     * Retrieve template id
     *
     * @param int $storeId
     * @return string
     */
    private function getTemplateId($storeId)
    {
        return $this->config->getTicketTemplatePdf($storeId);
    }

    /**
     * Prepare template options
     *
     * @param int $storeId
     * @return array
     */
    private function getTemplateOptions($storeId)
    {
        return [
            'area' => Area::AREA_FRONTEND,
            'store' => $storeId
        ];
    }

    /**
     * Prepare template variables
     *
     * @param TicketInterface $ticket
     * @return array
     */
    private function prepareTemplateVariables($ticket)
    {
        $templateVariables = [
            PdfVariables::TICKET => $ticket,
            PdfVariables::STORE => $this->storeManager->getStore($ticket->getStoreId())
        ];

        return $this->variableProcessorComposite->prepareVariables($templateVariables);
    }
}
