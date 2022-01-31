<?php

namespace Perfect\Event\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Action
 *
 * @package Perfect\Event\Ui\Component\Listing\Column
 */
class Action extends Column
{
    /**
     * @const string
     */
    const URL_PATH_EDIT = 'perfect_event/index/newAction';
    const URL_PATH_DELETE = 'perfect_event/index/delete';
    const URL_PATH_ENABLE = 'perfect_event/index/enable';
    const URL_PATH_DISABLE = 'perfect_event/index/disable';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Action constructor.
     *
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface       $urlBuilder
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['id'])) {
                    $item[$this->getData('name')] = $this->getActions($item);
                }
            }
        }

        return $dataSource;
    }

    /**
     * Get actions links.
     *
     * @param array $item
     *
     * @return array
     */
    public function getActions(array $item)
    {
        return [
            'edit'   => [
                'href'  => $this->getEditUrl($item['id']),
                'label' => __('Edit')
            ],
            'delete' => [
                'href'    => $this->getDeleteUrl($item['id']),
                'label'   => __('Delete'),
                'confirm' => [
                    'title'   => __('Delete "${ $.$data.title }"'),
                    'message' => __('Are you sure you want to delete: "${ $.$data.title }"?')
                ]
            ],
            'enable'   => [
                'href'  => $this->getEnableUrl($item['id']),
                'label' => __('Enable')
            ],
            'disable'   => [
                'href'  => $this->getDisableUrl($item['id']),
                'label' => __('Disable')
            ],
        ];
    }

    /**
     * Get Edit url.
     *
     * @param int $eventId
     *
     * @return string
     */
    protected function getEditUrl(int $eventId)
    {
        return $this->urlBuilder->getUrl(static::URL_PATH_EDIT, ['id' => $eventId]);
    }

    /**
     * Get Delete url.
     *
     * @param int $eventId
     *
     * @return string
     */
    protected function getDeleteUrl(int $eventId)
    {
        return $this->urlBuilder->getUrl(static::URL_PATH_DELETE, ['id' => $eventId]);
    }

    /**
     * Get Enable url.
     *
     * @param int $eventId
     *
     * @return string
     */
    protected function getEnableUrl(int $eventId)
    {
        return $this->urlBuilder->getUrl(static::URL_PATH_ENABLE, ['id' => $eventId]);
    }

    /**
     * Get Disable url.
     *
     * @param int $eventId
     *
     * @return string
     */
    protected function getDisableUrl(int $eventId)
    {
        return $this->urlBuilder->getUrl(static::URL_PATH_DISABLE, ['id' => $eventId]);
    }
}