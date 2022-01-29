<?php
namespace Aheadworks\EventTickets\Model\ThirdParty\Module;

use Magento\Framework\Module\ModuleListInterface;
use Aheadworks\EventTickets\Model\ThirdParty\ModuleList
    as ThirdPartyModuleList;

/**
 * Class Manager
 * @package Aheadworks\EventTickets\Model\ThirdParty\Module
 */
class Manager
{
    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @var array
     */
    private $msiModules = [
        ThirdPartyModuleList::MAGENTO_MSI_INVENTORY_API_MODULE_NAME,
        ThirdPartyModuleList::MAGENTO_MSI_INVENTORY_SALES_API_MODULE_NAME,
        ThirdPartyModuleList::MAGENTO_MSI_INVENTORY_CONFIGURATION_API_MODULE_NAME,
    ];

    /**
     * @param ModuleListInterface $moduleList
     */
    public function __construct(
        ModuleListInterface $moduleList
    ) {
        $this->moduleList = $moduleList;
    }

    /**
     * Check if Magento MSI module enabled
     *
     * @return bool
     */
    public function isMagentoMsiModuleEnabled()
    {
        foreach ($this->msiModules as $module) {
            if (!$this->moduleList->has($module)) {
                return false;
            }
        }

        return true;
    }
}
