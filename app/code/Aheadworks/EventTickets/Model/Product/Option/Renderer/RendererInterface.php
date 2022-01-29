<?php
namespace Aheadworks\EventTickets\Model\Product\Option\Renderer;

use Aheadworks\EventTickets\Api\Data\OptionInterface;

/**
 * Interface RendererInterface
 *
 * @package Aheadworks\EventTickets\Model\Product\Option\Renderer
 */
interface RendererInterface
{
    /**
     * Retrieve data for display
     *
     * @param OptionInterface $options
     * @return array
     */
    public function render($options);
}
