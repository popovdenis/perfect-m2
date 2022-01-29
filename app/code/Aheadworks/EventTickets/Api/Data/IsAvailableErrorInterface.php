<?php
namespace Aheadworks\EventTickets\Api\Data;

/**
 * Interface IsAvailableErrorInterface
 * @package Aheadworks\EventTickets\Api\Data
 */
interface IsAvailableErrorInterface
{
    /**
     * Get error code
     *
     * @return string
     */
    public function getCode();

    /**
     * Get error message
     *
     * @return string
     */
    public function getMessage();
}
