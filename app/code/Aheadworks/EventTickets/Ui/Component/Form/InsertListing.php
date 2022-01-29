<?php
namespace Aheadworks\EventTickets\Ui\Component\Form;

use Magento\Ui\Component\Container;

/**
 * Class InsertListing
 *
 * @package Aheadworks\EventTickets\Ui\Component\Form
 */
class InsertListing extends Container
{
    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $id = $this->getContext()->getRequestParam(
            $this->getContext()->getDataProvider()->getRequestFieldName(),
            'new'
        );
        $config = $this->getData('config');
        $config['params'][$config['addParamToFilter']] = $id;
        $this->setData('config', $config);

        parent::prepare();
    }
}
