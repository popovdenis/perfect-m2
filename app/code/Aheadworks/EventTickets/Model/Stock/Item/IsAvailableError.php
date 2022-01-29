<?php
namespace Aheadworks\EventTickets\Model\Stock\Item;

use Aheadworks\EventTickets\Api\Data\IsAvailableErrorInterface;

/**
 * Class IsAvailableResult
 * @package Aheadworks\EventTickets\Model\Stock\Item
 */
class IsAvailableError implements IsAvailableErrorInterface
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $message;

    /**
     * @param string $code
     * @param string $message
     */
    public function __construct($code, $message)
    {
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * @inheritDoc
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        return $this->message;
    }
}
