<?php
namespace Aheadworks\EventTickets\Model\Product\Attribute\Backend;

use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class SectorConfiguration
 *
 * @package Aheadworks\EventTickets\Model\Product\Attribute\Backend
 */
class SectorConfiguration extends AbstractBackend
{
    /**
     * {@inheritdoc}
     */
    public function validate($object)
    {
        $addedKeys = [];
        $sectors = $object->getData($this->getAttribute()->getName()) ? : [];
        foreach ($sectors as $sector) {
            $sectorId = $sector['sector_id'];
            $sectorTickets = $this->resolveSectorTickets($sector);
            foreach ($sectorTickets as $ticket) {
                $key = implode('-', [$sectorId, $ticket['type_id']]);
                $addedKeys[$key] = $this->processValidateDuplicate($key, $addedKeys);
            }
        }

        return true;
    }

    /**
     * Resolve sector tickets
     *
     * @param array $sector
     * @return array
     * @throws LocalizedException
     */
    private function resolveSectorTickets($sector)
    {
        $sectorTickets = isset($sector['sector_tickets']) ? $sector['sector_tickets'] : [];
        if (empty($sectorTickets)) {
            throw new LocalizedException(__('One of the sectors is not configured. Please configure it.'));
        }

        return $sectorTickets;
    }

    /**
     * Validate on duplicate
     *
     * @param string $key
     * @param array $addedKeys
     * @return bool
     * @throws LocalizedException
     */
    private function processValidateDuplicate($key, $addedKeys)
    {
        if (array_key_exists($key, $addedKeys)) {
            throw new LocalizedException(__('Duplicate ticket type found.'));
        }
        return true;
    }
}
