<?php
namespace Aheadworks\EventTickets\Model\Product\Additional\Exclusive\Resolver\Product;

/**
 * Class TypePool
 * @package Aheadworks\EventTickets\Model\Product\Additional\Exclusive\Resolver\Product
 */
class TypePool
{
    /**
     * @var array
     */
    private $types = [];

    /**
     * @param array $types
     */
    public function __construct(
        $types = []
    ) {
        $this->types = $types;
    }

    /**
     * Get resolver instance
     *
     * @param string $productType
     * @return ProductResolverInterface
     * @throws \Exception
     */
    public function getResolver($productType)
    {
        if (!isset($this->types[$productType])) {
            throw new \Exception(sprintf('Unknown resolver: %s requested', $productType));
        }
        $typeInstance = $this->types[$productType];
        if (!$typeInstance instanceof ProductResolverInterface) {
            throw new \Exception(
                sprintf('Resolver instance %s does not implement required interface.', $productType)
            );
        }

        return $typeInstance;
    }

    /**
     * Check if resolver for product type exists
     *
     * @param string $productType
     * @return bool
     */
    public function hasResolver($productType)
    {
        return isset($this->types[$productType]);
    }
}
