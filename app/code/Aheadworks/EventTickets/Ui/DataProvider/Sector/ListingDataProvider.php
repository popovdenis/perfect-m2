<?php
namespace Aheadworks\EventTickets\Ui\DataProvider\Sector;

use Aheadworks\EventTickets\Model\ResourceModel\Sector\CollectionFactory;
use Aheadworks\EventTickets\Model\Source\Entity\Status;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class ListingDataProvider
 *
 * @package Aheadworks\EventTickets\Ui\DataProvider\Sector
 */
class ListingDataProvider extends AbstractDataProvider
{
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
                ->addFieldToFilter('status', Status::STATUS_ENABLED)
                ->addFieldToFilter('space_id', $this->getSpaceIdValue());
        }
        $this->getCollection()->load();

        return parent::getData();
    }

    /**
     * Retrieve space id value from wizard filter
     *
     * @return int
     */
    private function getSpaceIdValue()
    {
        $filter = $this->request->getParam('wizardFilter', []);

        return isset($filter['space_id']) && $filter['space_id'] ? $filter['space_id'] : 0;
    }
}
