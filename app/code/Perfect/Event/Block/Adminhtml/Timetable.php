<?php

namespace Perfect\Event\Block\Adminhtml;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * Class Timetable
 *
 * @package Perfect\Event\Block\Adminhtml
 */
class Timetable extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $jsonEncoder;
    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    private $collectionFactory;

    /**
     * Timetable constructor.
     *
     * @param \Magento\Backend\Block\Template\Context                          $context
     * @param \Magento\Framework\Serialize\Serializer\Json                     $jsonEncoder
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionFactory
     * @param array                                                            $data
     * @param \Magento\Framework\Json\Helper\Data|null                         $jsonHelper
     * @param \Magento\Directory\Helper\Data|null                              $directoryHelper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Serialize\Serializer\Json $jsonEncoder,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionFactory,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    )
    {
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
        $this->jsonEncoder = $jsonEncoder;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return bool|false|string
     */
    public function getConfig($schedulerId)
    {
        $config = [
            'scheduler' => '#scheduler' . $schedulerId,
            'customers' => $this->getCustomers()
        ];

        return $this->jsonEncoder->serialize($config);
    }

    /**
     * Get customer collection
     */
    public function getCustomers()
    {
        $customers = [];
        $customerCollection = $this->collectionFactory->create();
        $customerCollection->addFieldToFilter('group_id', ['eq' => 5]);

        return $customerCollection->getItems();
    }
}