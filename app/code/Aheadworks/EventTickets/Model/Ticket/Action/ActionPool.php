<?php
namespace Aheadworks\EventTickets\Model\Ticket\Action;

use Magento\Framework\ObjectManagerInterface;
use Aheadworks\EventTickets\Model\Ticket\Action\Metadata\ActionMetadataPool;

/**
 * Class ActionPool
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Action
 */
class ActionPool
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ActionMetadataPool
     */
    private $actionMetadataPool;

    /**
     * @var AbstractAction[]
     */
    private $actionInstances = [];

    /**
     * @param ObjectManagerInterface $objectManager
     * @param ActionMetadataPool $actionMetadataPool
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ActionMetadataPool $actionMetadataPool
    ) {
        $this->objectManager = $objectManager;
        $this->actionMetadataPool = $actionMetadataPool;
    }

    /**
     * Retrieves ticket action instance
     *
     * @param string $actionCode
     * @return AbstractAction
     * @throws \Exception
     */
    public function getAction($actionCode)
    {
        if (empty($this->actionInstances[$actionCode])) {
            $metadata = $this->actionMetadataPool->getMetadata($actionCode);
            $actionInstance = $this->objectManager->create($metadata->getClassName());
            if (!$actionInstance instanceof AbstractAction) {
                throw new \Exception(
                    sprintf('Ticket action %s does not implement required interface.', $actionCode)
                );
            }
            $this->actionInstances[$actionCode] = $actionInstance;
        }
        return $this->actionInstances[$actionCode];
    }
}
