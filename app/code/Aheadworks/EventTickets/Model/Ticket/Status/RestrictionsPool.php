<?php
namespace Aheadworks\EventTickets\Model\Ticket\Status;

/**
 * Class RestrictionsPool
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Status
 */
class RestrictionsPool
{
    /**
     * @var RestrictionsInterfaceFactory
     */
    private $restrictionsFactory;

    /**
     * @var array
     */
    private $restrictionsData = [];

    /**
     * @var RestrictionsInterface[]
     */
    private $restrictionsInstances = [];

    /**
     * @param RestrictionsInterfaceFactory $restrictionsFactory
     * @param array $restrictionsData
     */
    public function __construct(
        RestrictionsInterfaceFactory $restrictionsFactory,
        $restrictionsData = []
    ) {
        $this->restrictionsFactory = $restrictionsFactory;
        $this->restrictionsData = $restrictionsData;
    }

    /**
     * Retrieves restrictions instance
     *
     * @param int $status
     * @return RestrictionsInterface
     * @throws \Exception
     */
    public function getRestrictions($status)
    {
        if (empty($this->restrictionsInstances[$status])) {
            $restrictionsDataForStatus = $this->getRestrictionsDataForStatus($status);
            $restrictionInstance = $this->getRestrictionsInstance($restrictionsDataForStatus);
            $this->restrictionsInstances[$status] = $restrictionInstance;
        }
        return $this->restrictionsInstances[$status];
    }

    /**
     * Retrieve restriction data for specified status
     *
     * @param int $status
     * @return array
     * @throws \Exception
     */
    private function getRestrictionsDataForStatus($status)
    {
        if (!isset($this->restrictionsData[$status])) {
            throw new \Exception(sprintf('Unknown status: %s requested', $status));
        }
        return $this->restrictionsData[$status];
    }

    /**
     * Retrieve restrictions instance from restrictions data
     *
     * @param array $restrictionsData
     * @return RestrictionsInterface
     * @throws \Exception
     */
    private function getRestrictionsInstance($restrictionsData)
    {
        $restrictionInstance = $this->restrictionsFactory->create(['data' => $restrictionsData]);
        if (!$restrictionInstance instanceof RestrictionsInterface) {
            throw new \Exception(
                sprintf('Restrictions instance does not implement required interface.')
            );
        }
        return $restrictionInstance;
    }
}
