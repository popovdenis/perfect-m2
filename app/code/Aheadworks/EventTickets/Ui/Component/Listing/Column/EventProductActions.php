<?php
namespace Aheadworks\EventTickets\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class EventProductActions
 *
 * @package Aheadworks\EventTickets\Ui\Component\Listing\Column
 */
class EventProductActions extends Column
{
    /**
     * Url path
     */
    const VIEW_TICKETS_URL_PATH = 'aw_event_tickets/ticket/index';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
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
        foreach ($dataSource['data']['items'] as & $item) {
            $name = $this->getData('name');
            $item[$name]['view_tickets'] = [
                'href' => $this->getUrl($item),
                'label' => __('View Tickets')
            ];
        }

        return $dataSource;
    }

    /**
     * Retrieve url
     *
     * @param array $itemData
     * @return string
     */
    protected function getUrl($itemData)
    {
        return $this->urlBuilder->getUrl(self::VIEW_TICKETS_URL_PATH, ['product_id' => $itemData['entity_id']]);
    }
}
