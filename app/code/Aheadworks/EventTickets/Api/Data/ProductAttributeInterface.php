<?php
namespace Aheadworks\EventTickets\Api\Data;

use Magento\Catalog\Api\Data\ProductAttributeInterface as CatalogProductAttributeInterface;

/**
 * Interface ProductAttributeInterface
 * @api
 */
interface ProductAttributeInterface extends CatalogProductAttributeInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const CODE_AW_ET_REQUIRE_SHIPPING = 'aw_et_require_shipping';
    const CODE_AW_ET_SCHEDULE_TYPE = 'aw_et_schedule_type';
    const CODE_AW_ET_RECURRING_SCHEDULE_TYPE = 'aw_et_recurring_schedule_type';
    const CODE_AW_ET_START_DATE = 'aw_et_start_date';
    const CODE_AW_ET_END_DATE = 'aw_et_end_date';
    const CODE_AW_ET_VENUE_ID = 'aw_et_venue_id';
    const CODE_AW_ET_SPACE_ID = 'aw_et_space_id';
    const CODE_AW_ET_TICKET_SELLING_DEADLINE = 'aw_et_selling_deadline';
    const CODE_AW_ET_TICKET_SELLING_DEADLINE_DATE = 'aw_et_selling_deadline_date';
    const CODE_AW_ET_EARLY_BIRD_END_DATE = 'aw_et_early_bird_end_date';
    const CODE_AW_ET_LAST_DAYS_START_DATE = 'aw_et_last_days_start_date';
    const CODE_AW_ET_SECTOR_CONFIG = 'aw_et_sector_config';
    const CODE_AW_ET_PERSONAL_OPTIONS = 'aw_et_personal_options';
    /**#@-*/

    /**#@+
     * Constants defined for simple and configurable products
     */
    const CODE_AW_ET_EXCLUSIVE_PRODUCT = 'aw_et_exclusive_product';
    /**#@-*/

    /**
     * Recurring schedule key
     */
    const CODE_AW_ET_RECURRING_SCHEDULE = 'aw_et_recurring_schedule';
}
