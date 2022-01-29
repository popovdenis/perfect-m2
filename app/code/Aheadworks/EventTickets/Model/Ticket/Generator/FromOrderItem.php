<?php
namespace Aheadworks\EventTickets\Model\Ticket\Generator;

use Aheadworks\EventTickets\Api\Data\OptionInterface;
use Aheadworks\EventTickets\Api\Data\TicketInterface;
use Aheadworks\EventTickets\Api\Data\TicketInterfaceFactory;
use Aheadworks\EventTickets\Api\TicketRepositoryInterface;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\ScheduleType;
use Aheadworks\EventTickets\Model\Source\Ticket\Status;
use Aheadworks\EventTickets\Model\Ticket\Generator\FromOrderItem\Resolver\TicketEntity as TicketEntityResolver;
use Magento\Sales\Model\Order\Item;
use Aheadworks\EventTickets\Model\Ticket\Generator\Number as TicketNumberGenerator;
use Aheadworks\EventTickets\Model\Product\Option\Extractor as OptionExtractor;
use Aheadworks\EventTickets\Model\Ticket\Generator\FromOrderItem\Resolver\Options as OptionsResolver;
use Aheadworks\EventTickets\Model\Source\Email\Status as EmailStatus;

/**
 * Class FromOrderItem
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Generator
 */
class FromOrderItem
{
    /**
     * @var OptionExtractor
     */
    private $optionExtractor;

    /**
     * @var TicketNumberGenerator
     */
    private $ticketNumberGenerator;

    /**
     * @var TicketInterfaceFactory
     */
    private $ticketDataFactory;

    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @var TicketEntityResolver
     */
    private $ticketEntityResolver;

    /**
     * @var array
     */
    private $byItemOptions;

    /**
     * @param OptionExtractor $optionExtractor
     * @param TicketNumberGenerator $ticketNumberGenerator
     * @param TicketInterfaceFactory $ticketDataFactory
     * @param TicketRepositoryInterface $ticketRepository
     * @param OptionsResolver $optionsResolver
     * @param TicketEntityResolver $ticketEntityResolver
     */
    public function __construct(
        OptionExtractor $optionExtractor,
        TicketNumberGenerator $ticketNumberGenerator,
        TicketInterfaceFactory $ticketDataFactory,
        TicketRepositoryInterface $ticketRepository,
        OptionsResolver $optionsResolver,
        TicketEntityResolver $ticketEntityResolver
    ) {
        $this->optionExtractor = $optionExtractor;
        $this->ticketNumberGenerator = $ticketNumberGenerator;
        $this->ticketDataFactory = $ticketDataFactory;
        $this->ticketRepository = $ticketRepository;
        $this->optionsResolver = $optionsResolver;
        $this->ticketEntityResolver = $ticketEntityResolver;
    }

    /**
     * Generate tickets
     *
     * @param Item $item
     * @return TicketInterface[]|bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function generateTickets($item)
    {
        $qty = $this->calculateQtyByItem($item);
        if ($qty > 0) {
            return $this->generateTicketInQty($item, $qty);
        }

        return false;
    }

    /**
     * Generate tickets in the given qty
     *
     * @param Item $item
     * @param int $qty
     * @return TicketInterface[]
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function generateTicketInQty($item, $qty)
    {
        $ticketNumbers = [];
        $ticketObjects = [];
        $order = $item->getOrder();
        $product = $item->getProduct();
        $options = $this->getItemOptions($item);
        $storeId = $order->getStoreId();
        $venueId = $product->getAwEtVenueId();
        for ($qtyNumber = 1; $qtyNumber <= $qty; $qtyNumber++) {
            /** @var TicketInterface $ticketObject */
            $ticketObject = $this->ticketDataFactory->create();
            $ticketType = $this->ticketEntityResolver->resolveTicketType($options->getAwEtTicketTypeId(), $storeId);
            $sector = $this->ticketEntityResolver->resolveSector($options->getAwEtSectorId(), $storeId);
            $venue = $this->ticketEntityResolver->resolveVenue($venueId, $storeId);

            $ticketObject
                ->setOrderId($order->getId())
                ->setProductId($item->getProductId())
                ->setStoreId($order->getStoreId())
                ->setNumber($this->ticketNumberGenerator->generate($order->getStore()->getWebsiteId()))
                ->setStatus(Status::PENDING)
                ->setEmailSent(EmailStatus::READY_FOR_SENDING)
                ->setTicketTypeId($options->getAwEtTicketTypeId())
                ->setVenueId($venueId)
                ->setSectorId($options->getAwEtSectorId())
                ->setCustomerId($order->getCustomerId())
                ->setCustomerName($order->getCustomerName())
                ->setCustomerEmail($order->getCustomerEmail())
                ->setBasePrice($item->getBasePrice())
                ->setBaseOriginalPrice($item->getBaseOriginalPrice())
                ->setSectorStorefrontTitle($sector->getCurrentLabels()->getTitle())
                ->setTicketTypeStorefrontTitle($ticketType->getCurrentLabels()->getTitle())
                ->setEventTitle($product->getName())
                ->setEventAddress($venue->getAddress())
                ->setEventDescription($product->getDescription())
                ->setEventStartDate($product->getAwEtStartDate())
                ->setEventEndDate($product->getAwEtEndDate())
                ->setEventImage($product->getImage())
                ->setOptions($this->optionsResolver->resolve($options, $qtyNumber));

            if ($product->getAwEtScheduleType() == ScheduleType::RECURRING) {
                $ticketObject
                    ->setEventStartDate($options->getAwEtRecurringStartDate())
                    ->setEventEndDate($options->getAwEtRecurringEndDate())
                    ->setRecurringTimeSlotId($options->getAwEtRecurringTimeSlotId());
            }

            $this->ticketRepository->save($ticketObject);

            $ticketNumbers[] = $ticketObject->getNumber();
            $ticketObjects[] = $ticketObject;
        }
        $this->updateItemOptions($item, $options, $ticketNumbers);

        return $ticketObjects;
    }

    /**
     * Retrieve item options
     *
     * @param Item $item
     * @return OptionInterface
     */
    private function getItemOptions($item)
    {
        $itemId = $item->getItemId();
        if (!isset($this->byItemOptions[$itemId])) {
            $this->byItemOptions[$itemId] = $this->optionExtractor->extractFromArray($item->getProductOptions());
        }

        return $this->byItemOptions[$itemId];
    }

    /**
     * Calculate qty to generate by item
     *
     * @param Item $item
     * @return int|bool
     */
    private function calculateQtyByItem($item)
    {
        $createdTicketNumbers = $this->getItemOptions($item)->getAwEtTicketNumbers() ? : [];
        $createdTicketsQty = count($createdTicketNumbers);
        $allowedQty = $item->getQtyOrdered() - $createdTicketsQty;

        return $allowedQty;
    }

    /**
     * Update order item options
     *
     * @param Item $item
     * @param OptionInterface $options
     * @param array $ticketNumbers
     * @return void
     * @throws \Exception
     */
    private function updateItemOptions($item, $options, $ticketNumbers)
    {
        $ticketNumbers = $options->getAwEtTicketNumbers()
            ? array_merge($options->getAwEtTicketNumbers(), $ticketNumbers)
            : $ticketNumbers;
        $options->setAwEtTicketNumbers($ticketNumbers);

        $arrayOptions = $this->optionExtractor->extractFromObject($options);
        $info = $item->getProductOptionByCode('info_buyRequest');
        if ($info) {
            $arrayOptions['info_buyRequest'] = $info;
        }
        $item->setProductOptions($arrayOptions)->save();
    }
}
