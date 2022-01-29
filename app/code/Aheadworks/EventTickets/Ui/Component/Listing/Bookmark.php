<?php
namespace Aheadworks\EventTickets\Ui\Component\Listing;

use Aheadworks\EventTickets\Model\Source\Product\Status;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Api\BookmarkManagementInterface;
use Magento\Ui\Api\BookmarkRepositoryInterface;
use Magento\Ui\Component\Bookmark as UiBookmark;
use Magento\Ui\Api\Data\BookmarkInterfaceFactory;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Bookmark
 *
 * @package Aheadworks\EventTickets\Ui\Component\Listing
 */
class Bookmark extends UiBookmark
{
    /**
     * @var string
     */
    const AW_ET_EVENT_LISTING_NAMESPACE = 'aw_event_tickets_event_listing';

    /**
     * @var BookmarkInterfaceFactory
     */
    private $bookmarkFactory;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @param BookmarkInterfaceFactory $bookmarkFactory
     * @param UserContextInterface $userContext
     * @param ContextInterface $context
     * @param BookmarkRepositoryInterface $bookmarkRepository
     * @param BookmarkManagementInterface $bookmarkManagement
     * @param array $components
     * @param array $data
     */
    public function __construct(
        BookmarkInterfaceFactory $bookmarkFactory,
        UserContextInterface $userContext,
        ContextInterface $context,
        BookmarkRepositoryInterface $bookmarkRepository,
        BookmarkManagementInterface $bookmarkManagement,
        array $components = [],
        array $data = []
    ) {
        $this->bookmarkFactory = $bookmarkFactory;
        $this->userContext = $userContext;
        parent::__construct($context, $bookmarkRepository, $bookmarkManagement, $components, $data);
    }

    /**
     * Register component
     *
     * @return void
     */
    public function prepare()
    {
        parent::prepare();
        $config = $this->getConfiguration();
        if (!isset($config['views'])) {
            $this->addView('default', __('Default View'), ['payment_method']);
            $this->addView(
                'past_events',
                __('Past events'),
                ['aw_et_status' => (string)Status::PAST]
            );
            $this->addView(
                'running_events',
                __('Running events'),
                ['aw_et_status' => (string)Status::RUNNING]
            );
            $this->addView(
                'upcoming_events',
                __('Upcoming events'),
                ['aw_et_status' => (string)Status::UPCOMING]
            );
        }
    }

    /**
     * Add view to the current config and save the bookmark to db
     *
     * @param string $index
     * @param string $label
     * @param array $filters applied filters as $filterName => $filterValue array
     * @return $this
     */
    private function addView($index, $label, $filters = [])
    {
        $config = $this->getConfiguration();

        $viewConf = $this->getDefaultViewConfig();
        $viewConf = array_merge($viewConf, [
            'index'     => $index,
            'label'     => $label,
            'value'     => $label,
            'editable'  => false
        ]);
        foreach ($filters as $filterName => $filterValue) {
            $viewConf['data']['filters']['applied'][$filterName] = $filterValue;
        }
        $viewConf['data']['displayMode'] = 'grid';

        $this->saveBookmark($index, $label, $viewConf);

        $config['views'][$index] = $viewConf;
        $this->setData('config', array_replace_recursive($config, $this->getConfiguration()));
        return $this;
    }

    /**
     * Save bookmark to db
     *
     * @param string $index
     * @param string $label
     * @param array $viewConf
     * @return $this
     */
    private function saveBookmark($index, $label, $viewConf)
    {
        $bookmark = $this->bookmarkFactory->create();
        $config = ['views' => [$index => $viewConf]];
        $bookmark->setUserId($this->userContext->getUserId())
            ->setNamespace(self::AW_ET_EVENT_LISTING_NAMESPACE)
            ->setIdentifier($index)
            ->setTitle($label)
            ->setConfig(json_encode($config));
        try {
            $this->bookmarkRepository->save($bookmark);
        } catch (LocalizedException $exception) {
        }

        return $this;
    }

    /**
     * Retrieve default view config
     *
     * @return mixed
     */
    private function getDefaultViewConfig()
    {
        $config['editable']  = false;
        $config['data']['filters']['applied']['placeholder'] = true;
        $config['data']['columns'] = [
            'ids'                           => ['sorting' => false, 'visible' => true],
            'entity_id'                     => ['sorting' => 'desc', 'visible' => true],
            'name'                          => ['sorting' => false, 'visible' => true],
            'aw_et_start_date'              => ['sorting' => false, 'visible' => true],
            'aw_et_end_date'                => ['sorting' => false, 'visible' => true],
            'aw_et_status'                  => ['sorting' => false, 'visible' => true],
            'aw_et_total_tickets_qty'       => ['sorting' => false, 'visible' => true],
            'aw_et_used_tickets_qty'        => ['sorting' => false, 'visible' => true],
            'aw_et_available_tickets_qty'   => ['sorting' => false, 'visible' => true],
            'actions'                       => ['sorting' => false, 'visible' => true],
        ];

        $position = 0;
        foreach (array_keys($config['data']['columns']) as $colName) {
            $config['data']['positions'][$colName] = $position;
            $position++;
        }

        $config['data']['paging'] = [
            'options' => [
                20 => ['value' => 20, 'label' => 20],
                30 => ['value' => 30, 'label' => 30],
                50 => ['value' => 50, 'label' => 50],
                100 => ['value' => 100, 'label' => 100],
                200 => ['value' => 200, 'label' => 200]
            ],
            'value' => 20
        ];

        return $config;
    }
}
