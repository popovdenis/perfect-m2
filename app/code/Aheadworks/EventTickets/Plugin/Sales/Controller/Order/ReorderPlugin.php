<?php
namespace Aheadworks\EventTickets\Plugin\Sales\Controller\Order;

use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Controller\Order\Reorder
    as MagentoReorderController;
use Aheadworks\EventTickets\Model\Sales\Reorder\Magento243\Controller
    as ReorderController;

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
     * @param MagentoReorderController $subject
     * @param callable $proceed
     * @return ResultInterface
     */
    public function aroundExecute(MagentoReorderController $subject, callable $proceed): ResultInterface
    {
        return $this->reorderController->execute();
    }
}
