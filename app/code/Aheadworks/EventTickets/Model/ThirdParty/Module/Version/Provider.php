<?php
namespace Aheadworks\EventTickets\Model\ThirdParty\Module\Version;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\Filesystem\Directory\ReadFactory as DirectoryReadFactory;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

class Provider
{
    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @var ComponentRegistrarInterface
     */
    private $componentRegistrar;

    /**
     * @var DirectoryReadFactory
     */
    private $directoryReadFactory;

    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * @param ModuleListInterface $moduleList
     * @param ComponentRegistrarInterface $componentRegistrar
     * @param DirectoryReadFactory $directoryReadFactory
     * @param JsonSerializer $jsonSerializer
     */
    public function __construct(
        ModuleListInterface $moduleList,
        ComponentRegistrarInterface $componentRegistrar,
        DirectoryReadFactory $directoryReadFactory,
        JsonSerializer $jsonSerializer
    ) {
        $this->moduleList = $moduleList;
        $this->componentRegistrar = $componentRegistrar;
        $this->directoryReadFactory = $directoryReadFactory;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * Retrieve module version by its name
     *
     * @param string $moduleName
     * @return string
     * @throws LocalizedException
     */
    public function get($moduleName)
    {
        if ($this->moduleList->has($moduleName)) {
            $moduleVersion = $this->getFromDeclarationData($moduleName);
            if (empty($moduleVersion)) {
                $moduleVersion = $this->getFromComposerJson($moduleName);
            }
            if (empty($moduleVersion)) {
                throw new LocalizedException(__("Can't detect version for module %1", $moduleName));
            }
            return $moduleVersion;
        }
        throw new LocalizedException(__('Module %1 not found', $moduleName));
    }

    /**
     * Retrieve module version from declaration data
     *
     * @param string $moduleName
     * @return string|null
     */
    private function getFromDeclarationData($moduleName)
    {
        $moduleDeclarationData = $this->moduleList->getOne($moduleName);
        if (is_array($moduleDeclarationData)
            && isset($moduleDeclarationData['setup_version'])
        ) {
            return $moduleDeclarationData['setup_version'];
        }
        return null;
    }

    /**
     * Retrieve module version from composer.json file
     *
     * @param string $moduleName
     * @return string|null
     * @throws LocalizedException
     */
    private function getFromComposerJson($moduleName)
    {
        $moduleVersion = null;
        $modulePath = $this->componentRegistrar->getPath(
            ComponentRegistrar::MODULE,
            $moduleName
        );
        if ($modulePath) {
            $moduleDirectory = $this->directoryReadFactory->create($modulePath);
            if ($moduleDirectory->isExist('composer.json')) {
                $composerJsonData = $moduleDirectory->readFile('composer.json');
                $composerJsonDecodedData = $this->jsonSerializer->unserialize($composerJsonData);
                if (is_array($composerJsonDecodedData)
                    && isset($composerJsonDecodedData['version'])
                ) {
                    $moduleVersion = $composerJsonDecodedData['version'];
                }
            }
        }
        return $moduleVersion;
    }
}
