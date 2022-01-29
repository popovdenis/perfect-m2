<?php
namespace Aheadworks\EventTickets\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Date;

/**
 * Class Time
 * @package Aheadworks\EventTickets\Ui\Component\Listing\Column
 */
class Time extends Date
{
    /**
     * @inheritdoc
     */
    public function prepare()
    {
        $config = $this->getData('config');
        $config['dateFormat'] = $this->timezone->getTimeFormat(\IntlDateFormatter::SHORT);
        $this->setData('config', $config);

        parent::prepare();
    }
}
