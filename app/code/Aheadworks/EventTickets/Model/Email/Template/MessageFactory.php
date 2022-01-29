<?php
namespace Aheadworks\EventTickets\Model\Email\Template;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * Class MessageFactory
 * @package Aheadworks\EventTickets\Model\Email\Template
 */
class MessageFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ProductMetadataInterface $productMetadata
    ) {
        $this->objectManager = $objectManager;
        $this->productMetadata = $productMetadata;
    }

    /**
     * Create serializer instance
     *
     * @return Message
     */
    public function create()
    {
        $magentoVersion = $this->productMetadata->getVersion();
        $messageClassName = version_compare($magentoVersion, '2.3.0', '>=')
            ? \Aheadworks\EventTickets\Model\Email\Template\Magento230\Message::class
            : \Aheadworks\EventTickets\Model\Email\Template\Message::class;

        return $this->objectManager->create($messageClassName);
    }
}
