<?php
namespace Aheadworks\EventTickets\Model\SampleData\Installer;

use Aheadworks\EventTickets\Model\SampleData\Reader;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\SampleData\InstallerInterface as SampleDataInstallerInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Store\Model\Store;
use Aheadworks\EventTickets\Model\SampleData\Installer\Product\Converter\Composite as Converter;

/**
 * Class Product
 *
 * @package Aheadworks\EventTickets\Model\SampleData\Installer
 */
class Product implements SampleDataInstallerInterface
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var ProductInterfaceFactory
     */
    private $productDataFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var Converter
     */
    private $converter;

    /**
     * @var string
     */
    private $fileName = 'Aheadworks_EventTickets::fixtures/products.csv';

    /**
     * @param Reader $reader
     * @param ProductInterfaceFactory $productDataFactory
     * @param ProductRepositoryInterface $productRepository
     * @param DataObjectHelper $dataObjectHelper
     * @param Converter $converter
     */
    public function __construct(
        Reader $reader,
        ProductInterfaceFactory $productDataFactory,
        ProductRepositoryInterface $productRepository,
        DataObjectHelper $dataObjectHelper,
        Converter $converter
    ) {
        $this->reader = $reader;
        $this->productDataFactory = $productDataFactory;
        $this->productRepository = $productRepository;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->converter = $converter;
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $rows = $this->reader->readFile($this->fileName);
        foreach ($rows as $row) {
            if (!$this->ifExists($row[ProductInterface::SKU])) {
                $this->createProduct($row);
            }
        }
    }

    /**
     * Check if exists
     *
     * @param string $sku
     * @return bool
     */
    private function ifExists($sku)
    {
        try {
            $this->productRepository->get($sku, false, Store::DEFAULT_STORE_ID);
        } catch (NoSuchEntityException $e) {
            return false;
        }

        return true;
    }

    /**
     * Create product
     *
     * @param array $row
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function createProduct($row)
    {
        /** @var ProductInterface $product */
        $product = $this->productDataFactory->create();
        $data = $this->converter->convertRow($row);
        $this->dataObjectHelper->populateWithArray(
            $product,
            $data,
            ProductInterface::class
        );

        $this->productRepository->save($product);
    }
}
