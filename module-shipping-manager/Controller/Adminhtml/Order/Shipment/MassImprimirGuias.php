<?php

namespace Combinatoria\ShippingManager\Controller\Adminhtml\Order\Shipment;

ini_set('max_execution_time',3000);

use Magento\Backend\App\Action;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Webapi\Exception;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action\Context;
use Magento\Shipping\Model\Shipping\LabelGenerator;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Model\Order;
use Magento\Framework\Controller\Result\JsonFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class MassImprimirGuias extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Sales::shipment';

    /**
     * @var LabelGenerator
     */
    protected $labelGenerator;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var ShipmentCollectionFactory
     */
    protected $shipmentCollectionFactory;

    /**
     * @var ShipmentCollectionFactory
     */
    protected $_order;

    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    protected $_objectManager;

    protected $resultRawFactory;

    protected $_scopeConfig;
    /**
     * MassImprimirGuiasAndreani constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param FileFactory $fileFactory
     * @param LabelGenerator $labelGenerator
     * @param ShipmentCollectionFactory $shipmentCollectionFactory
     * @param Order $order
     * @param JsonFactory $resultJsonFactory
     * @param LoggerInterface $loggerInterface
     * @param \Magento\Framework\ObjectManagerInterface $objectmanager
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        FileFactory $fileFactory,
        LabelGenerator $labelGenerator,
        ShipmentCollectionFactory $shipmentCollectionFactory,
        Order $order,
        JsonFactory $resultJsonFactory,
        LoggerInterface $loggerInterface,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->fileFactory                  = $fileFactory;
        $this->collectionFactory            = $collectionFactory;
        $this->shipmentCollectionFactory    = $shipmentCollectionFactory;
        $this->labelGenerator               = $labelGenerator;
        $this->_order                       = $order;
        $this->_resultJsonFactory           = $resultJsonFactory;
        $this->logger                       = $loggerInterface;
        $this->_objectManager               = $objectmanager;
        $this->resultRawFactory             = $resultRawFactory;
        $this->_scopeConfig             = $scopeConfig;

        parent::__construct($context, $filter);
    }

    /**
     * @param AbstractCollection $collection
     * @return $this
     */
    protected function massAction(AbstractCollection $collection)
    {
        $files = array();
        $where = $collection->getSelect()->getPart(\Magento\Framework\DB\Select::WHERE);

        $max_orders = $this->_scopeConfig->getValue('shipping/shipping_manager/max_orders');
        if (count($where) == 0){
            $this->messageManager->addNoticeMessage(__('Seleccione al menos una orden.'));
        } elseif($max_orders && count($collection) > (int) $max_orders) {
            $this->messageManager->addNoticeMessage(__('Debe seleccionar un máximo de ' . $max_orders . ' órdenes.'));
        }else{
            foreach ($collection as $order){
                if(
                    $order->getShippingMethod()=='andreaniestandar_estandar' ||
                    $order->getShippingMethod()=='andreaniurgente_urgente' ||
                    $order->getShippingMethod()=='andreanisucursal_sucursal'
                ){
                    $andreani[] = $order->getEntityId();
                }

                if(
                    substr($order->getShippingMethod(),0,6)=='shipro'
                ){
                    $shipro[] = $order->getEntityId();
                }
            }

            if(isset($andreani)  && count($andreani) > 0){
                $andreaniModel = $this->_objectManager->create('Ids\Andreani\Model\GuiasMasivas');
                try{
                    $files[] = $andreaniModel->generarEnvios($andreani);
                }catch (\Exception $exception){
                    // Add your own failure logic here
                    $this->messageManager->addErrorMessage(__($exception->getMessage()));
                    $this->logger->error($exception->getMessage());

//                    $resultRedirect = $this->resultRedirectFactory->create()->setPath('shippingmanager/guias/admin');
//                    return $resultRedirect;
                }
            }

            if(isset($shipro)  && count($shipro) > 0){
                $shiproModel = $this->_objectManager->create('Ecomerciar\Shipro\Model\Shipment');
                try{
                    $files = array_merge($files,$shiproModel->generarEnvios($shipro));
                }catch (\Exception $exception){
                    // Add your own failure logic here
                    $this->messageManager->addErrorMessage(__($exception->getMessage()));
                    $this->logger->error($exception->getMessage());

//                    $resultRedirect = $this->resultRedirectFactory->create()->setPath('shippingmanager/guias/admin');
//                    return $resultRedirect;
                }
            }

            if(count($files) > 0){
                if(count($files) == 1){
                    try{
                        $fileName = basename($files[0]); // the name of the downloaded resource
                        $this->fileFactory->create(
                            $fileName,
                            [
                                'type' => 'filename',
                                'value' => $files[0]
                            ]
                        );
                    }catch (\Exception $exception){
                        // Add your own failure logic here
                        $this->messageManager->addErrorMessage(__($exception->getMessage()));
                        $this->logger->error($exception->getMessage());
                    }
                    $resultRaw = $this->resultRawFactory->create();
                    return $resultRaw;

                }else{
                    if (file_exists(BP."/var/export/Guias.zip")) {
                        unlink(BP."/var/export/Guias.zip");
                    }

                    $zip = new \ZipArchive();
                    $zip->open(BP."/var/export/Guias.zip", \ZIPARCHIVE::CREATE);
                    foreach ($files as $file){
                        if($file != null){
                            $zip->addFile($file,basename($file));
                        }
                    }
                    $zip->close();

                    try{
                        $fileName = "Guias.zip"; // the name of the downloaded resource
                        $this->fileFactory->create(
                            $fileName,
                            [
                                'type' => 'filename',
                                'value' => BP."/var/export/Guias.zip"
                            ]
                        );
                    }catch (\Exception $exception){
                        // Add your own failure logic here
                        $this->messageManager->addErrorMessage(__($exception->getMessage()));
                        $this->logger->error($exception->getMessage());
                    }
                    $resultRaw = $this->resultRawFactory->create();
                    return $resultRaw;
                }
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create()->setPath('shippingmanager/guias/admin');
        return $resultRedirect;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Combinatoria_ShippingManager::guias');
    }
}
