<?php
namespace Aheadworks\EventTickets\Controller\Adminhtml\Product;

use Aheadworks\EventTickets\Model\Source\Entity\Status;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\EventTickets\Api\Data\SectorInterface;
use Aheadworks\EventTickets\Api\SectorRepositoryInterface;

/**
 * Class SectorConfig
 *
 * @package Aheadworks\EventTickets\Controller\Adminhtml\Product
 */
class SectorConfig extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Magento_Catalog::products';

    /**
     * @var SectorRepositoryInterface
     */
    private $sectorRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param Context $context
     * @param SectorRepositoryInterface $sectorRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        Context $context,
        SectorRepositoryInterface $sectorRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        parent::__construct($context);
        $this->sectorRepository = $sectorRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Sector configuration action
     *
     * @return Json
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            $result['sectorConfigData'] = $this->getSectorConfigData();
        } catch (\Exception $exception) {
            $result = [
                'error' => $exception->getMessage(),
                'errorcode' => $exception->getCode()
            ];
        }
        return $resultJson->setData($result);
    }

    /**
     * Retrieve sector config data
     *
     * @return array
     */
    private function getSectorConfigData()
    {
        $sectorConfigData = [];

        $sectors = $this->getSectorsForCurrentSpace();

        /** @var SectorInterface $sector */
        foreach ($sectors as $sector) {
            $sectorConfigData[] = [
                'sector_id' => $sector->getId(),
                'sector' => $sector->getName()
            ];
        }

        return $sectorConfigData;
    }

    /**
     * Retrieve sectors array for current space
     *
     * @return SectorInterface[]|array
     */
    private function getSectorsForCurrentSpace()
    {
        try {
            $currentSpaceId = $this->getRequest()->getParam('space_id');
            $this->searchCriteriaBuilder
                ->addFilter(SectorInterface::STATUS, Status::STATUS_ENABLED)
                ->addFilter(SectorInterface::SPACE_ID, $currentSpaceId);
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $searchResults = $this->sectorRepository->getList($searchCriteria);
            $sectors = $searchResults->getItems();
        } catch (LocalizedException $e) {
            $sectors = [];
        }
        return $sectors;
    }
}
