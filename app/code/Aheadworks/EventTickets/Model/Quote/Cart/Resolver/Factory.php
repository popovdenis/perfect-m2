<?php
namespace Aheadworks\EventTickets\Model\Quote\Cart\Resolver;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\ObjectManagerInterface;

class Factory
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
     * Return resolver instance based on the current version of Magento
     *
     * @return ResolverInterface
     */
    public function getInstance()
    {
        $magentoVersion = $this->productMetadata->getVersion();
        $resolverClassName = version_compare($magentoVersion, '2.4.0', '>=')
            ? Magento24::class
            : Magento23::class;

        return $this->objectManager->create($resolverClassName);
    }
}
