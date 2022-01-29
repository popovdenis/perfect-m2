<?php
namespace Aheadworks\EventTickets\Ui\DataProvider\Space;

use Aheadworks\EventTickets\Api\Data\SpaceInterface;
use Aheadworks\EventTickets\Model\ResourceModel\Space\CollectionFactory;
use Aheadworks\EventTickets\Model\Source\Entity\Status;
use Aheadworks\EventTickets\Model\Source\VenueList;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class ListingDataProvider
 *
 * @package Aheadworks\EventTickets\Ui\DataProvider\Space
 */
class ListingDataProvider extends AbstractDataProvider
{
    /**
     * Request param key which shows that component is used on the separate edit form
     */
    const IS_EDIT_FORM_PARAM_KEY = 'isEditForm';

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        if ($this->request->getParam('isWizard', false)) {
            $this->getCollection()
                ->addFieldToFilter(SpaceInterface::STATUS, Status::STATUS_ENABLED)
                ->addFieldToFilter(SpaceInterface::VENUE_ID, $this->getVenueIdValue());
        }
        if ($this->request->getParam(self::IS_EDIT_FORM_PARAM_KEY, false)) {
            $this->getCollection()->addFieldToFilter(SpaceInterface::VENUE_ID, $this->getVenueIdValueFromEditForm());
        }
        $this->getCollection()
            ->setAttachSectors(false)
            ->load();

        return parent::getData();
    }

    /**
     * Retrieve venue id value from wizard filter
     *
     * @return array
     */
    private function getVenueIdValue()
    {
        $filter = $this->request->getParam('wizardFilter', []);
        $venueIds = [VenueList::ANY_VENUE];

        if (isset($filter['venue_id']) && $filter['venue_id']) {
            $venueIds[] = $filter['venue_id'];
        };

        return ['in' => $venueIds];
    }

    /**
     * Retrieve venue id value from edit form filter
     *
     * @return bool
     */
    private function getVenueIdValueFromEditForm()
    {
        $venueId = $this->request->getParam(SpaceInterface::VENUE_ID);

        return !empty($venueId) ? $venueId : -1;
    }
}
