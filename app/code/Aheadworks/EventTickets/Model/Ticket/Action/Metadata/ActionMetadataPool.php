<?php
namespace Aheadworks\EventTickets\Model\Ticket\Action\Metadata;

/**
 * Class ActionMetadataPool
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Action\Metadata
 */
class ActionMetadataPool
{
    /**
     * @var array
     */
    private $metadata = [];

    /**
     * @var ActionMetadataInterfaceFactory
     */
    private $metadataFactory;

    /**
     * @var ActionMetadataInterface[]
     */
    private $metadataInstances = [];

    /**
     * @param ActionMetadataInterfaceFactory $metadataFactory
     * @param array $metadata
     */
    public function __construct(
        ActionMetadataInterfaceFactory $metadataFactory,
        $metadata = []
    ) {
        $this->metadataFactory = $metadataFactory;
        $this->metadata = $metadata;
    }

    /**
     * Retrieves metadata for ticket action
     *
     * @param string $actionName
     * @return ActionMetadataInterface
     * @throws \Exception
     */
    public function getMetadata($actionName)
    {
        if (empty($this->metadataInstances[$actionName])) {
            $actionMetadata = $this->getActionMetadata($actionName);
            $actionMetadataInstance = $this->getMetadataInstance($actionMetadata);
            $this->metadataInstances[$actionName] = $actionMetadataInstance;
        }
        return $this->metadataInstances[$actionName];
    }

    /**
     * Retrieve metadata for specified ticket action
     *
     * @param string $actionName
     * @return array
     * @throws \Exception
     */
    private function getActionMetadata($actionName)
    {
        if (!isset($this->metadata[$actionName])) {
            throw new \Exception(sprintf('Unknown ticket action metadata: %s requested', $actionName));
        }
        return $this->metadata[$actionName];
    }

    /**
     * Retrieve metadata instance from specified data
     *
     * @param $actionMetadata
     * @return mixed
     * @throws \Exception
     */
    private function getMetadataInstance($actionMetadata)
    {
        $metadataInstance = $this->metadataFactory->create(['data' => $actionMetadata]);
        if (!$metadataInstance instanceof ActionMetadataInterface) {
            throw new \Exception(
                sprintf('Metadata instance does not implement required interface.')
            );
        }
        return $metadataInstance;
    }
}
