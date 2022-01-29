<?php
namespace Aheadworks\EventTickets\Ui\Component\Listing\Column;

use Aheadworks\EventTickets\Observer\ControllerActionPredispatchObserver;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class EventProductName
 *
 * @package Aheadworks\EventTickets\Ui\Component\Listing\Column
 */
class EventProductName extends Column
{
    /**
     * Key for the parameter in the component config
     */
    const AW_ET_PAGE_TO_RETURN_CONFIG_KEY = 'awEtPageToReturn';

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

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
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $generalUrlParams = $this->getGeneralUrlParamsForEditAction();
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$fieldName . '_label'] = $item[$fieldName];
                $item[$fieldName . '_url'] = $this->urlBuilder->getUrl(
                    'aw_event_tickets/event/edit',
                    array_merge(['id' => $item['entity_id']], $generalUrlParams)
                );
            }
        }

        return $dataSource;
    }

    /**
     * Retrieve general params to build edit action url
     *
     * @return array
     */
    private function getGeneralUrlParamsForEditAction()
    {
        $generalUrlParams = [];
        $generalUrlParams['store'] = $this->context->getFilterParam('store_id');
        $config = $this->getData('config');
        if (!empty($config[self::AW_ET_PAGE_TO_RETURN_CONFIG_KEY])) {
            $generalUrlParams[ControllerActionPredispatchObserver::AW_ET_PAGE_TO_RETURN_PARAM_KEY] =
                $config[self::AW_ET_PAGE_TO_RETURN_CONFIG_KEY];
        }
        return $generalUrlParams;
    }
}
