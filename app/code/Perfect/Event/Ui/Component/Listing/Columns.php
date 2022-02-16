<?php

namespace Perfect\Event\Ui\Component\Listing;

/**
 * Class Columns
 *
 * @package Perfect\Event\Ui\Component\Listing
 */
class Columns extends \Magento\Ui\Component\Listing\Columns
{
    /**
     * @var int
     */
    protected $columnSortOrder;

    /**
     * @return int
     */
    protected function getDefaultSortOrder()
    {
        $max = 0;
        foreach ($this->components as $component) {
            $config = $component->getData('config');
            if (isset($config['sortOrder']) && $config['sortOrder'] > $max) {
                $max = $config['sortOrder'];
            }
        }
        return ++$max;
    }

    /**
     * Update actions column sort order
     *
     * @return void
     */
    protected function updateActionColumnSortOrder()
    {
        if (isset($this->components['actions'])) {
            $component = $this->components['actions'];
            $component->setData(
                'config',
                array_merge($component->getData('config'), ['sortOrder' => ++$this->columnSortOrder])
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $this->columnSortOrder = $this->getDefaultSortOrder();
        $this->updateActionColumnSortOrder();
        parent::prepare();
    }
}