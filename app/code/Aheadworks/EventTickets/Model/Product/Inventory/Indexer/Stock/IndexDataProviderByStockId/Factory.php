<?php
namespace Aheadworks\EventTickets\Model\Product\Inventory\Indexer\Stock\IndexDataProviderByStockId;

use Aheadworks\EventTickets\Model\Product\Inventory\Indexer\Stock\IndexDataProviderByStockIdInterface;
use Aheadworks\EventTickets\Model\ThirdParty\ModuleList
    as ThirdPartyModuleList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Aheadworks\EventTickets\Model\ThirdParty\Module\Version\Provider
    as ThirdPartyModuleVersionProvider;

class Factory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ThirdPartyModuleVersionProvider
     */
    private $thirdPartyModuleVersionProvider;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param ThirdPartyModuleVersionProvider $thirdPartyModuleVersionProvider
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ThirdPartyModuleVersionProvider $thirdPartyModuleVersionProvider
    ) {
        $this->objectManager = $objectManager;
        $this->thirdPartyModuleVersionProvider = $thirdPartyModuleVersionProvider;
    }

    /**
     * Return provider instance based on the current version of related MSI module
     *
     * @return IndexDataProviderByStockIdInterface
     * @throws LocalizedException
     */
    public function getInstance()
    {
        $msiInventoryIndexerModuleVersion = $this->thirdPartyModuleVersionProvider->get(
            ThirdPartyModuleList::MAGENTO_MSI_INVENTORY_INDEXER_MODULE_NAME
        );
        $indexDataProviderByStockIdClassName = version_compare($msiInventoryIndexerModuleVersion, '2.1.0', '>=')
            ? InventoryIndexerVersion210::class
            : InventoryIndexerVersionPriorTo210::class;

        return $this->objectManager->create($indexDataProviderByStockIdClassName);
    }
}
