<?php
namespace Aheadworks\EventTickets\Model\Ticket\Generator\Number;

use Aheadworks\EventTickets\Api\Data\NumberGenerationSettingsInterface;
use Aheadworks\EventTickets\Api\Data\NumberGenerationSettingsInterfaceFactory;
use Aheadworks\EventTickets\Model\Config;
use Aheadworks\EventTickets\Model\Source\Ticket\NumberFormat;

/**
 * Class SettingsResolver
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Generator\Number
 */
class SettingsResolver
{
    /**
     * @var int
     */
    const DEFAULT_LENGTH = 8;

    /**
     * @var NumberGenerationSettingsInterfaceFactory
     */
    private $numberGenerationSettingsFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param NumberGenerationSettingsInterfaceFactory $numberGenerationSettingsFactory
     * @param Config $config
     */
    public function __construct(
        NumberGenerationSettingsInterfaceFactory $numberGenerationSettingsFactory,
        Config $config
    ) {
        $this->numberGenerationSettingsFactory = $numberGenerationSettingsFactory;
        $this->config = $config;
    }

    /**
     * Resolve number generation Settings
     *
     * @param NumberGenerationSettingsInterface|null $numberSettings
     * @param int $websiteId
     * @return NumberGenerationSettingsInterface
     */
    public function resolve($numberSettings, $websiteId)
    {
        if (empty($numberSettings) || !($numberSettings instanceof NumberGenerationSettingsInterface)) {
            $numberSettings = $this->getSettingsByDefault($websiteId);
        }

        $numberSettings
            ->setLength($this->resolveNumberLength($numberSettings->getLength()))
            ->setFormat($this->resolveNumberFormat($numberSettings->getFormat()))
            ->setDelimiterAtEvery($this->resolveDelimiterAtEvery($numberSettings->getDelimiterAtEvery()))
            ->setPrefix($this->preparePrefix($numberSettings->getPrefix()))
            ->setSuffix($this->prepareSuffix($numberSettings->getSuffix()))
            ->setDelimiter(
                $this->resolveDelimiter($numberSettings->getDelimiterAtEvery(), $numberSettings->getDelimiter())
            );

        return $numberSettings;
    }

    /**
     * Retrieve generation settings by default
     *
     * @param int $websiteId
     * @return NumberGenerationSettingsInterface
     */
    private function getSettingsByDefault($websiteId)
    {
        /** @var NumberGenerationSettingsInterface $numberGenerationSettings */
        $numberGenerationSettings = $this->numberGenerationSettingsFactory->create();
        $numberGenerationSettings
            ->setQty(1)
            ->setLength($this->config->getTicketNumberLength($websiteId))
            ->setFormat($this->config->getTicketNumberFormat($websiteId))
            ->setPrefix($this->config->getTicketNumberPrefix($websiteId))
            ->setSuffix($this->config->getTicketNumberSuffix($websiteId))
            ->setDelimiterAtEvery($this->config->getTicketNumberDashAtEvery($websiteId));

        return $numberGenerationSettings;
    }

    /**
     * Resolve number format
     *
     * @param string|null $format
     * @return string
     */
    private function resolveNumberFormat($format)
    {
        return !empty($format) ? $format : NumberFormat::ALPHANUMERIC;
    }

    /**
     * Resolve number length
     *
     * @param int $length
     * @return string
     */
    private function resolveNumberLength($length)
    {
        return max(self::DEFAULT_LENGTH, (int)$length);
    }

    /**
     * Resolve number length
     *
     * @param int $delimiterAtEvery
     * @return string
     */
    private function resolveDelimiterAtEvery($delimiterAtEvery)
    {
        return max(0, (int)$delimiterAtEvery);
    }

    /**
     * Resolve delimiter
     *
     * @param int $delimiterAtEvery
     * @param string|null $delimiter
     * @return string
     */
    private function resolveDelimiter($delimiterAtEvery, $delimiter)
    {
        if (empty($delimiter) && (int)$delimiterAtEvery > 0) {
            return '-';
        }
        return $delimiter;
    }

    /**
     * Prepare prefix
     *
     * @param string $prefix
     * @return string
     */
    private function preparePrefix($prefix)
    {
        return trim($prefix);
    }

    /**
     * Prepare suffix
     *
     * @param string $suffix
     * @return string
     */
    private function prepareSuffix($suffix)
    {
        return trim($suffix);
    }
}
