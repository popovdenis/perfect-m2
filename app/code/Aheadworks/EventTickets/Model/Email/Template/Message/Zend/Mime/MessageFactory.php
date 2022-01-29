<?php
namespace Aheadworks\EventTickets\Model\Email\Template\Message\Zend\Mime;

use Magento\Framework\ObjectManagerInterface;
use Zend\Mime\Message;

/**
 * Class MessageFactory
 * @package Aheadworks\EventTickets\Model\Email\Template\Message\Zend\Mime
 */
class MessageFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var string
     */
    private $instanceName;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        $instanceName = Message::class
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return Message
     */
    public function create(array $data = [])
    {
        return $this->objectManager->create($this->instanceName, $data);
    }
}
