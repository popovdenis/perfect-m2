<?php
namespace Aheadworks\EventTickets\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface TicketOptionInterface
 * @api
 */
interface TicketOptionInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const NAME = 'name';
    const TYPE = 'type';
    const VALUE = 'value';
    const KEY = 'key';
    /**#@-*/

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get type
     *
     * @return string
     */
    public function getType();

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * Get value
     *
     * @return string
     */
    public function getValue();

    /**
     * Set value
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value);

    /**
     * Get key
     *
     * @return string
     */
    public function getKey();

    /**
     * Set key
     *
     * @param string $key
     * @return $this
     */
    public function setKey($key);
}
