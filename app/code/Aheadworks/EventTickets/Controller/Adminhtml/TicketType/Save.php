<?php
namespace Aheadworks\EventTickets\Controller\Adminhtml\TicketType;

use Aheadworks\EventTickets\Api\TicketTypeRepositoryInterface;
use Aheadworks\EventTickets\Api\Data\TicketTypeInterface;
use Aheadworks\EventTickets\Api\Data\TicketTypeInterfaceFactory;
use Aheadworks\EventTickets\Ui\DataProvider\TicketType\FormDataProvider as TicketTypeFormDataProvider;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class Save
 *
 * @package Aheadworks\EventTickets\Controller\Adminhtml\TicketType
 */
class Save extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_EventTickets::venues';

    /**
     * @var TicketTypeRepositoryInterface
     */
    private $ticketTypeRepository;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var TicketTypeInterfaceFactory
     */
    private $ticketTypeInterfaceFactory;

    /**
     * @var PostDataProcessor
     */
    private $postDataProcessor;

    /**
     * @param Context $context
     * @param TicketTypeRepositoryInterface $ticketTypeRepository
     * @param TicketTypeInterfaceFactory $ticketTypeInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataPersistorInterface $dataPersistor
     * @param PostDataProcessor $postDataProcessor
     */
    public function __construct(
        Context $context,
        TicketTypeRepositoryInterface $ticketTypeRepository,
        TicketTypeInterfaceFactory $ticketTypeInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        DataPersistorInterface $dataPersistor,
        PostDataProcessor $postDataProcessor
    ) {
        parent::__construct($context);
        $this->ticketTypeRepository = $ticketTypeRepository;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPersistor = $dataPersistor;
        $this->ticketTypeInterfaceFactory = $ticketTypeInterfaceFactory;
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

                $ticketType = $this->performSave($data);

                $this->dataPersistor->clear(TicketTypeFormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY);
                $this->messageManager->addSuccessMessage(__('Ticket type was successfully saved.'));

                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $ticketType->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the ticket type.')
                );
            }
            $this->dataPersistor->set(TicketTypeFormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY, $data);
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
     * @return TicketTypeInterface
     * @throws LocalizedException | \Exception
     */
    private function performSave($data)
    {
        $id = isset($data['id']) ? $data['id'] : false;
        $ticketTypeObject = $id
            ? $this->ticketTypeRepository->get($id)
            : $this->ticketTypeInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $ticketTypeObject,
            $data,
            TicketTypeInterface::class
        );

        return $this->ticketTypeRepository->save($ticketTypeObject);
    }
}
