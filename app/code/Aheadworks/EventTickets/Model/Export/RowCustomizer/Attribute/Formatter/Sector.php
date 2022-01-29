<?php
namespace Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter;

use Aheadworks\EventTickets\Api\SectorRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Sector
 *
 * @package Aheadworks\EventTickets\Model\Export\RowCustomizer\Attribute\Formatter
 */
class Sector implements FormatterInterface
{
    /**
     * @var SectorRepositoryInterface
     */
    private $sectorRepository;

    /**
     * @param SectorRepositoryInterface $sectorRepository
     */
    public function __construct(
        SectorRepositoryInterface $sectorRepository
    ) {
        $this->sectorRepository = $sectorRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormattedValue($value)
    {
        try {
            $formattedValue = $this->sectorRepository->get($value)->getName();
        } catch (NoSuchEntityException $exception) {
            $formattedValue = '';
        }
        return $formattedValue;
    }
}
