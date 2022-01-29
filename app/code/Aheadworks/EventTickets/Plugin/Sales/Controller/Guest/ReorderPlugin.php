<?php
namespace Aheadworks\EventTickets\Plugin\Sales\Controller\Guest;

use Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Controller
    as ReorderController;
use Magento\Sales\Controller\Guest\Reorder as MagentoGuestReorderController;
use Magento\Framework\Controller\ResultInterface;

class ReorderPlugin
{
    /**
     * @var ReorderController
     */
    private $reorderController;

    /**
     * @param ReorderController $reorderController
     */
    public function __construct(
        ReorderController $reorderController
    ) {
        $this->reorderController = $reorderController;
    }

    /**
     * Call separate controller instead of the native one
     *
     * @param MagentoGuestReorderController $subject
     * @param callable $proceed
     * @return ResultInterface
     */
    public function aroundExecute(MagentoGuestReorderController $subject, callable $proceed): ResultInterface
    {
        return $this->reorderController->execute();
    }
}
