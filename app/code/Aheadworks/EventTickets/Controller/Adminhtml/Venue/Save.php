<?php

namespace Aheadworks\EventTickets\Controller\Adminhtml\Venue;

use Aheadworks\EventTickets\Api\VenueRepositoryInterface;
use Aheadworks\EventTickets\Api\Data\VenueInterface;
use Aheadworks\EventTickets\Api\Data\VenueInterfaceFactory;
use Aheadworks\EventTickets\Ui\DataProvider\Venue\FormDataProvider as VenueFormDataProvider;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class Save
 *
 * @package Aheadworks\EventTickets\Controller\Adminhtml\Venue
 */
class Save extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_EventTickets::venues';

    /**
     * @var VenueRepositoryInterface
     */
    private $venueRepository;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var VenueInterfaceFactory
     */
    private $venueInterfaceFactory;

    /**
     * @var PostDataProcessor
     */
    private $postDataProcessor;

    /**
     * @param Context $context
     * @param VenueRepositoryInterface $venueRepository
     * @param VenueInterfaceFactory $venueInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataPersistorInterface $dataPersistor
     * @param PostDataProcessor $postDataProcessor
     */
    public function __construct(
        Context $context,
        VenueRepositoryInterface $venueRepository,
        VenueInterfaceFactory $venueInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        DataPersistorInterface $dataPersistor,
        PostDataProcessor $postDataProcessor
    ) {
        parent::__construct($context);
        $this->venueRepository = $venueRepository;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPersistor = $dataPersistor;
        $this->venueInterfaceFactory = $venueInterfaceFactory;
        $this->postDataProcessor = $postDataProcessor;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPostValue()) {
            try {
                $data = $this->postDataProcessor->prepareEntityData($data);

                $venue = $this->performSave($data);

                $this->dataPersistor->clear(VenueFormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY);
                $this->messageManager->addSuccessMessage(__('Venue was successfully saved.'));

                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $venue->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the venue.')
                );
            }
            $this->dataPersistor->set(VenueFormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY, $data);
            $id = isset($data['id']) ? $data['id'] : false;
            if ($id) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $id, '_current' => true]);
            }
            return $resultRedirect->setPath('*/*/new', ['_current' => true]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Perform save
     *
     * @param array $data
     * @return VenueInterface
     * @throws LocalizedException | \Exception
     */
    private function performSave($data)
    {
        $id = isset($data['id']) ? $data['id'] : false;
        $venueObject = $id
            ? $this->venueRepository->get($id)
            : $this->venueInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $venueObject,
            $data,
            VenueInterface::class
        );

        return $this->venueRepository->save($venueObject);
    }
}
