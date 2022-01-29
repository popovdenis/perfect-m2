<?php
namespace Aheadworks\EventTickets\Api\Data;

/**
 * Interface StorefrontLabelsEntityInterface
 * @api
 */
interface StorefrontLabelsEntityInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const LABELS = 'labels';
    const CURRENT_LABELS = 'current_labels';
    /**#@-*/

    /**
     * Get array of labels on storefront per store view
     *
     * @return \Aheadworks\EventTickets\Api\Data\StorefrontLabelsInterface[]
     */
    public function getLabels();

    /**
     * Set array of labels on storefront per store view
     *
     * @param \Aheadworks\EventTickets\Api\Data\StorefrontLabelsInterface[] $labelsRecordsArray
     * @return $this
     */
    public function setLabels($labelsRecordsArray);

    /**
     * Get labels on storefront for current store view
     *
     * @return \Aheadworks\EventTickets\Api\Data\StorefrontLabelsInterface
     */
    public function getCurrentLabels();

    /**
     * Set labels on storefront for current store view
     *
     * @param \Aheadworks\EventTickets\Api\Data\StorefrontLabelsInterface $labelsRecord
     * @return $this
     */
    public function setCurrentLabels($labelsRecord);
}
