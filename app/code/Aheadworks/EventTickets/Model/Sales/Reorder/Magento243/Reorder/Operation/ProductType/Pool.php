<?php
namespace Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Reorder\Operation\ProductType;

use Magento\Framework\Exception\ConfigurationMismatchException;

class Pool
{
    /**
     * The key of operation list for the default one
     */
    const DEFAULT_OPERATION_KEY = 'default';

    /**
     * @var OperationInterface[]
     */
    private $operationListByProductType;

    /**
     * @param OperationInterface[] $operationListByProductType
     */
    public function __construct(
        array $operationListByProductType = []
    ) {
        $this->operationListByProductType = $operationListByProductType;
    }

    /**
     * Retrieve operation for the specific product type id
     *
     * @param string $productTypeId
     * @return OperationInterface
     * @throws ConfigurationMismatchException
     */
    public function getByProductTypeId($productTypeId)
    {
        if (!isset($this->operationListByProductType[self::DEFAULT_OPERATION_KEY])) {
            throw new ConfigurationMismatchException(
                __('No default reorder operation found')
            );
        }

        if (!isset($this->operationListByProductType[$productTypeId])) {
            $productTypeId = self::DEFAULT_OPERATION_KEY;
        }

        return $this->operationListByProductType[$productTypeId];
    }
}
