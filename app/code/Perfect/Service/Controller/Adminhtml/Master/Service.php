<?php

namespace Perfect\Service\Controller\Adminhtml\Master;

use Magento\Backend\App\Action\Context;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Service
 *
 * @package Perfect\Service\Controller\Adminhtml\Master
 */
class Service extends \Magento\Backend\App\Action
{
    /**
     * @var \Perfect\Service\Api\ServiceRepositoryInterface
     */
    private $serviceRepository;

    /**
     * Service constructor.
     *
     * @param \Magento\Backend\App\Action\Context             $context
     * @param \Perfect\Service\Api\ServiceRepositoryInterface $serviceRepository
     */
    public function __construct(
        Context $context,
        \Perfect\Service\Api\ServiceRepositoryInterface $serviceRepository
    )
    {
        parent::__construct($context);
        $this->serviceRepository = $serviceRepository;
    }

    /**
     * Execute method.
     *
     * @return null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $masterId = (int) $this->getRequest()->getParam('masterId');

        if (empty($masterId)) {
            return $this->goBack([]);
        }

        $results = [];
        if ($services = $this->getServicesByMaster($masterId)) {
            /** @var \Perfect\Service\Api\Data\ServiceInterface $service */
            foreach ($services as $service) {
                $results[] = [
                    'service_id' => $service->getId(),
                    'service_name' => $service->getServiceName(),
                    'service_duration_h' => $service->getData('master_service_duration_h'),
                    'service_duration_m' => $service->getData('master_service_duration_m'),
                    'is_price_range' => $service->getData('is_price_range'),
                    'service_price_from' => $service->getData('service_price_from'),
                    'service_price_to' => $service->getData('service_price_to'),
                ];
            }
        }

        return $this->goBack($results);
    }

    /**
     * @param   $search
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getServicesByMaster($masterId)
    {
        return $this->serviceRepository->getServicesByMasterId($masterId);
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