<?php
namespace Aheadworks\EventTickets\Ui\DataProvider\TicketType;

use Aheadworks\EventTickets\Api\Data\TicketTypeInterface;
use Aheadworks\EventTickets\Model\ResourceModel\Ticket\Type\CollectionFactory;
use Aheadworks\EventTickets\Model\ResourceModel\Ticket\Type\Collection;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class FormDataProvider
 *
 * @package Aheadworks\EventTickets\Ui\DataProvider\TicketType
 */
class FormDataProvider extends AbstractDataProvider
{
    /**
     * Key for saving and getting form data from data persistor
     */
    const DATA_PERSISTOR_FORM_DATA_KEY = 'aw_event_tickets_tickettype';

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $preparedData = [];
        $dataFromForm = $this->dataPersistor->get(self::DATA_PERSISTOR_FORM_DATA_KEY);

        if (!empty($dataFromForm) && (is_array($dataFromForm)) && (!empty($dataFromForm[TicketTypeInterface::ID]))) {
            $id = $dataFromForm[TicketTypeInterface::ID];
            $this->dataPersistor->clear(self::DATA_PERSISTOR_FORM_DATA_KEY);
            $preparedData[$id] = $dataFromForm;
        } else {
            $id = $this->request->getParam($this->getRequestFieldName());
            $ticketTypes = $this->getCollection()->addFieldToFilter(TicketTypeInterface::ID, $id)->getItems();
            /** @var \Aheadworks\EventTickets\Model\Ticket\Type $ticketType */
            foreach ($ticketTypes as $ticketType) {
                if ($id == $ticketType->getId()) {
                    $preparedData[$id] = $this->getPreparedTicketTypeData($ticketType->getData());
                }
            }
        }

        return $preparedData;
    }

    /**
     * Retrieve array with prepared ticket type data
     *
     * @param array $ticketTypeData
     * @return array
     */
    private function getPreparedTicketTypeData($ticketTypeData)
    {
        return $ticketTypeData;
    }
}
