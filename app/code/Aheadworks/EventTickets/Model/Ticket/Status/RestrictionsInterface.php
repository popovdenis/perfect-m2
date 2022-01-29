<?php
namespace Aheadworks\EventTickets\Model\Ticket\Status;

/**
 * Interface RestrictionsInterface
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Status
 */
interface RestrictionsInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ALLOWED_ACTIONS_NAMES = 'allowed_actions_names';
    /**#@-*/

    /**
     * Get allowed actions names
     *
     * @return array
     */
    public function getAllowedActionsNames();
}
