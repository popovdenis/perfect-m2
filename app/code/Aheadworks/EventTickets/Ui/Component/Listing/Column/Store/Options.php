<?php
namespace Aheadworks\EventTickets\Ui\Component\Listing\Column\Store;

use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Ui\Component\Listing\Column\Store\Options as StoreOptions;

/**
 * Class Options
 *
 * @package Aheadworks\EventTickets\Ui\Component\Listing\Column\Store
 */
class Options extends StoreOptions
{
    /**
     * All Store Views value
     */
    const ALL_STORE_VIEWS = '0';

    /**
     * @var array
     */
    private $storeListOptions;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $this->currentOptions['All Store Views']['label'] = __('All Store Views');
        $this->currentOptions['All Store Views']['value'] = self::ALL_STORE_VIEWS;

        $this->generateCurrentOptions();

        $this->options = array_values($this->currentOptions);

        return $this->options;
    }

    /**
     * Get store list
     *
     * @return array
     */
    public function getStoreList()
    {
        if ($this->storeListOptions !== null) {
            return $this->storeListOptions;
        }

        $this->storeListOptions['All Store Views']['label'] = __('All Store Views');
        $this->storeListOptions['All Store Views']['value'] = self::ALL_STORE_VIEWS;
        /** @var StoreInterface $store */
        foreach ($this->systemStore->getStoreCollection() as $store) {
            $this->storeListOptions[] = [
                'label' => $store->getName(),
                'value' => $store->getId()
            ];
        }

        $this->storeListOptions = array_values($this->storeListOptions);

        return $this->storeListOptions;
    }
}
