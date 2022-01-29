<?php
namespace Aheadworks\EventTickets\Model;

use Magento\Framework\Flag as FrameworkFlag;

/**
 * Class Flag
 *
 * @package Aheadworks\EventTickets\Model
 */
class Flag extends FrameworkFlag
{
    /**#@+
     * Constants for event tickets cron flags
     */
    const AW_ET_DEADLINE_CHECK_LAST_EXEC_TIME = 'aw_et_deadline_check_last_exec_time';
    const AW_ET_PRICE_UPDATE_CHECK_LAST_EXEC_TIME = 'aw_et_price_update_check_last_exec_time';
    const AW_ET_END_DATE_CHECK_LAST_EXEC_TIME = 'aw_et_end_date_check_last_exec_time';
    /**#@-*/

    /**
     * Setter for flag code
     *
     * @param string $code
     * @return $this
     * @codeCoverageIgnore
     */
    public function setEtFlagCode($code)
    {
        $this->_flagCode = $code;
        return $this;
    }
}
