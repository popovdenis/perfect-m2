<?php
namespace Aheadworks\EventTickets\Model\ResourceModel;

use Aheadworks\EventTickets\Api\Data\AdditionalProductOptionsInterface;
use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Model\Source\Ticket\Status;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Ticket
 *
 * @package Aheadworks\EventTickets\Model\ResourceModel
 */
class Ticket extends AbstractResourceModel
{
    /**#@+
     * Constants defined for tables
     * used by corresponding entity
     */
    const MAIN_TABLE_NAME = 'aw_et_ticket';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, 'id');
    }

    /**
     * Get ticket identifier by number
     *
     * @param string $number
     * @return int|false
     * @throws LocalizedException
     */
    public function getIdByNumber($number)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), $this->getIdFieldName())
            ->where('number = :number');

        return $connection->fetchOne($select, ['number' => $number]);
    }

    /**
     * Get purchased tickets with grouping
     *
     * @param int $productId
     * @param int|array|null $sectorId
     * @return array
     * @throws LocalizedException
     */
    public function getPurchasedTickets($productId, $sectorId = null)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(
                $this->getMainTable(),
                [
                    TicketInterface::SECTOR_ID,
                    TicketInterface::EVENT_START_DATE,
                    TicketInterface::RECURRING_TIME_SLOT_ID,
                    'event_start_only_date' => new \Zend_Db_Expr('DATE(' . TicketInterface::EVENT_START_DATE . ')'),
                    AdditionalProductOptionsInterface::QTY => new \Zend_Db_Expr('COUNT(*)')
                ]
            )
            ->where(
                'status IN (?)',
                [ Status::UNUSED, Status::PENDING, Status::USED ]
            )
            ->where(
                'product_id = ?',
                $productId
            )
            ->where(
                'event_start_date > ?',
                new \DateTime()
            )
            ->group(
                [
                    'event_start_only_date',
                    TicketInterface::SECTOR_ID,
                    TicketInterface::RECURRING_TIME_SLOT_ID
                ]
            );

        if ($sectorId) {
            $select->where(
                'sector_id IN (?)',
                $sectorId
            );
        }

        return $connection->fetchAll($select);
    }

    /**
     * Get purchased tickets qty with grouping for event and sector
     *
     * @param int $productId
     * @param string $eventStartDate
     * @param int $timeSlot
     * @param int $sectorId
     * @return int
     * @throws LocalizedException
     */
    public function getPurchasedTicketsQtyForEvent($productId, $eventStartDate, $timeSlot, $sectorId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(
                $this->getMainTable(),
                [
                    AdditionalProductOptionsInterface::QTY => new \Zend_Db_Expr('COUNT(*)')
                ]
            )
            ->where(
                TicketInterface::STATUS . ' IN (?)',
                [ Status::UNUSED, Status::PENDING, Status::USED ]
            )
            ->where(
                TicketInterface::PRODUCT_ID . ' = ?',
                $productId
            )
            ->where(
                new \Zend_Db_Expr('DATE('. TicketInterface::EVENT_START_DATE .') = DATE(?)'),
                $eventStartDate
            )
            ->where(
                TicketInterface::RECURRING_TIME_SLOT_ID . ' = ?',
                $timeSlot
            )
            ->where(
                TicketInterface::SECTOR_ID . ' = ?',
                $sectorId
            );

        return (int)$connection->fetchOne($select);
    }
}
