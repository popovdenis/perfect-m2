<?php
namespace Aheadworks\EventTickets\Ui\Component\Listing\MassAction\Columns;

use Magento\Ui\Component\MassAction\Columns\Column as MassActionColumn;

/**
 * Class Column
 *
 * @package Aheadworks\EventTickets\Ui\Component\Listing\MassAction\Columns
 */
class Column extends MassActionColumn
{
    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        parent::prepare();
        if ($this->isSetSingleSelectTemplate()) {
            $config = $this->getData('config');
            $config['headerTmpl'] = 'Aheadworks_EventTickets/ui/grid/columns/single-select';
            $config['bodyTmpl'] = 'Aheadworks_EventTickets/ui/grid/columns/cells/single-select';
            $config['preserveSelectionsOnFilter'] = true;
            $this->setData('config', $config);
        }
    }

    /**
     * Check if set single select template
     *
     * @return bool
     */
    private function isSetSingleSelectTemplate()
    {
        return $this->getContext()->getRequestParam('isWizard', false);
    }
}
