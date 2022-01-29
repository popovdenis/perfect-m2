<?php
namespace Aheadworks\EventTickets\Model\Ticket\Action\Metadata;

/**
 * Interface ActionMetadataInterface
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Action\Metadata
 */
interface ActionMetadataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const NAME = 'name';
    const CLASS_NAME = 'class_name';
    /**#@-*/

    /**
     * Get ticket action name
     *
     * @return string
     */
    public function getName();

    /**
     * Get ticket action class name
     *
     * @return string
     */
    public function getClassName();
}
