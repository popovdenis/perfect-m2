<?php

namespace Perfect\EventService\Api\Data;

/**
 * Interface EventServiceAttributeInterface
 *
 * @package Perfect\EventService\Api\Data
 */
interface EventServiceAttributeInterface
{
    const ENTITY_TYPE_CODE = 'perfect_service';

    /**
     * Check if attribute has a global scope.
     *
     * @return bool
     */
    public function isScopeGlobal();

    /**
     * Check if attribute has a website scope.
     *
     * @return bool
     */
    public function isScopeWebsite();

    /**
     * Retrieve attribute has a store scope.
     *
     * @return bool
     */
    public function isScopeStore();
}