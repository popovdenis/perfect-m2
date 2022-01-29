<?php
namespace Aheadworks\EventTickets\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Setup\SampleData\Executor as SampleDataExecutor;
use Aheadworks\EventTickets\Setup\SampleData\Installer as SampleDataInstaller;

/**
 * Class InstallSampleData
 * @package Aheadworks\EventTickets\Console\Command
 */
class InstallSampleData extends Command
{
    /**
     * @var SampleDataExecutor
     */
    private $sampleDataExecutor;

    /**
     * @var SampleDataInstaller
     */
    private $sampleDataInstaller;

    /**
     * InstallSampleData constructor.
     * @param SampleDataExecutor $sampleDataExecutor
     * @param SampleDataInstaller $sampleDataInstaller
     * @param string|null $name
     */
    public function __construct(
        SampleDataExecutor $sampleDataExecutor,
        SampleDataInstaller $sampleDataInstaller,
        string $name = null
    ) {
        parent::__construct($name);

        $this->sampleDataExecutor = $sampleDataExecutor;
        $this->sampleDataInstaller = $sampleDataInstaller;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('aw_et:sampledata:deploy');
        $this->setDescription('Install sample data for Aheadworks Event Tickets module');

        parent::configure();
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return null|int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->sampleDataExecutor->exec($this->sampleDataInstaller);
        $output->writeln('<info>Sample data installed successfully</info>');
    }
}