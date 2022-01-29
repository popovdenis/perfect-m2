<?php
namespace Aheadworks\EventTickets\Model\Product\Option\Renderer;

/**
 * Class Composite
 *
 * @package Aheadworks\EventTickets\Model\Product\Option\Render
 */
class Composite implements RendererInterface
{
    /**
     * @var array
     */
    private $renderers = [];

    /**
     * @param array] $renderers
     */
    public function __construct(
        array $renderers
    ) {
        $this->renderers = $this->sort($renderers);
    }

    /**
     * {@inheritdoc}
     */
    public function render($options)
    {
        $result = [];
        foreach ($this->renderers as $rendererItem) {
            /** @var RendererInterface $renderer */
            $renderer = $rendererItem['object'];
            $result = array_merge($result, $renderer->render($options));
        }
        return $result;
    }

    /**
     * Sorting renderers according to sort order
     *
     * @param array $data
     * @return array
     */
    protected function sort(array $data)
    {
        usort($data, function (array $a, array $b) {
            $a['sortOrder'] = $this->getSortOrder($a);
            $b['sortOrder'] = $this->getSortOrder($b);

            if ($a['sortOrder'] == $b['sortOrder']) {
                return 0;
            }

            return ($a['sortOrder'] < $b['sortOrder']) ? -1 : 1;
        });

        return $data;
    }

    /**
     * Retrieve sort order from array
     *
     * @param array $variable
     * @return int
     */
    protected function getSortOrder(array $variable)
    {
        return !empty($variable['sortOrder']) ? $variable['sortOrder'] : 0;
    }
}
