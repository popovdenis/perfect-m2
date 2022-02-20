<?php

namespace Perfect\Service\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Action
 *
 * @package Perfect\Service\Ui\Component\Listing\Column
 */
class Action extends Column
{
    /**
     * @const string
     */
    const URL_PATH_EDIT = 'perfect_service/index/newAction';
    const URL_PATH_DELETE = 'perfect_service/index/delete';

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
                if (isset($item['entity_id'])) {
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
                'href'  => $this->getEditUrl($item['entity_id']),
                'label' => __('Edit')
            ],
            'delete' => [
                'href'    => $this->getDeleteUrl($item['entity_id']),
                'label'   => __('Delete'),
                'confirm' => [
                    'title'   => __('Delete "${ $.$data.title }"'),
                    'message' => __('Are you sure you want to delete: "${ $.$data.title }"?')
                ]
            ]
        ];
    }

    /**
     * Get Edit url.
     *
     * @param int $entityId
     *
     * @return string
     */
    protected function getEditUrl(int $entityId)
    {
        return $this->urlBuilder->getUrl(static::URL_PATH_EDIT, ['entity_id' => $entityId]);
    }

    /**
     * Get Delete url.
     *
     * @param int $eventId
     *
     * @return string
     */
    protected function getDeleteUrl(int $entityId)
    {
        return $this->urlBuilder->getUrl(static::URL_PATH_DELETE, ['entity_id' => $entityId]);
    }
}