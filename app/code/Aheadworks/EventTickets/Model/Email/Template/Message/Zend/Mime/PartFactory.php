<?php
namespace Aheadworks\EventTickets\Model\Email\Template\Message\Zend\Mime;

use Magento\Framework\ObjectManagerInterface;
use Zend\Mime\Part;

/**
 * Class PartFactory
 * @package Aheadworks\EventTickets\Model\Email\Template\Message\Zend\Mime
 */
class PartFactory
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
        $instanceName = Part::class
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return Part
     */
    public function create(array $data = [])
    {
        return $this->objectManager->create($this->instanceName, $data);
    }
}
