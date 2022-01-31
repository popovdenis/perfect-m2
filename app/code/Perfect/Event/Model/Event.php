<?php

namespace Perfect\Event\Model;

use Magento\Framework\Model\AbstractModel;
use Perfect\Event\Api\Data\EventInterface;

/**
 * Class Event
 *
 * @package Perfect\Event\Model
 */
class Event extends AbstractModel implements EventInterface
{
    /**
     * Model cache tag for clear cache in after save and after delete.
     *
     * @const string
     */
    const CACHE_TAG = 'perfect_event';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'perfect_event';

    /**
     * Profile Id setter.
     *
     * @param int $id
     *
     * @return self
     */
    public function setId($id): EventInterface
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * Organisation Id getter.
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setTitle($title): EventInterface
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * @inheritdoc
     */
    public function setEnabled($flag): EventInterface
    {
        return $this->setData(self::ENABLED, $flag);
    }

    /**
     * @inheritdoc
     */
    public function getEnabled()
    {
        return $this->getData(self::ENABLED);
    }
}
