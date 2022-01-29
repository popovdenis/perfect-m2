<?php
namespace Aheadworks\EventTickets\Model\Product\Type;

use Aheadworks\EventTickets\Api\Data\AttendeeInterface;
use Aheadworks\EventTickets\Api\Data\BuyRequest\AttendeeOptionInterface;
use Aheadworks\EventTickets\Api\Data\OptionInterface;
use Aheadworks\EventTickets\Api\Data\ProductAttributeInterface;
use Aheadworks\EventTickets\Api\Data\ProductPersonalOptionInterface;
use Aheadworks\EventTickets\Api\Data\ProductSectorTicketInterface;
use Aheadworks\EventTickets\Api\Data\TimeSlotInterface;
use Aheadworks\EventTickets\Api\StockManagementInterface;
use Aheadworks\EventTickets\Model\Product\Layout\Processor\View\Recurring\Provider;
use Aheadworks\EventTickets\Model\Product\RecurringSchedule\Ticket\AvailableTicket\Resolver as RecurringResolver;
use Aheadworks\EventTickets\Model\Product\RecurringSchedule\TimeSlot;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\Recurring\TicketSellingDeadline;
use Aheadworks\EventTickets\Model\Source\Product\Attribute\ScheduleType;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Aheadworks\EventTickets\Api\Data\ProductSectorInterface;
use Magento\Framework\DataObject;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Option as CatalogProductOption;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Catalog\Model\Product\Type as CatalogProductType;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\DateTime;
use Magento\MediaStorage\Helper\File\Storage\Database as FileStorageDatabase;
use Magento\Framework\Filesystem;
use Magento\Framework\Registry;
use Magento\Quote\Api\Data\CartItemInterface;
use Psr\Log\LoggerInterface;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket\ByRequest\Validator as BuyRequestValidator;
use Aheadworks\EventTickets\Model\Product\Option\Extractor as OptionExtractor;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket\Generator\Sku as SkuGenerator;
use Aheadworks\EventTickets\Model\Product\Type\EventTicket\Resolver\RequireShipping as RequireShippingResolver;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductAttributeSourceStatus;

/**
 * Class EventTicket
 *
 * @package Aheadworks\EventTickets\Model\Product\Type
 */
class EventTicket extends AbstractType
{
    /**
     * Product type code
     */
    const TYPE_CODE = 'aw_event_ticket';

    /**
     * {@inheritdoc}
     */
    protected $_canConfigure = true;

    /**
     * @var BuyRequestValidator
     */
    private $buyRequestValidator;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var OptionExtractor
     */
    private $optionExtractor;

    /**
     * @var SkuGenerator
     */
    private $skuGenerator;

    /**
     * @var RequireShippingResolver
     */
    private $requireShippingResolver;

    /**
     * @var StockManagementInterface
     */
    private $stockManagement;

    /**
     * @var RecurringResolver
     */
    private $recurringResolver;

    /**
     * @var bool
     */
    private $productLoadFlag = false;

    /**
     * @param CatalogProductOption $catalogProductOption
     * @param EavConfig $eavConfig
     * @param CatalogProductType $catalogProductType
     * @param EventManagerInterface $eventManager
     * @param FileStorageDatabase $fileStorageDb
     * @param Filesystem $filesystem
     * @param Registry $coreRegistry
     * @param LoggerInterface $logger
     * @param ProductRepositoryInterface $productRepository
     * @param BuyRequestValidator $buyRequestValidator
     * @param PriceCurrencyInterface $priceCurrency
     * @param OptionExtractor $optionExtractor
     * @param SkuGenerator $skuGenerator
     * @param RequireShippingResolver $requireShippingResolver
     * @param StockManagementInterface $stockManagement
     * @param RecurringResolver $recurringResolver
     */
    public function __construct(
        CatalogProductOption $catalogProductOption,
        EavConfig $eavConfig,
        CatalogProductType $catalogProductType,
        EventManagerInterface $eventManager,
        FileStorageDatabase $fileStorageDb,
        Filesystem $filesystem,
        Registry $coreRegistry,
        LoggerInterface $logger,
        ProductRepositoryInterface $productRepository,
        BuyRequestValidator $buyRequestValidator,
        PriceCurrencyInterface $priceCurrency,
        OptionExtractor $optionExtractor,
        SkuGenerator $skuGenerator,
        RequireShippingResolver $requireShippingResolver,
        StockManagementInterface $stockManagement,
        RecurringResolver $recurringResolver
    ) {
        parent::__construct(
            $catalogProductOption,
            $eavConfig,
            $catalogProductType,
            $eventManager,
            $fileStorageDb,
            $filesystem,
            $coreRegistry,
            $logger,
            $productRepository
        );
        $this->buyRequestValidator = $buyRequestValidator;
        $this->priceCurrency = $priceCurrency;
        $this->optionExtractor = $optionExtractor;
        $this->skuGenerator = $skuGenerator;
        $this->requireShippingResolver = $requireShippingResolver;
        $this->stockManagement = $stockManagement;
        $this->recurringResolver = $recurringResolver;
    }

    /**
     * Check if require shipping
     *
     * @param Product $product
     * @return bool|null
     */
    public function isRequireShipping(Product $product)
    {
        $requireShippingAttribute =
            $this->getAttribute($product, ProductAttributeInterface::CODE_AW_ET_REQUIRE_SHIPPING);
        $requireShipping = $requireShippingAttribute !== [] ? $requireShippingAttribute : null;

        return null === $requireShipping ? $requireShipping : (bool)(int)$requireShipping;
    }

    /**
     * Retrieve ticket selling deadline type
     *
     * @param Product $product
     * @return int
     */
    public function getTicketSellingDeadlineType(Product $product)
    {
        if ($this->isRecurring($product)) {
            $recurringSchedule = $product->getExtensionAttributes()->getAwEtRecurringSchedule();
            return $recurringSchedule
                ? $recurringSchedule->getSellingDeadlineType()
                : TicketSellingDeadline::EVENT_START_DATE;
        }

        return $this->getAttribute($product, ProductAttributeInterface::CODE_AW_ET_TICKET_SELLING_DEADLINE);
    }

    /**
     * Retrieve ticket schedule type
     *
     * @param Product $product
     * @return string
     */
    public function getScheduleType(Product $product)
    {
        return $this->getAttribute($product, ProductAttributeInterface::CODE_AW_ET_SCHEDULE_TYPE);
    }

    /**
     * Get is product recurring
     *
     * @param Product $product
     * @return bool
     */
    public function isRecurring($product)
    {
        return $this->getScheduleType($product) == ScheduleType::RECURRING;
    }

    /**
     * Retrieve ticket selling deadline custom date
     *
     * @param Product $product
     * @return string
     */
    public function getTicketSellingDeadlineCustomDate(Product $product)
    {
        return $this->getAttribute($product, ProductAttributeInterface::CODE_AW_ET_TICKET_SELLING_DEADLINE_DATE);
    }

    /**
     * Retrieve event start date
     *
     * @param Product $product
     * @return string
     */
    public function getEventStartDate(Product $product)
    {
        return $this->getAttribute($product, ProductAttributeInterface::CODE_AW_ET_START_DATE);
    }

    /**
     * Retrieve event end date
     *
     * @param Product $product
     * @return string
     */
    public function getEventEndDate(Product $product)
    {
        return $this->getAttribute($product, ProductAttributeInterface::CODE_AW_ET_END_DATE);
    }

    /**
     * Retrieve early bird price end date
     *
     * @param Product $product
     * @return string
     */
    public function getEarlyBirdEndDate(Product $product)
    {
        return $this->getAttribute($product, ProductAttributeInterface::CODE_AW_ET_EARLY_BIRD_END_DATE);
    }

    /**
     * Retrieve last days price start date
     *
     * @param Product $product
     * @return string
     */
    public function getLastDaysStartDate(Product $product)
    {
        return $this->getAttribute($product, ProductAttributeInterface::CODE_AW_ET_LAST_DAYS_START_DATE);
    }

    /**
     * Retrieves amounts of product
     *
     * @param Product $product
     * @return array
     */
    public function getAmounts(Product $product)
    {
        $amounts = [];
        $this->setLoadFlag(false);
        $sectorConfig = (array)$this->getSectorConfig($product);
        /** @var ProductSectorInterface $sector */
        foreach ($sectorConfig as $sector) {
            foreach ($sector->getSectorTickets() as $ticket) {
                $amounts[] = $ticket->getFinalPrice();
            }
        }

        return $amounts;
    }

    /**
     * Retrieves sector config of product
     *
     * @param Product $product
     * @return array
     */
    public function getSectorConfig(Product $product)
    {
        // Load additional attributes by entity manager
        if (empty($product->getExtensionAttributes()->getAwEtSectorConfig())) {
            $product->getResource()->load($product, $product->getId());
        }
        return $product->getExtensionAttributes()->getAwEtSectorConfig();
    }

    /**
     * Retrieves personal options of product
     *
     * @param Product $product
     * @param bool $all
     * @return ProductPersonalOptionInterface[]
     */
    public function getPersonalOptions(Product $product, $all = true)
    {
        // Load additional attributes by entity manager
        $this->getAttribute($product, ProductAttributeInterface::CODE_AW_ET_PERSONAL_OPTIONS);
        $preparedPersonalOptions = $personalOptions =
            (array)$product->getExtensionAttributes()->getAwEtPersonalOptions();

        if (!$all) {
            $sectorId = $this->getProductCustomOptions($product)->getAwEtSectorId();
            $ticketTypeId = $this->getProductCustomOptions($product)->getAwEtTicketTypeId();
            if (!empty($sectorId) && !empty($ticketTypeId)) {
                $preparedPersonalOptions = [];
                foreach ($personalOptions as $option) {
                    if ($this->isOptionAvailableForTicket($option->getUid(), $product, $sectorId, $ticketTypeId)) {
                        $preparedPersonalOptions[] = $option;
                    }
                }
            }
        }

        return $preparedPersonalOptions;
    }

    /**
     * Check if is option available for ticket
     *
     * @param string $optionUid
     * @param Product $product
     * @param int $sectorId
     * @param int $ticketTypeId
     * @return bool
     */
    public function isOptionAvailableForTicket($optionUid, Product $product, $sectorId, $ticketTypeId)
    {
        if ($this->isAllPersonalOptionEmpty($product)) {
            $result = true;
        } else {
            $ticket = $this->getTicketFromSectorConfig($product, $sectorId, $ticketTypeId);
            $result = $ticket && in_array($optionUid, $ticket->getPersonalOptionUids());
        }

        return $result;
    }

    /**
     * Retrieve personal option from options array of product by given option id
     *
     * @param Product $product
     * @param int $optionId
     * @return ProductPersonalOptionInterface|null
     */
    public function getOptionById($product, $optionId)
    {
        $options = $this->getPersonalOptions($product);
        if (!empty($options)) {
            foreach ($options as $option) {
                if ($option->getId() == $optionId) {
                    return $option;
                }
            }
        }

        return null;
    }

    /**
     * Retrieves available ticket qty
     *
     * @param Product $product
     * @return int
     */
    public function getAvailableTicketQty(Product $product)
    {
        $qty = 0;
        $sectorConfig = $this->getSectorConfig($product);
        /** @var ProductSectorInterface $sector */
        foreach ($sectorConfig as $sector) {
            $qty += $sector->getQtyAvailableTickets();
        }

        if ($this->isRecurring($product)) {
            $qty = 1;
        }

        return $qty;
    }

    /**
     * Retrieves available ticket qty by sector
     *
     * @param Product $product
     * @param int $sectorId
     * @param CartItemInterface $quoteItem
     * @return int
     * @throws LocalizedException
     */
    public function getAvailableTicketQtyBySector(Product $product, $sectorId, $quoteItem = null)
    {
        if ($sector = $this->getSectorFromSectorConfig($product, $sectorId)) {
            if ($quoteItem && $this->isRecurring($product)) {
                return $this->recurringResolver->getAvailableTicketQtyBySector($sectorId, $quoteItem);
            }

            return $sector->getQtyAvailableTickets();
        }

        return 0;
    }

    /**
     * Check if free tickets in sector
     *
     * @param Product $product
     * @param int $sectorId
     * @return bool
     */
    public function isFreeTicketsInSector(Product $product, $sectorId)
    {
        $sector = $this->getSectorFromSectorConfig($product, $sectorId);
        if (null !== $sector) {
            $sectorTickets = $sector->getSectorTickets();
            if (is_array($sectorTickets)) {
                return $this->isFreeTickets($product, $sectorTickets);
            }
        }

        return false;
    }

    /**
     * Check if free tickets
     *
     * @param Product $product
     * @return bool
     */
    public function isFreeTicketsByProduct(Product $product)
    {
        $tickets = [];
        $sectorConfig = $this->getSectorConfig($product);
        /** @var ProductSectorInterface $sector */
        foreach ($sectorConfig as $sector) {
            $sectorTickets = $sector->getSectorTickets();
            if (is_array($sectorTickets)) {
                $tickets = array_merge($tickets, $sectorTickets);
            }
        }

        return $this->isFreeTickets($product, $tickets);
    }

    /**
     * {@inheritdoc}
     */
    public function isSalable($product)
    {
        $salable = $product->getStatus() == ProductAttributeSourceStatus::STATUS_ENABLED;
        if ($salable) {
            $salable = $this->stockManagement->isSalable($product->getId());
        }

        return $salable;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderOptions($product)
    {
        $objectOptions = $this->getProductCustomOptions($product);
        $arrayOptions = $this->optionExtractor->extractFromObject($objectOptions);

        $info = $product->getCustomOption('info_buyRequest');
        if ($info) {
            $arrayOptions['info_buyRequest'] = $this->serializer->unserialize($info->getValue());
        }
        return $arrayOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function processBuyRequest($product, $buyRequest)
    {
        $arrayOptions = [];
        $awEtTickets = $buyRequest->getAwEtTickets();
        if (is_array($awEtTickets)) {
            $arrayOptions = reset($awEtTickets);
        }
        $arrayOptions[OptionInterface::BUY_REQUEST_PRODUCT_IS_CONFIGURE] = true;

        return $arrayOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function isVirtual($product)
    {
        return !$this->requireShippingResolver->resolve($product);
    }

    /**
     * {@inheritdoc}
     */
    public function getSku($product)
    {
        $sku = parent::getSku($product);

        return $this->skuGenerator->generate($sku, $product);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteTypeSpecificData(Product $product)
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareOptions(DataObject $buyRequest, $product, $processMode)
    {
        $preparedSlots = [];
        if (is_array($buyRequest->getAwEtSlots())) {
            $preparedSlots = $this->prepareTimeSlotsByRequest($buyRequest);
        }
        $buyRequest->setAwEtSlots($preparedSlots);

        $preparedTickets = [];

        if (!$this->isRecurring($product)) {
            if (is_array($buyRequest->getAwEtTickets())) {
                $preparedTickets = $this->prepareTicketOptionsByRequest($buyRequest);
            }
            if (empty($preparedTickets)) {
                $preparedTickets = $this->prepareTicketOptionsWithoutSpecifyingQty($buyRequest, $product);
            }
        }

        $buyRequest->setAwEtTickets($preparedTickets);

        return $preparedTickets;
    }

    /**
     * {@inheritdoc}
     */
    public function checkProductBuyState($product)
    {
        $attendeeOptions = $this->getPersonalOptions($product, false);
        if ($product->getSkipCheckRequiredOption() || empty($attendeeOptions)) {
            return $this;
        }

        $objectOptions = $this->getProductCustomOptions($product);
        $productAttendees = $objectOptions->getAwEtAttendees() ? : [];
        foreach ($attendeeOptions as $attendeeOption) {
            $optionFound = $optionCounted = 0;
            if ($attendeeOption->isRequire()) {
                /** @var AttendeeInterface $productAttendee */
                foreach ($productAttendees as $productAttendee) {
                    if ($attendeeOption->getId() == $productAttendee->getProductOption()->getId()) {
                        $optionCounted++;
                        if (!empty($productAttendee->getValue())) {
                            $optionFound++;
                        }
                    }
                }
                if (($optionFound == 0 && $optionCounted == 0) || $optionFound != $optionCounted) {
                    $product->setSkipCheckRequiredOption(true);
                    throw new LocalizedException(__('The product has required personal options.'));
                }
            }
        }

        return $this;
    }

    /**
     * Retrieve product custom options
     *
     * @param Product $product
     * @return OptionInterface
     */
    public function getProductCustomOptions($product)
    {
        $customOptions = [];
        /** @var \Magento\Quote\Model\Quote\Item\Option $option */
        foreach ($product->getCustomOptions() as $option) {
            $customOptions[$option->getCode()] = $option->getValue();
        }

        return $this->optionExtractor->extractFromArray($customOptions, $product);
    }

    /**
     * Check if all ticket personal options is empty
     *
     * @param Product $product
     * @return bool
     */
    public function isAllPersonalOptionEmpty(Product $product)
    {
        $sectorConfig = $this->getSectorConfig($product);
        /** @var ProductSectorInterface $sector */
        foreach ($sectorConfig as $sector) {
            foreach ($sector->getSectorTickets() as $ticket) {
                if (!empty($ticket->getPersonalOptionUids())) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareProduct(DataObject $buyRequest, $product, $processMode)
    {
        $isStrictProcessMode = $this->_isStrictProcessMode($processMode);
        try {
            $this->_prepareOptions($buyRequest, $product, $processMode);
            $this->buyRequestValidator->validate($buyRequest, $product, $isStrictProcessMode);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return $this->divideProduct($buyRequest, $product);
    }

    /**
     * Divide the product into additional products
     *
     * @param DataObject $buyRequest
     * @param Product $product
     * @return Product[]
     * @throws \Exception
     */
    private function divideProduct($buyRequest, $product)
    {
        $products = [];
        foreach ($buyRequest->getAwEtTickets() as $ticket) {
            $products[] = $this->prepareProductForTicket($buyRequest, $ticket, $product);
        }

        foreach ($buyRequest->getAwEtSlots() as $slot) {
            foreach ($slot[OptionInterface::BUY_REQUEST_AW_ET_TICKETS] as $ticket) {
                $newProduct = $this->prepareProductForTicket($buyRequest, $ticket, $product);
                if ($this->isRecurring($newProduct)) {
                    $timeSlotId = $slot[OptionInterface::RECURRING_TIME_SLOT_ID];
                    $newProduct
                        ->addCustomOption(
                            OptionInterface::RECURRING_START_DATE,
                            $this->prepareTime(
                                $slot[OptionInterface::RECURRING_START_DATE],
                                $product,
                                $timeSlotId,
                                TimeSlotInterface::START_TIME
                            )
                        )
                        ->addCustomOption(
                            OptionInterface::RECURRING_END_DATE,
                            $this->prepareTime(
                                $slot[OptionInterface::RECURRING_START_DATE],
                                $product,
                                $timeSlotId,
                                TimeSlotInterface::END_TIME
                            )
                        )
                        ->addCustomOption(
                            OptionInterface::RECURRING_TIME_SLOT_ID,
                            $timeSlotId
                        );
                }
                $this->attachAttendeeOptions($newProduct, $ticket);

                $products[] = $newProduct;
            }
        }

        $this->updateByRequestQtyForCartProductConfigure($buyRequest, $products);

        return $products;
    }

    /**
     * Prepare product for ticket
     *
     * @param DataObject $buyRequest
     * @param array $ticket
     * @param Product $product
     * @return Product
     * @throws \Exception
     */
    private function prepareProductForTicket($buyRequest, $ticket, $product)
    {
        $price = $product->getPriceModel()->getSelectionFinalPrice(
            $product,
            $ticket[OptionInterface::BUY_REQUEST_SECTOR_ID],
            $ticket[OptionInterface::BUY_REQUEST_TYPE_ID]
        );
        $newProduct = clone $product;
        $newProduct
            ->setCartQty($ticket[OptionInterface::BUY_REQUEST_QTY])
            ->setQty($ticket[OptionInterface::BUY_REQUEST_QTY])
            ->addCustomOption(
                OptionInterface::SECTOR_ID,
                $ticket[OptionInterface::BUY_REQUEST_SECTOR_ID],
                $newProduct
            )->addCustomOption(
                OptionInterface::TICKET_TYPE_ID,
                $ticket[OptionInterface::BUY_REQUEST_TYPE_ID],
                $newProduct
            )->addCustomOption(OptionInterface::AMOUNT, $price, $newProduct)
            ->addCustomOption('info_buyRequest', $this->divideProductBuyRequest($buyRequest, $ticket));
        $this->attachAttendeeOptions($newProduct, $ticket);

        return $newProduct;
    }

    /**
     * Divide product buyRequest
     *
     * @param DataObject $buyRequest
     * @param array $ticket
     * @return string|bool
     */
    private function divideProductBuyRequest($buyRequest, $ticket)
    {
        $infoBuyRequest = $buyRequest->getData();
        $infoBuyRequest[OptionInterface::BUY_REQUEST_AW_ET_TICKETS] = [
            [
                OptionInterface::BUY_REQUEST_QTY => $ticket[OptionInterface::BUY_REQUEST_QTY],
                OptionInterface::BUY_REQUEST_SECTOR_ID => $ticket[OptionInterface::BUY_REQUEST_SECTOR_ID],
                OptionInterface::BUY_REQUEST_TYPE_ID => $ticket[OptionInterface::BUY_REQUEST_TYPE_ID],
                OptionInterface::BUY_REQUEST_ATTENDEE => $ticket[OptionInterface::BUY_REQUEST_ATTENDEE],
                OptionInterface::BUY_REQUEST_CONFIGURED => true
            ]
        ];
        $infoBuyRequest[OptionInterface::BUY_REQUEST_QTY] = $ticket[OptionInterface::BUY_REQUEST_QTY];

        return $this->serializer->serialize($infoBuyRequest);
    }

    /**
     * Attach attendee options
     *
     * @param Product $newProduct
     * @param array $ticket
     * @return void
     */
    private function attachAttendeeOptions($newProduct, $ticket)
    {
        $attendeeOptions = $this->getPersonalOptions($newProduct, false);
        if (empty($attendeeOptions)) {
            return;
        }

        $optionIds = [];
        $attendees = $ticket[OptionInterface::BUY_REQUEST_ATTENDEE];
        foreach ($attendeeOptions as $option) {
            $optionIds[] = $option->getId();
            foreach ($attendees as $number => $attendee) {
                if (!isset($attendee[$option->getId()])) {
                    continue;
                }
                $optName = $this->optionExtractor->composeAttendeeOptionName($option->getId(), $number);
                $optVal = $attendee[$option->getId()];

                $newProduct->addCustomOption($optName, $optVal, $newProduct);
            }
        }
        $newProduct->addCustomOption(
            OptionInterface::OPTION_ATTENDEE_IDS,
            $this->optionExtractor->composeOptionIds($optionIds),
            $newProduct
        );
        $newProduct->addCustomOption(
            OptionInterface::ATTENDEE_IDS,
            $this->optionExtractor->composeOptionIds(array_keys($attendees)),
            $newProduct
        );
    }

    /**
     * Update qty option in byRequest for cart product configure
     *
     * @param DataObject $buyRequest
     * @param Product[] $products
     */
    private function updateByRequestQtyForCartProductConfigure($buyRequest, $products)
    {
        if (count($products) == 1) {
            $product = $products[0];
            $buyRequest->setQty($product->getQty());
        }
    }

    /**
     * Retrieves sector by $sectorId
     *
     * @param Product $product
     * @param int $sectorId
     * @return ProductSectorInterface|null
     */
    private function getSectorFromSectorConfig(Product $product, $sectorId)
    {
        $sectorConfig = $this->getSectorConfig($product);
        /** @var ProductSectorInterface $sector */
        foreach ($sectorConfig as $sector) {
            if ($sector->getSectorId() == $sectorId) {
                return $sector;
            }
        }

        return null;
    }

    /**
     * Retrieves available ticket qty by sector
     *
     * @param Product $product
     * @param int $sectorId
     * @param int $ticketTypeId
     * @return ProductSectorTicketInterface|null
     */
    private function getTicketFromSectorConfig(Product $product, $sectorId, $ticketTypeId)
    {
        if ($sector = $this->getSectorFromSectorConfig($product, $sectorId)) {
            foreach ($sector->getSectorTickets() as $ticket) {
                if ($ticket->getTypeId() == $ticketTypeId) {
                    return $ticket;
                }
            }
        }

        return null;
    }

    /**
     * Check if free tickets
     *
     * @param Product $product
     * @param array $tickets
     * @return bool
     */
    private function isFreeTickets(Product $product, array $tickets)
    {
        $priceFreeCount = 0;
        foreach ($tickets as $ticket) {
            if ($ticket->getFinalPrice() == 0) {
                $priceFreeCount++;
            }
        }
        return count($tickets) == $priceFreeCount;
    }

    /**
     * Prepare ticket options by request
     *
     * @param DataObject $buyRequest
     * @return array
     */
    private function prepareTicketOptionsByRequest($buyRequest)
    {
        $preparedTickets = [];
        foreach ($buyRequest->getAwEtTickets() as $ticket) {
            $qty = $this->resolveQtyOptionByRequest($buyRequest, $ticket);
            if (!is_numeric($qty) || empty($qty)) {
                continue;
            }

            $ticket[OptionInterface::BUY_REQUEST_QTY] = $qty;
            $ticket[OptionInterface::BUY_REQUEST_ATTENDEE] = $this->prepareTicketAttendeeOptions($ticket);
            $preparedTickets[] = $ticket;
        }
        return $preparedTickets;
    }

    /**
     * Prepare time slots by request
     *
     * @param DataObject $buyRequest
     * @return array
     */
    private function prepareTimeSlotsByRequest($buyRequest)
    {
        $preparedSlots = [];
        foreach ($buyRequest->getAwEtSlots() as $slot) {
            $preparedTickets = [];
            foreach ($slot[OptionInterface::BUY_REQUEST_AW_ET_TICKETS] as $ticket) {
                $qty = $this->resolveQtyOptionByRequest($buyRequest, $ticket);
                if (!is_numeric($qty) || empty($qty)) {
                    continue;
                }

                $ticket[OptionInterface::BUY_REQUEST_QTY] = $qty;
                $ticket[OptionInterface::BUY_REQUEST_ATTENDEE] = $this->prepareTicketAttendeeOptions($ticket);
                $preparedTickets[] = $ticket;
            }

            $preparedSlots[] = [
                OptionInterface::RECURRING_START_DATE => $slot[OptionInterface::RECURRING_START_DATE],
                OptionInterface::RECURRING_TIME_SLOT_ID => $slot[OptionInterface::RECURRING_TIME_SLOT_ID],
                OptionInterface::BUY_REQUEST_AW_ET_TICKETS => $preparedTickets
            ];
        }

        return $preparedSlots;
    }

    /**
     * Resolve qty
     *
     * @param DataObject $buyRequest
     * @param array $entity
     * @return mixed
     */
    private function resolveQtyOptionByRequest($buyRequest, $entity)
    {
        if (isset($entity[OptionInterface::BUY_REQUEST_CONFIGURED])) {
            $qty = $buyRequest->getQty();
        } else {
            $qty = isset($entity[OptionInterface::BUY_REQUEST_QTY])
                ? $entity[OptionInterface::BUY_REQUEST_QTY]
                : 0;
        }

        return $qty;
    }

    /**
     * Prepare ticket options without specifying qty
     *
     * @param DataObject $buyRequest
     * @param Product $product
     * @return array
     */
    private function prepareTicketOptionsWithoutSpecifyingQty(DataObject $buyRequest, $product)
    {
        $preparedTicket = [];
        $sectorConfig = $this->getSectorConfig($product);
        $firstSector = reset($sectorConfig);
        if (count($sectorConfig) == 1
            && $firstSector instanceof ProductSectorInterface && count($firstSector->getSectorTickets()) == 1
        ) {
            $sectorTickets = $firstSector->getSectorTickets();
            $firstTicket = reset($sectorTickets);

            //need get qty from buy request because magento set min qty from inventory config automatically
            $qty = $buyRequest->getQty() ?: 1;

            $preparedTicket[] = [
                OptionInterface::BUY_REQUEST_QTY => $qty,
                OptionInterface::BUY_REQUEST_SECTOR_ID => $firstSector->getSectorId(),
                OptionInterface::BUY_REQUEST_TYPE_ID => $firstTicket->getTypeId(),
                OptionInterface::BUY_REQUEST_ATTENDEE => $this->prepareTicketAttendeeOptions([])
            ];
        }

        return $preparedTicket;
    }

    /**
     * Prepare ticket attendee options
     *
     * @param array $ticket
     * @return array
     */
    private function prepareTicketAttendeeOptions($ticket)
    {
        $preparedAttendees = [];
        $attendees = isset($ticket[OptionInterface::BUY_REQUEST_ATTENDEE])
            ? $ticket[OptionInterface::BUY_REQUEST_ATTENDEE]
            : [];
        $attendeeOptions = isset($ticket[OptionInterface::BUY_REQUEST_ATTENDEE_OPTIONS])
            ? $ticket[OptionInterface::BUY_REQUEST_ATTENDEE_OPTIONS]
            : [];

        if ($attendees) {
            foreach ($attendees as $attendeeOptions) {
                $preparedAttendeeOptions = [];
                foreach ($attendeeOptions as $optionId => $option) {
                    $preparedOption = trim($option);
                    $preparedAttendeeOptions[$optionId] = $preparedOption;
                }
                if (empty($preparedAttendeeOptions)) {
                    continue;
                }
                $preparedAttendees[] = $preparedAttendeeOptions;
            }
        } elseif ($attendeeOptions) {
            foreach ($attendeeOptions as $attendeeOption) {
                $ticketNumber = $attendeeOption[AttendeeOptionInterface::TICKET_NUMBER];
                $optionId = $attendeeOption[AttendeeOptionInterface::OPTION_ID];
                $optionValue = trim($attendeeOption[AttendeeOptionInterface::OPTION_VALUE]);

                $preparedAttendees[$ticketNumber][$optionId] = $optionValue;
            }
        }

        return $preparedAttendees;
    }

    /**
     * Retrieve product attribute by code
     *
     * @param Product $product
     * @param string $code
     * @return mixed
     */
    private function getAttribute(Product $product, $code)
    {
        if (!$product->hasData($code)) {
            if (!$this->productLoadFlag) {
                $this->setLoadFlag(true);
                $product->getResource()->load($product, $product->getId());
            } else {
                $product->setData(
                    $code,
                    $product->getResource()->getAttributeRawValue($product->getId(), $code, $product->getStoreId())
                );
            }
        }
        return $product->getData($code);
    }

    /**
     * Prepare event time
     *
     * @param string $recurringEventDate
     * @param Product $product
     * @param string $timeSlotId
     * @param string $timeKey
     * @return string|null
     * @throws \Exception
     */
    private function prepareTime($recurringEventDate, $product, $timeSlotId, $timeKey)
    {
        $recurringSchedule = $product->getExtensionAttributes()->getAwEtRecurringSchedule();
        /** @var TimeSlot $timeSlot */
        if ($recurringSchedule) {
            foreach ($recurringSchedule->getTimeSlots() as $timeSlot) {
                if ($timeSlot->getId() == $timeSlotId) {
                    $date = new \DateTime($recurringEventDate);
                    $time = new \DateTime($timeSlot->getData($timeKey));

                    return $date->format(DateTime::DATE_PHP_FORMAT)
                        . ' ' . $time->format(Provider::CALENDAR_TIME_FORMAT);
                }
            }
        }

        return null;
    }

    /**
     * Set load state flag
     *
     * @param bool $flag
     */
    private function setLoadFlag($flag)
    {
        $this->productLoadFlag = $flag;
    }
}
