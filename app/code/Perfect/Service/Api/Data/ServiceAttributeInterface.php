<?php

namespace Perfect\Service\Api\Data;

/**
 * Interface ServiceAttributeInterface
 *
 * @package Perfect\Service\Api\Data
 */
interface ServiceAttributeInterface
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