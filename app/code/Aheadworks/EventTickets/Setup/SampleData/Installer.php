<?php
namespace Aheadworks\EventTickets\Setup\SampleData;

use Magento\Framework\Setup\SampleData\InstallerInterface as SampleDataInstallerInterface;

/**
 * Class Installer
 *
 * @package Aheadworks\EventTickets\Setup\SampleData
 */
class Installer implements SampleDataInstallerInterface
{
    /**
     * @var SampleDataInstallerInterface[]
     */
    private $installers;

    /**
     * @param SampleDataInstallerInterface[] $installers
     */
    public function __construct(
        array $installers = []
    ) {
        $this->installers = $installers;
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        foreach ($this->installers as $installer) {
            $installer->install();
        }
    }
}
