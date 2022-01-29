<?php
namespace Aheadworks\EventTickets\Model;

/**
 * Interface PostDataProcessorInterface
 * @package Aheadworks\EventTickets\Model
 */
interface PostDataProcessorInterface
{
    /**
     * Prepare entity data for save
     *
     * @param array $data
     * @return array
     */
    public function prepareEntityData($data);
}
