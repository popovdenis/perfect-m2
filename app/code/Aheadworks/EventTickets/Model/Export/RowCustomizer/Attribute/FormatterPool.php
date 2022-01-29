<?php
namespace Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute;

use Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter\FormatterInterface;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Class FormatterPool
 *
 * @package Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute
 */
class FormatterPool
{
    /**
     * @var FormatterInterface[]
     */
    private $formatters = [];

    /**
     * @var FormatterInterface
     */
    private $baseFormatter = [];

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param ArrayManager $arrayManager
     * @param FormatterInterface $baseFormatter
     * @param array $formatters
     */
    public function __construct(
        ArrayManager $arrayManager,
        FormatterInterface $baseFormatter,
        $formatters = []
    ) {
        $this->arrayManager = $arrayManager;
        $this->baseFormatter = $baseFormatter;
        $this->formatters = $formatters;
    }

    /**
     * Retrieve column value formatter by column name
     *
     * @param string $attributePath
     * @return FormatterInterface
     */
    public function getByAttributePath($attributePath)
    {
        $formatter = $this->arrayManager->get($attributePath, $this->formatters);
        if (isset($formatter) && $formatter instanceof FormatterInterface) {
            return $formatter;
        }
        return $this->baseFormatter;
    }
}
