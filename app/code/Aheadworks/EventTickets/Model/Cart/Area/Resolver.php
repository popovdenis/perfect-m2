<?php
namespace Aheadworks\EventTickets\Model\Cart\Area;

use Magento\Quote\Api\Data\CartInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Framework\App\State as AppState;
use Magento\Framework\App\Area;

/**
 * Class Resolver
 * @package Aheadworks\EventTickets\Model\Cart\Area
 */
class Resolver
{
    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var AppState
     */
    private $appState;

    /**
     * @param CartRepositoryInterface $cartRepository
     * @param AppState $appState
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        AppState $appState
    ) {
        $this->cartRepository = $cartRepository;
        $this->appState = $appState;
    }

    /**
     * Returns quote depending of current area
     *
     * @param int $cartId
     * @return CartInterface|Quote
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function resolve($cartId)
    {
        $areaCode = $this->appState->getAreaCode();
        if ($areaCode == Area::AREA_FRONTEND) {
            /** @var Quote $quote */
            $quote = $this->cartRepository->getActive($cartId);
        } else {
            $quote = $this->cartRepository->get($cartId);
        }

        return $quote;
    }
}
