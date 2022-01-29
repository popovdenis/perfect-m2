<?php
namespace Aheadworks\EventTickets\Model\SampleData;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\SampleData\Context as SampleDataContext;
use Magento\Framework\Setup\SampleData\FixtureManager;
use Magento\Framework\File\Csv;

/**
 * Class Reader
 *
 * @package Aheadworks\EventTickets\Model\SampleData
 */
class Reader
{
    /**
     * @var FixtureManager
     */
    private $fixtureManager;

    /**
     * @var Csv
     */
    private $csvReader;

    /**
     * @param SampleDataContext $sampleDataContext
     */
    public function __construct(
        SampleDataContext $sampleDataContext
    ) {
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->csvReader = $sampleDataContext->getCsvReader();
    }

    /**
     * Read file
     *
     * @param string $fileName
     * @return array
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function readFile($fileName)
    {
        $fileName = $this->fixtureManager->getFixture($fileName);
        if (!file_exists($fileName)) {
             throw new LocalizedException(__('File %1 not found.', $fileName));
        }

        return $this->convertRowData($this->csvReader->getData($fileName));
    }

    /**
     * Convert row data
     *
     * @param array $rows
     * @return array
     */
    private function convertRowData($rows)
    {
        $header = array_shift($rows);
        foreach ($rows as &$row) {
            $data = [];
            foreach ($row as $key => $value) {
                $data[$header[$key]] = $value;
            }
            $row = $data;
        }

        return $rows;
    }
}
