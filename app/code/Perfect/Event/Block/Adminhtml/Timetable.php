<?php

namespace Perfect\Event\Block\Adminhtml;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * Class Timetable
 *
 * @package Perfect\Event\Block\Adminhtml
 */
class Timetable extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $jsonEncoder;

    /**
     * Timetable constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Serialize\Serializer\Json     $jsonEncoder
     * @param array                                            $data
     * @param \Magento\Framework\Json\Helper\Data|null         $jsonHelper
     * @param \Magento\Directory\Helper\Data|null              $directoryHelper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Serialize\Serializer\Json $jsonEncoder,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    )
    {
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
        $this->jsonEncoder = $jsonEncoder;
    }

    /**
     * @return bool|false|string
     */
    public function getConfig()
    {
        return $this->jsonEncoder->serialize(['scheduler' => '#scheduler']);
    }
}