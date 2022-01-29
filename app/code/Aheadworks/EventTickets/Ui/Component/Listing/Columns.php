<?php
namespace Aheadworks\EventTickets\Ui\Component\Listing;

use Magento\Ui\Component\Listing\Columns as UiColumns;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Api\Data\TicketOptionInterface;
use Aheadworks\EventTickets\Model\ResourceModel\Ticket\Option\Repository as TicketOptionsRepository;
use Aheadworks\EventTickets\Model\Ticket\Option\Mapper;

/**
 * Class Columns
 *
 * @package Aheadworks\EventTickets\Ui\Component\Listing
 */
class Columns extends UiColumns
{
    /**
     * @var UiComponentFactory
     */
    private $componentFactory;

    /**
     * @var TicketOptionsRepository
     */
    private $ticketOptionsRepository;

    /**
     * @var Mapper
     */
    private $mapper;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $componentFactory
     * @param TicketOptionsRepository $ticketOptionsRepository
     * @param Mapper $mapper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $componentFactory,
        TicketOptionsRepository $ticketOptionsRepository,
        Mapper $mapper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->componentFactory = $componentFactory;
        $this->ticketOptionsRepository = $ticketOptionsRepository;
        $this->mapper = $mapper;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $currentProductId = $this->getContext()->getRequestParam('product_id');
        try {
            $currentTicketOptionsList =
                $this->ticketOptionsRepository->getTicketOptionsListByProduct($currentProductId);
            foreach ($currentTicketOptionsList as $ticketOptionData) {
                if ($this->isNeedToAddCustomTicketOptionField($ticketOptionData)) {
                    $optionColumnConfig = $this->mapper->map($ticketOptionData);
                    $this->createComponent(
                        $ticketOptionData[TicketOptionInterface::KEY],
                        'column',
                        $optionColumnConfig
                    );
                }
            }
        } catch (\Exception $exception) {
        }
        parent::prepare();
    }

    /**
     * Check if ticket option data contains all necessary info
     *
     * @param array $ticketOptionData
     * @return bool
     */
    private function isNeedToAddCustomTicketOptionField($ticketOptionData)
    {
        return (
            !empty($ticketOptionData)
            && (is_array($ticketOptionData))
            && (isset($ticketOptionData[TicketOptionInterface::KEY]))
            && (isset($ticketOptionData[TicketOptionInterface::NAME]))
            && (isset($ticketOptionData[TicketOptionInterface::TYPE]))
        );
    }

    /**
     * Create component
     *
     * @param string $columnName
     * @param string $type
     * @param array $config
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function createComponent($columnName, $type, $config)
    {
        $component = $this->componentFactory->create(
            $columnName,
            $type,
            ['context' => $this->getContext()]
        );
        $component->setData('config', $config);
        $component->prepare();
        $this->addComponent($columnName, $component);

        return $this;
    }
}
