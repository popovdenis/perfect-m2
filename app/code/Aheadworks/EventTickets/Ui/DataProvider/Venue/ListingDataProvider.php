<?php
namespace Aheadworks\EventTickets\Ui\DataProvider\Venue;

use Aheadworks\EventTickets\Model\ResourceModel\Venue\CollectionFactory;
use Aheadworks\EventTickets\Model\Source\Entity\Status;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class ListingDataProvider
 *
 * @package Aheadworks\EventTickets\Ui\DataProvider\Venue
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
        if ($isWizard = $this->request->getParam('isWizard', false)) {
            $this->getCollection()->addFieldToFilter('status', Status::STATUS_ENABLED);
        }
        $this->getCollection()->load();

        return parent::getData();
    }
}
