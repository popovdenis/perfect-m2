<?php

namespace Perfect\Event\Block\Adminhtml;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Backend\Block\Template\Context;

/**
 * Class TimetableConfig
 *
 * @package Perfect\Event\Block\Adminhtml
 */
class TimetableConfig extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    public function __construct(
        Context $context,
        SerializerInterface $serializer,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    )
    {
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
        $this->serializer = $serializer;
    }

    /**
     * Get configuration JSON.
     *
     * @return string
     */
    public function getConfig(): string
    {
        return $this->serializer->serialize([
            'save_event_url' => $this->getUrl(
                'perfect_event/timetable/save', ['_secure' => true]
            ),
            'delete_event_url' => $this->getUrl(
                'perfect_event/timetable/delete', ['_secure' => true]
            ),
            'client_search_url' => $this->getUrl(
                'perfect_event/timetable/search', ['_secure' => true]
            )
        ]);
    }
}