<?php
namespace Aheadworks\EventTickets\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Date;

/**
 * Class DateOnly
 * @package Aheadworks\EventTickets\Ui\Component\Listing\Column
 */
class DateOnly extends Date
{
    /**
     * @inheritdoc
     */
    public function prepare()
    {
        $config = $this->getData('config');
        $config['dateFormat'] = $this->timezone->getDateFormat(\IntlDateFormatter::MEDIUM);

        $this->setData('config', $config);

        parent::prepare();
    }
}
