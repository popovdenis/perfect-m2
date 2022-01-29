<?php
namespace Aheadworks\EventTickets\Block\Information;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Backend\Block\Template\Context;

/**
 * Class Bar
 *
 * @method BarMessageInterface[] getBarMessages()
 * @package Aheadworks\EventTickets\Block\Information
 */
class Bar extends Template
{
    /**
     * Block template filename
     *
     * @var string
     */
    protected $_template = 'Aheadworks_EventTickets::information/bar.phtml';

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->objectManager = $objectManager;
    }

    /**
     * Retrieve information messages
     *
     * @return array
     */
    public function getMessages()
    {
        $messages = [];
        foreach ($this->getBarMessages() as $messageClass) {
            $message = $this->objectManager->create($messageClass);
            if ($message->canShow()) {
                $messages[] = $message->getTemplate()
                    ? $message->toHtml()
                    : $message->getMessage();
            }
        }

        return $messages;
    }
}
