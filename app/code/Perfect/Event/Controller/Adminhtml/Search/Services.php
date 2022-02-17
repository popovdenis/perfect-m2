<?php

namespace Perfect\Event\Controller\Adminhtml\Search;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Services
 *
 * @package Perfect\Event\Controller\Adminhtml\Search
 */
class Services extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * Services constructor.
     *
     * @param \Magento\Backend\App\Action\Context          $context
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        parent::__construct($context);
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Execute method.
     *
     * @return null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $search = $this->getRequest()->getParam('search');

        if (empty($search)) {
            return $this->goBack([]);
        }
    }

    /**
     * @param                                                    $search
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getServicesBySearch($search)
    {
        $searchBuilder = $this->searchCriteriaBuilder;
        $searchBuilder->addFilter(BaseEntityInterface::NAME, '%' . $search . '%', 'like');

        return $this->entityRepository->getEntities(
            $profile->getOrganisationProfile()->getMainTableName(),
            $searchBuilder->create()
        );
    }

    /**
     * @param array $providers
     *
     * @return string
     */
    private function goBack($providers)
    {
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($providers);
    }
}