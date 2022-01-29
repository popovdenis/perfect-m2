<?php
namespace Aheadworks\EventTickets\Ui\DataProvider\Space;

use Aheadworks\EventTickets\Api\Data\SpaceInterface;
use Aheadworks\EventTickets\Model\ResourceModel\Space\CollectionFactory;
use Aheadworks\EventTickets\Model\ResourceModel\Space\Collection;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class FormDataProvider
 *
 * @package Aheadworks\EventTickets\Ui\DataProvider\Space
 */
class FormDataProvider extends AbstractDataProvider
{
    /**
     * Key for saving and getting form data from data persistor
     */
    const DATA_PERSISTOR_FORM_DATA_KEY = 'aw_event_tickets_space';

    /**
     * Key for sector service field is_new
     */
    const IS_NEW_SECTOR_FIELD_KEY = 'is_new';

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

        if (!empty($dataFromForm) && (is_array($dataFromForm)) && (!empty($dataFromForm[SpaceInterface::ID]))) {
            $id = $dataFromForm[SpaceInterface::ID];
            $this->dataPersistor->clear(self::DATA_PERSISTOR_FORM_DATA_KEY);
            $preparedData[$id] = $dataFromForm;
        } else {
            $id = $this->request->getParam($this->getRequestFieldName());
            $spaces = $this->getCollection()->addFieldToFilter(SpaceInterface::ID, $id)->getItems();
            /** @var \Aheadworks\EventTickets\Model\Space $space */
            foreach ($spaces as $space) {
                if ($id == $space->getId()) {
                    $preparedData[$id] = $this->getPreparedSpaceData($space->getData());
                }
            }
        }

        return $preparedData;
    }

    /**
     * Retrieve array with prepared space data
     *
     * @param array $spaceData
     * @return array
     */
    private function getPreparedSpaceData($spaceData)
    {
        $preparedSpaceData = $spaceData;

        $preparedSpaceData[SpaceInterface::SECTORS] = $this->getPreparedSectorDataForSpace(
            $preparedSpaceData[SpaceInterface::SECTORS]
        );

        return $preparedSpaceData;
    }

    /**
     * Retrieve array with prepared sectors data for space
     *
     * @param mixed $sectorsData
     * @return array
     */
    private function getPreparedSectorDataForSpace($sectorsData)
    {
        $preparedSectorsData = [];
        if (!empty($sectorsData) && (is_array($sectorsData))) {
            foreach ($sectorsData as $sectorsDataRow) {
                if ($sectorsDataRow instanceof DataObject) {
                    $preparedSectorsDataRow = $sectorsDataRow->getData();
                } else {
                    $preparedSectorsDataRow = $sectorsDataRow;
                }
                if (!empty($preparedSectorsDataRow)
                    && (is_array($preparedSectorsDataRow))
                ) {
                    $preparedSectorsDataRow[self::IS_NEW_SECTOR_FIELD_KEY] = false;
                }
                $preparedSectorsData[] = $preparedSectorsDataRow;
            }
        }
        return $preparedSectorsData;
    }
}
