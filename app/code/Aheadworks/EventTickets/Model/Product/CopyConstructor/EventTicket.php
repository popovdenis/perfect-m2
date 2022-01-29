<?php
namespace Aheadworks\EventTickets\Model\Product\CopyConstructor;

use Magento\Catalog\Model\Product\CopyConstructorInterface;
use Magento\Catalog\Model\Product;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket as EventTicketType;

/**
 * Class EventTicket
 *
 * @package Aheadworks\EventTickets\Model\Product\CopyConstructor
 */
class EventTicket implements CopyConstructorInterface
{
    /**
     * @var array
     */
    private $optionMapper = [];

    /**
     * {@inheritdoc}
     */
    public function build(Product $product, Product $duplicate)
    {
        if ($product->getTypeId() == EventTicketType::TYPE_CODE) {
            $this->makeCopy($product, $duplicate);
        }
    }

    /**
     * Make a copy of event tickets attributes
     *
     * @param Product $product
     * @param Product $duplicate
     * @return bool
     */
    private function makeCopy(Product $product, Product $duplicate)
    {
        $extensionAttributes = $product->getExtensionAttributes();
        if (!$extensionAttributes) {
            return false;
        }
        $personalOptions = $extensionAttributes->getAwEtPersonalOptions();
        foreach ($this->resolveArray($personalOptions) as $option) {
            $uniqueId = uniqid();
            $this->optionMapper[$option->getUid()] = $uniqueId;
            $option->setUid($uniqueId);
        }
        $extensionAttributes->setAwEtPersonalOptions($personalOptions);

        $sectorCofig = $extensionAttributes->getAwEtSectorConfig();
        foreach ($this->resolveArray($sectorCofig) as $sector) {
            foreach ($this->resolveArray($sector->getSectorTickets()) as $ticket) {
                $newPersonalOptionUids = [];
                foreach ($this->resolveArray($ticket->getPersonalOptionUids()) as $optionUid) {
                    $newPersonalOptionUids[] = isset($this->optionMapper[$optionUid])
                        ? $this->optionMapper[$optionUid]
                        : uniqid();
                }
                $ticket->setPersonalOptionUids($newPersonalOptionUids);
            }
        }
        $extensionAttributes->setAwEtSectorConfig($sectorCofig);
        $duplicate->setExtensionAttributes($extensionAttributes);
        return true;
    }

    /**
     * Resolve array
     *
     * @param array $array
     * @return array
     */
    private function resolveArray($array)
    {
        return is_array($array) && !empty($array)
            ? $array
            : [];
    }
}
