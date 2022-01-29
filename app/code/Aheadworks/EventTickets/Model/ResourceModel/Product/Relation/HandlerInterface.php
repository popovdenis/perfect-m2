<?php
namespace Aheadworks\EventTickets\Model\ResourceModel\Product\Relation;

/**
 * Interface HandlerInterface
 * @package Aheadworks\EventTickets\Model\ResourceModel\Product\Relation
 */
interface HandlerInterface
{
    /**
     * Perform save
     *
     * @param object $entity
     * @return object
     */
    public function save($entity);

    /**
     * Perform load
     *
     * @param object $entity
     * @return object
     */
    public function load($entity);
}
