<?php
namespace Aheadworks\EventTickets\Model\Ticket\Generator;

use Aheadworks\EventTickets\Api\Data\NumberGenerationSettingsInterface;
use Aheadworks\EventTickets\Model\Ticket\Generator\Number\SettingsResolver;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;
use Aheadworks\EventTickets\Model\ResourceModel\Ticket\Validator\IsUnique as TicketIsUnique;

/**
 * Class Number
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Generator
 */
class Number
{
    /**
     * @var int
     */
    const GENERATION_ATTEMPTS = 1000;

    /**
     * @var array
     */
    private $numberParams = [];

    /**
     * @var TicketIsUnique
     */
    private $ticketIsUniqueValidator;

    /**
     * @var SettingsResolver
     */
    private $settingsResolver;

    /**
     * @param TicketIsUnique $ticketIsUniqueValidator
     * @param SettingsResolver $settingsResolver
     * @param string[] $numberParams
     */
    public function __construct(
        TicketIsUnique $ticketIsUniqueValidator,
        SettingsResolver $settingsResolver,
        $numberParams = []
    ) {
        $this->ticketIsUniqueValidator = $ticketIsUniqueValidator;
        $this->settingsResolver = $settingsResolver;
        $this->numberParams = $numberParams;
    }

    /**
     * Generate ticket number
     *
     * @param int $websiteId
     * @param NumberGenerationSettingsInterface|null $numberSettings
     * @return string|string[]
     * @throws LocalizedException
     * @throws \Exception
     */
    public function generate($websiteId, $numberSettings = null)
    {
        $numbers = [];
        $numberSettings = $this->settingsResolver->resolve($numberSettings, $websiteId);
        $qty = $numberSettings->getQty();
        while ($qty > 0) {
            $numbers[] = $this->generateOneNumber($numberSettings);
            $qty--;
        }

        return count($numbers) == 1 ? array_shift($numbers) : $numbers;
    }

    /**
     * Try generate one number
     *
     * @param NumberGenerationSettingsInterface $numberSettings
     * @return string
     * @throws LocalizedException
     * @throws \Exception
     */
    private function generateOneNumber($numberSettings)
    {
        $attempt = 0;
        do {
            if ($attempt >= self::GENERATION_ATTEMPTS) {
                throw new LocalizedException(__('Unable to create ticket number.'));
            }
            $number = $this->generateNumber($numberSettings);
            $attempt++;
        } while (!$this->ticketIsUniqueValidator->validate($number));

        return $number;
    }

    /**
     * Generate number
     *
     * @param NumberGenerationSettingsInterface $numberSettings
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function generateNumber($numberSettings)
    {
        $number = '';
        $charset = $this->getCharset($numberSettings->getFormat());
        $charsetLength = strlen($charset);
        $delimiterAtEvery = $numberSettings->getDelimiterAtEvery();
        for ($i = 0; $i < $numberSettings->getLength(); $i++) {
            $symbol = $charset[Random::getRandomNumber(0, $charsetLength - 1)];
            if (($delimiterAtEvery > 0) && (($i % $delimiterAtEvery) === 0) && ($i !== 0)) {
                $symbol = $numberSettings->getDelimiter() . $symbol;
            }
            $number .= $symbol;
        }

        return strtoupper($numberSettings->getPrefix() . $number . $numberSettings->getSuffix());
    }

    /**
     * Retrieve charset by format
     *
     * @param string $format
     * @return string
     */
    private function getCharset($format)
    {
        return $this->numberParams['charset'][$format];
    }
}
