<?php
namespace Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Reorder\Data;

class Error
{
    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $code;

    /**
     * @param string $message
     * @param string $code
     */
    public function __construct(string $message, string $code)
    {
        $this->message = $message;
        $this->code = $code;
    }

    /**
     * Get error message
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Get error code
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }
}
