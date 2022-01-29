<?php
namespace Aheadworks\EventTickets\Model\Product\Option;

use Aheadworks\EventTickets\Model\Product\Option\Renderer\Composite;
use Magento\Catalog\Model\Product;
use Aheadworks\EventTickets\Model\Product\Option\Extractor as OptionExtractor;

/**
 * Class Render
 *
 * @package Aheadworks\EventTickets\Model\Product\Option
 */
class Render
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const GLOBAL_SECTION = 'global';
    const BACKEND_SECTION = 'backend';
    const FRONTEND_SECTION = 'frontend';
    /**#@-*/

    /**
     * @var OptionExtractor
     */
    private $optionExtractor;

    /**
     * @var Composite
     */
    private $rendererProcessor;

    /**
     * @var array
     */
    private $optionsConfig;

    /**
     * @param OptionExtractor $optionExtractor
     * @param Composite $rendererProcessor
     * @param array $optionsConfig
     */
    public function __construct(
        OptionExtractor $optionExtractor,
        Composite $rendererProcessor,
        array $optionsConfig = []
    ) {
        $this->optionExtractor = $optionExtractor;
        $this->rendererProcessor = $rendererProcessor;
        $this->optionsConfig = $optionsConfig;
    }

    /**
     * Retrieve data for display
     *
     * @param array $data
     * @param Product $product
     * @param string $section
     * @return array
     */
    public function render($data, $product = null, $section = self::GLOBAL_SECTION)
    {
        $data = $this->prepareDataBySection($data, $section);
        $objectOptions = $this->optionExtractor->extractFromArray($data, $product);
        $result = $this->rendererProcessor->render($objectOptions);

        return $result;
    }

    /**
     * Prepare data by section
     *
     * @param array $data
     * @param string $section
     * @return array
     */
    private function prepareDataBySection($data, $section)
    {
        if ($section == self::GLOBAL_SECTION) {
            return $data;
        }

        foreach ($this->optionsConfig as $optionConfig) {
            $remove = true;
            if (!isset($data[$optionConfig['optionName']]) || !isset($data[$optionConfig['optionName']])) {
                continue;
            }
            if (!isset($optionConfig['sections']) || !is_array($optionConfig['sections'])) {
                continue;
            }

            foreach ($optionConfig['sections'] as $optionSection) {
                if ($section == $optionSection) {
                    $remove = false;
                }
            }
            if ($remove) {
                unset($data[$optionConfig['optionName']]);
            }
        }
        return $data;
    }
}
