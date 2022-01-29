<?php
namespace Aheadworks\EventTickets\Block\Information;

/**
 * Interface BarMessageInterface
 * @package Aheadworks\EventTickets\Block\Information
 */
interface BarMessageInterface
{
    /**
     * Check can show or not
     *
     * @return bool
     */
    public function canShow();

    /**
     * Retrieve message
     *
     * @return string
     */
    public function getMessage();

    /**
     * Retrieve template
     *
     * @return string
     */
    public function getTemplate();

    /**
     * Retrieve html
     *
     * @return string
     */
    public function toHtml();
}
