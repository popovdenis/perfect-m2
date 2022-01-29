<?php
namespace Aheadworks\EventTickets\Controller\Adminhtml\Space;

use Aheadworks\EventTickets\Api\SpaceRepositoryInterface;
use Aheadworks\EventTickets\Api\Data\SpaceInterface;
use Aheadworks\EventTickets\Api\Data\SpaceInterfaceFactory;
use Aheadworks\EventTickets\Ui\DataProvider\Space\FormDataProvider as SpaceFormDataProvider;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class Save
 *
 * @package Aheadworks\EventTickets\Controller\Adminhtml\Space
 */
class Save extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_EventTickets::spaces';

    /**
     * @var SpaceRepositoryInterface
     */
    private $spaceRepository;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var SpaceInterfaceFactory
     */
    private $spaceInterfaceFactory;

    /**
     * @var PostDataProcessor
     */
    private $postDataProcessor;

    /**
     * @param Context $context
     * @param SpaceRepositoryInterface $spaceRepository
     * @param SpaceInterfaceFactory $spaceInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataPersistorInterface $dataPersistor
     * @param PostDataProcessor $postDataProcessor
     */
    public function __construct(
        Context $context,
        SpaceRepositoryInterface $spaceRepository,
        SpaceInterfaceFactory $spaceInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        DataPersistorInterface $dataPersistor,
        PostDataProcessor $postDataProcessor
    ) {
        parent::__construct($context);
        $this->spaceRepository = $spaceRepository;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPersistor = $dataPersistor;
        $this->spaceInterfaceFactory = $spaceInterfaceFactory;
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

                $space = $this->performSave($data);

                $this->dataPersistor->clear(SpaceFormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY);
                $this->messageManager->addSuccessMessage(__('Space was successfully saved.'));

                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $space->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the space.')
                );
            }
            $this->dataPersistor->set(SpaceFormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY, $data);
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
     * @return SpaceInterface
     * @throws LocalizedException | \Exception
     */
    private function performSave($data)
    {
        $id = isset($data['id']) ? $data['id'] : false;
        $spaceObject = $id
            ? $this->spaceRepository->get($id)
            : $this->spaceInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $spaceObject,
            $data,
            SpaceInterface::class
        );

        return $this->spaceRepository->save($spaceObject);
    }
}
