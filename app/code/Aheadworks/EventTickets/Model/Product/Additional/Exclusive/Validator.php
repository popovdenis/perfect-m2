<?php
namespace Aheadworks\EventTickets\Model\Product\Additional\Exclusive;

use Aheadworks\EventTickets\Api\Data\OptionInterface;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket;
use Aheadworks\EventTickets\Model\ResourceModel\Product\SectorRepository;
use Magento\Quote\Model\Quote;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\EventTickets\Model\Product\Additional\Exclusive\Message\Builder as ExclusiveMessageBuilder;
use Aheadworks\EventTickets\Model\Product\Additional\Exclusive\Resolver\Product as ProductResolver;

/**
 * Class Validator
 * @package Aheadworks\EventTickets\Model\Product\Additional\Exclusive
 */
class Validator
{
    /**
     * @var ExclusiveMessageBuilder
     */
    private $exclusiveMessageBuilder;

    /**
     * @var SectorRepository
     */
    private $sectorRepository;

    /**
     * @var ProductResolver
     */
    private $productResolver;

    /**
     * @var array
     */
    private $validAdditionalProductIds;

    /**
     * @param ExclusiveMessageBuilder $exclusiveMessageBuilder
     * @param SectorRepository $sectorRepository
     * @param ProductResolver $productResolver
     */
    public function __construct(
        ExclusiveMessageBuilder $exclusiveMessageBuilder,
        SectorRepository $sectorRepository,
        ProductResolver $productResolver
    ) {
        $this->exclusiveMessageBuilder = $exclusiveMessageBuilder;
        $this->sectorRepository = $sectorRepository;
        $this->productResolver = $productResolver;
    }

    /**
     * Validate product by quote
     *
     * @param Quote $quote
     * @param Quote\Item $item
     * @param bool $cached
     * @throws LocalizedException
     */
    public function validate($item, $quote, $cached = false)
    {
        $product = $this->productResolver->resolve($item);
        if ($product->getExtensionAttributes()
            && $product->getExtensionAttributes()->getAwEtExclusiveProduct()
            && !in_array($product->getEntityId(), $this->getValidAdditionalProductIds($quote, $cached))
        ) {
            throw new LocalizedException($this->exclusiveMessageBuilder->buildShort());
        }
    }

    /**
     * Retrieve valid additional product
     *
     * @param Quote $quote
     * @param bool $cached
     * @return array
     * @throws \Exception
     */
    private function getValidAdditionalProductIds($quote, $cached)
    {
        if (!$cached || null === $this->validAdditionalProductIds) {
            $productIds = $sectorIds = [];
            /** @var Quote\Item $item */
            foreach ($quote->getAllItems() as $item) {
                if ($item->getProductType() == EventTicket::TYPE_CODE) {
                    $productIds[] = $item->getProductId();
                    $sectorIds[] = $item->getOptionByCode(OptionInterface::SECTOR_ID)->getValue();
                }
            }
            $this->validAdditionalProductIds = $this->sectorRepository
                ->getAdditionalProductsByTicketSectorProducts($productIds, $sectorIds);
        }
        return $this->validAdditionalProductIds;
    }
}
