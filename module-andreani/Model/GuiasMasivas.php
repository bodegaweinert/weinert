<?php

namespace Ids\Andreani\Model;

ini_set('max_execution_time',300);

use Ids\Andreani\Helper\Data as AndreaniHelper;
use Ids\Andreani\Model\Webservice as AndreaniWS;
use Magento\Framework\Model\ResourceModel\Db\TransactionManager;
use Magento\Sales\Model\Convert\Order as ConvertOrder;
use Magento\Shipping\Model\ShipmentNotifier;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\Shipment\Track;
use Magento\Catalog\Model\Product;
use Magento\Shipping\Model\Shipping\LabelGenerator;
use Magento\Shipping\Model\Shipping\LabelsFactory;
use Magento\Shipping\Model\CarrierFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order\Shipment\TrackFactory;
use Combinatoria\OrderWorkflow\Helper\Data as OrderWorkflowHelper;
use Combinatoria\OrderWorkflow\Model\Workflow as OrderWorkflow;
use Magento\Sales\Model\Order;

/**
 * Class GuiasMasivas
 * @package Ids\Andreani\Model
 */
class GuiasMasivas
{
    /**
     * @var
     */
    protected $_webService;

    /**
     * @var AndreaniHelper
     */
    protected $_andreaniHelper;

    /**
     * @var AndreaniHelper
     */
    protected $_convertOrder;

    /**
     * @var ShipmentNotifier
     */
    protected $_shipmentNotifier;

    /**
     * @var Track
     */
    protected $_track;

    /**
     * @var ShipmentCollectionFactory
     */
    protected $shipment;

    /**
     * @var LabelGenerator
     */
    protected $_labelGenerator;

    /**
     * @var Product
     */
    protected $_product;

    /**
     * @var \Magento\Shipping\Model\Shipping\LabelsFactory
     */
    protected $_labelFactory;

    /**
     * @var CarrierFactory
     */
    protected $_carrierFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var TrackFactory
     */
    protected $_trackFactory;

    /**
     * @var OrderWorkflowHelper
     * */
    protected $_orderWorkflowHelper;



    /**
     * GuiasMasivas constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param AndreaniHelper $andreaniHelper
     * @param \Ids\Andreani\Model\Webservice $webservice
     * @param ConvertOrder $convertOrder
     * @param ShipmentNotifier $shipmentNotifier
     * @param Shipment $shipment
     * @param Product $product
     * @param Track $track
     * @param LabelGenerator $labelGenerator
     * @param LabelsFactory $labelFactory
     * @param CarrierFactory $carrierFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param TrackFactory $trackFactory
     * @param OrderWorkflowHelper $orderWorkflowHelper
     * @param array $data
     * @internal param ShipmentTrackCreationInterface $shipmentTrackCreationInterface
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        AndreaniHelper $andreaniHelper,
        Webservice $webservice,
        ConvertOrder $convertOrder,
        ShipmentNotifier $shipmentNotifier,
        Shipment $shipment,
        Product $product,
        Track $track,
        LabelGenerator $labelGenerator,
        LabelsFactory $labelFactory,
        CarrierFactory $carrierFactory,
        ScopeConfigInterface $scopeConfig,
        TrackFactory $trackFactory,
        OrderWorkflowHelper $orderWorkflowHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        ShipmentCollectionFactory $shipmentCollectionFactory,
        Order $order,
        AndreaniWS $_webservice,
        \Magento\Framework\App\Action\Context $contextAction,
        array $data = []
    )
    {
        $this->_andreaniHelper      = $andreaniHelper;
        $this->_webService          = $webservice;
        $this->_convertOrder        = $convertOrder;
        $this->_shipmentNotifier    = $shipmentNotifier;
        $this->shipment             = $shipment;
        $this->_track               = $track;
        $this->_product             = $product;
        $this->_labelGenerator      = $labelGenerator;
        $this->_labelFactory        = $labelFactory;
        $this->_carrierFactory      = $carrierFactory;
        $this->_scopeConfig         = $scopeConfig;
        $this->_trackFactory        = $trackFactory;
        $this->_orderWorkflowHelper = $orderWorkflowHelper;
        $this->_messageManager      = $messageManager;
        $this->shipmentCollectionFactory    = $shipmentCollectionFactory;
        $this->_order                       = $order;
        $this->_webservice                  = $_webservice;
        $this->_view = $contextAction->getView();
    }

    /**
     * @description Obtiene los datos de la orden y consulta el WS, y,  a partir de eso datos
     * inserta el Json serializado en la orden del envío. Posteriormente genera una petición
     * de generar un envío siempre y cuando la orden que llega por parámetros no haya sido enviada
     * previamente.
     * @param $order
     * @return \Magento\Framework\DataObject|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function doShipmentRequest($order)
    {
        $helper = $this->_andreaniHelper;

        if ($helper->getGeneracionGuiasConfig() == '1') {
            $this->_requestByOrder($order);
        } else {
            $this->_requestByOrderItems($order);
        }
    }

    /**
     * @description Hace la petición al WS de Andreani para obtener el objeto con los datos
     * para armar la guía. Este método está activo cuando se ha seleccionado que se confeccione
     * una guía por ítems en la orden.
     * @param $orderItem
     * @return bool|\Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _requestByOrderItems($orderItem)
    {
        $result         = new \Magento\Framework\DataObject();
        $helper         = $this->_andreaniHelper;
        $webservice     = $this->_webService;
        $volumen        = 0;
        $productName    = '';
        $dataGuia       = [];

        foreach($orderItem as $_item)
        {
            if($_item->getProductType() == 'configurable')
                continue;

            $options = $_item->getProductOptions();
            if(isset($options['bundle_selection_attributes'])){
                continue;
            }

            $order          = $_item->getOrder();
            $_producto      = $helper->getLoadProduct($_item['product_id']);
            $pesoTotal      = $_producto->getWeight() * 1000;
            $volumen        += (int) $_producto->getResource()->getAttributeRawValue($_producto->getId(),'volumen',$_producto->getStoreId());
            $productName    = $_item['name'];

            $carrierParams                                  = [];
            $carrierParams['provincia']                     = $order->getShippingAddress()->getRegion();
            $carrierParams['localidad']                     = $order->getShippingAddress()->getCity();
            $carrierParams['codigopostal']                  = preg_replace('/[^0-9]/', '', $order->getShippingAddress()->getPostCode());
            $carrierParams['calle']                         = $order->getShippingAddress()->getStreetLine(1).' '.$order->getShippingAddress()->getStreetLine(2);
            $carrierParams['numero']                        = $order->getShippingAddress()->getStreetNumber()? $order->getShippingAddress()->getStreetNumber() : '';
            $carrierParams['piso']                          = $order->getShippingAddress()->getStreetFloor()? $order->getShippingAddress()->getStreetFloor() : '';
            $carrierParams['departamento']                  = $order->getShippingAddress()->getStreetApartment()? $order->getShippingAddress()->getStreetApartment() : '';
            $carrierParams['nombre']                        = $order->getShippingAddress()->getFirstname();
            $carrierParams['apellido']                      = $order->getShippingAddress()->getLastname();
            $carrierParams['nombrealternativo']             = '';
            $carrierParams['apellidoalternativo']           = '';
            $carrierParams['tipodedocumento']               = 'DNI';
            $carrierParams['numerodedocumento']             = $order->getShippingAddress()->getDni()? $order->getShippingAddress()->getDni() : '';
            $carrierParams['email']                         = $order->getCustomerEmail();
            $carrierParams['telefonofijo']                  = $order->getShippingAddress()->getTelephone();
            $carrierParams['telefonocelular']               = $order->getShippingAddress()->getTelephone()? $order->getShippingAddress()->getTelephone() : '';
            $carrierParams['categoriapeso']                 = 1;//TODO próximos versiones implementación de acuerdo a la lógica de  negocio
            $carrierParams['peso']                          = $pesoTotal;
            $carrierParams['detalledeproductosaentregar']   = $productName;
            $carrierParams['detalledeproductosaretirar']    = $productName;
            $carrierParams['volumen']                       = $volumen;
            $carrierParams['valordeclaradoconiva']          = $_item->getPrice();
            $carrierParams['idcliente']                     = 'Orden nro: ' . $order->getIncrementId();
            $carrierParams['sucursalderetiro']              = $order->getCodigoSucursalAndreani()? $order->getCodigoSucursalAndreani() : '';
            $carrierParams['sucursaldelcliente']            = '';
            $carrierParams['increment_id']                  = $order->getIncrementId();
            $carrierCode = explode('_',$order->getShippingMethod());

            for($i=0;$i<$_item['qty_ordered'];$i++) {
                $dataGuia[] = $webservice->GenerarEnviosDeEntregaYRetiroConDatosDeImpresion($carrierParams, $carrierCode[0]);
            }
        }

        if (!count($dataGuia))
        {
            $result->setErrors('Hubo un error al generar el envío');
            return $result;
        }
        return $this->_doShipmentByOrderItems($order,$dataGuia);

    }

    /**
     * @description Hace la petición al WS de Andreani para obtener el objeto con los datos
     * para armar la guía. Este método está activo cuando se ha seleccionado que se confeccione
     * una guía por orden.
     * @param $order
     * @return \Magento\Framework\DataObject|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _requestByOrder($order)
    {
        $result = new \Magento\Framework\DataObject();
        $helper = $this->_andreaniHelper;

        $volumen = 0;
        $productName = '';
        $packageWeight = 0;

        foreach ($order->getAllItems() as $_item)
        {
            if($_item->getProductType() == 'configurable')
                continue;

            $webservice = $this->_webService;

            $_producto = $helper->getLoadProduct($_item['product_id']);

            $packageWeight += $_item->getWeight();
            $producto = $this->_product;
            $volumen += (int)$_producto->getResource()->getAttributeRawValue($_producto->getId(), 'volumen', $_producto->getStoreId()) * $_item['qty_ordered'];
            $productName .= $_item['name'] . ', ';
        }

        $productName    = rtrim(trim($productName), ",");
        $pesoTotal      = $packageWeight * 1000;

        $carrierParams = [];
        $carrierParams['provincia']                     = $order->getShippingAddress()->getRegion();
        $carrierParams['localidad']                     = $order->getShippingAddress()->getCity();
        $carrierParams['codigopostal']                  = preg_replace('/[^0-9]/', '', $order->getShippingAddress()->getPostCode());
        $carrierParams['calle']                         = $order->getShippingAddress()->getStreetLine(1) . ' ' . $order->getShippingAddress()->getStreetLine(2);
        $carrierParams['numero']                        = $order->getShippingAddress()->getStreetNumber() ? $order->getShippingAddress()->getStreetNumber() : '';
        $carrierParams['piso']                          = $order->getShippingAddress()->getStreetFloor() ? $order->getShippingAddress()->getStreetFloor() : '';
        $carrierParams['departamento']                  = $order->getShippingAddress()->getStreetApartment() ? $order->getShippingAddress()->getStreetApartment() : '';
        $carrierParams['nombre']                        = $order->getShippingAddress()->getFirstname();
        $carrierParams['apellido']                      = $order->getShippingAddress()->getLastname();
        $carrierParams['nombrealternativo']             = '';
        $carrierParams['apellidoalternativo']           = '';
        $carrierParams['tipodedocumento']               = 'DNI';
        $carrierParams['numerodedocumento']             = $order->getShippingAddress()->getDni() ? $order->getShippingAddress()->getDni() : '';
        $carrierParams['email']                         = $order->getCustomerEmail();
        $carrierParams['telefonofijo']                  = $order->getShippingAddress()->getTelephone();
        $carrierParams['telefonocelular']               = $order->getShippingAddress()->getTelephone() ? $order->getShippingAddress()->getTelephone() : '';
        $carrierParams['categoriapeso']                 = 1;//TODO próximos versiones implementación de acuerdo a la lógica de  negocio
        $carrierParams['peso']                          = $pesoTotal;
        $carrierParams['detalledeproductosaentregar']   = $productName;
        $carrierParams['detalledeproductosaretirar']    = $productName;
        $carrierParams['volumen']                       = $volumen;
        $carrierParams['valordeclaradoconiva']          = $order->getTotalDue();
        $carrierParams['idcliente']                     = 'Orden nro: ' . $order->getIncrementId();
        $carrierParams['sucursalderetiro']              = $order->getCodigoSucursalAndreani() ? $order->getCodigoSucursalAndreani() : '';
        $carrierParams['sucursaldelcliente']            = '';
        $carrierParams['increment_id']                  = $order->getIncrementId();
        $carrierCode                                    = explode('_', $order->getShippingMethod());

        $dataGuia[0] = $webservice->GenerarEnviosDeEntregaYRetiroConDatosDeImpresion($carrierParams, $carrierCode[0]);

        if (!$dataGuia[0])
        {
            throw new \Exception(
                __("Hubo un error al generar el envío.")
            );
        }
        else
        {
            if($dataGuia[0]['datosguia']->GenerarEnviosDeEntregaYRetiroConDatosDeImpresionResult->CodigoDeResultado == 1){
                return $this->_doShipmentByOrder($order, $dataGuia);
            }else{
                throw new \Exception(
                    __("Hubo un error al generar el envío.")
                );
            }
        }
    }

    /**
     * @description Se encarga de generar el envío por cada ítem de la orden, y a su vez,
     * generar una número de envío por cada ítem.
     * @param $order
     * @param $dataGuia
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _doShipmentByOrderItems($order,$dataGuia)
    {
        $response = [];
        //$order = $orderItem->getOrder();
        if (!$order->canShip())
        {
            return false;
        }

        $helper       = $this->_andreaniHelper;
        $convertOrder = $this->_convertOrder;
        $shipment     = $convertOrder->toShipment($order);

        $valorTotal = $pesoTotal = 0;
        $itemsArray = [];

        $packages = [];
        $trackingsAndreani = [];

        foreach ($order->getAllItems() as $orderItem)
        {
            if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                continue;
            }

            $qtyShipped = $orderItem->getQtyToShip();;
            $shipmentItem = $convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);

            $valorTotal += $qtyShipped * $orderItem->getPrice();
            $pesoTotal  += $qtyShipped * $orderItem->getWeight();

            $itemsArray[$orderItem->getId()] = [
                'qty' => $qtyShipped,
                'customs_value' => $orderItem->getPrice(),
                'price' => $orderItem->getPrice(),
                'name' => $orderItem->getName(),
                'weight'=> $orderItem->getWeight(),
                'product_id' => $orderItem->getProductId(),
                'order_item_id' => $orderItem->getId()
            ];

            for($i=0;$i<$orderItem->getQtyOrdered();$i++) {
                $shipment->addItem($shipmentItem);
            }

        }

        $shipment->register();
        $shipment->getOrder()->setIsInProcess(true);

        $packages[0] =
            [
                'items' => $itemsArray,
                'params'=> [
                    'weight' => $pesoTotal,
                    'container'=> 1,
                    'customs_value'=> $valorTotal
                ]
            ];

        try {
            $serialJson  = serialize(json_encode($dataGuia));

            $order = $shipment->getOrder();
            $carrier = $this->_carrierFactory->create($order->getShippingMethod(true)->getCarrierCode());

            $shipment->setPackages($packages);

            $carrierCode = $carrier->getCarrierCode();
            $carrierTitle = $this->_scopeConfig->getValue(
                'carriers/' . $carrierCode . '/title',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $shipment->getStoreId()
            );

            foreach ($dataGuia as $guia){
                $trackingNumbers[] = $guia['datosguia']->GenerarEnviosDeEntregaYRetiroConDatosDeImpresionResult->NumeroAndreani;
            }

            if (!empty($trackingNumbers)) {
                $this->addTrackingNumbersToShipment($shipment, $trackingNumbers, $carrierCode, $carrierTitle);
            }

            $shipment->setData('andreani_datos_guia', $serialJson);
            $shipment->save();
            $shipment->getOrder()->save();

            //$this->_shipmentNotifier->notify($shipment);
            $newStatus = OrderWorkflow::STATUS_SHIPPING_DELIVERED;

            $order->setState($this->_orderWorkflowHelper->getStateByStatus($newStatus));
            $order->setStatus($newStatus);
            $order->save();
            if (!empty($trackingNumbers)) {
                foreach ($trackingNumbers as $trackingNumber) {
                    $mensajeEstado = "Seguimiento envío <a href='{$helper->getTrackingUrl($trackingNumber)}' target='_blank'>{$trackingNumber}</a>";
                    $history = $order->addStatusHistoryComment($mensajeEstado);
                    $history->setIsVisibleOnFront(true);
                    $history->setIsCustomerNotified(true);
                    $history->save();
                }
            }

            return $shipment->getEntityId();

        } catch (\Exception $e) {
            $helper->log("ERROR: ".$e->getMessage(),'generacion_guia_andreani_'.date('Y_m').'.log');
            $helper->log(print_r($dataGuia,true),'generacion_guia_andreani_'.date('Y_m').'.log');

            throw new \Magento\Framework\Exception\LocalizedException(
                __($e->getMessage())
            );
        }

    }

    /**
     * @description Efectiviza la generación del envío siempre y cuando dicha orden
     * esté habilitada para ser enviada.
     * @param $order
     * @param $dataGuia
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _doShipmentByOrder($order,$dataGuia)
    {
        $response = [];
        if (!$order->canShip())
        {
            return false;
        }

        $helper       = $this->_andreaniHelper;
        $convertOrder = $this->_convertOrder;
        $shipment     = $convertOrder->toShipment($order);

        $valorTotal = $pesoTotal = 0;
        $itemsArray = [];

        foreach ($order->getAllItems() AS $orderItem)
        {
            if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                continue;
            }

            $qtyShipped = $orderItem->getQtyToShip();
            $shipmentItem = $convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);

            $valorTotal += $qtyShipped * $orderItem->getPrice();
            $pesoTotal  += $qtyShipped * $orderItem->getWeight();

            $itemsArray[$orderItem->getId()] = [
                'qty' => $qtyShipped,
                'customs_value' => $orderItem->getPrice(),
                'price' => $orderItem->getPrice(),
                'name' => $orderItem->getName(),
                'weight'=> $orderItem->getWeight(),
                'product_id' => $orderItem->getProductId(),
                'order_item_id' => $orderItem->getId()
            ];

            $shipment->addItem($shipmentItem);
        }

        $shipment->register();
        $shipment->getOrder()->setIsInProcess(true);

        try {
            $shippingLabelContent               = $dataGuia[0]['lastrequest'];
            $trackingNumber                     = $dataGuia[0]['datosguia']->GenerarEnviosDeEntregaYRetiroConDatosDeImpresionResult->NumeroAndreani;
            $response['tracking_number']        = $trackingNumber;
            $response['shipping_label_content'] = $shippingLabelContent;
            $serialJson                         = serialize(json_encode($dataGuia));
            $trackingUrl                        = $helper->getTrackingUrl($trackingNumber);

            $order = $shipment->getOrder();
            $carrier = $this->_carrierFactory->create($order->getShippingMethod(true)->getCarrierCode());

            $shipment->setPackages(
                [
                    1=> [
                        'items' => $itemsArray,
                        'params'=> [
                            'weight' => $pesoTotal,
                            'container'=> 1,
                            'customs_value'=> $valorTotal
                        ]
                    ]]);

            $carrierCode = $carrier->getCarrierCode();
            $carrierTitle = $this->_scopeConfig->getValue(
                'carriers/' . $carrierCode . '/title',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $shipment->getStoreId()
            );

            foreach ($dataGuia as $guia){
                $trackingNumbers[] = $guia['datosguia']->GenerarEnviosDeEntregaYRetiroConDatosDeImpresionResult->NumeroAndreani;
            }

            if (!empty($trackingNumbers)) {
                $this->addTrackingNumbersToShipment($shipment, $trackingNumbers, $carrierCode, $carrierTitle);
            }

            $shipment->setData('andreani_datos_guia', $serialJson);
            $shipment->save();
            $shipment->getOrder()->save();



            //$this->_shipmentNotifier->notify($shipment);
            $newStatus = OrderWorkflow::STATUS_SHIPPING_DELIVERED;

            $order->setState($this->_orderWorkflowHelper->getStateByStatus($newStatus));
            $order->setStatus($newStatus);
            $order->save();

            $mensajeEstado = "Seguimiento envío <a href='{$trackingUrl}' target='_blank'>{$trackingNumber}</a>";
            $history = $order->addStatusHistoryComment($mensajeEstado);
            $history->setIsVisibleOnFront(true);
            $history->setIsCustomerNotified(true);
            $history->save();

            return $shipment->getEntityId();

        } catch (\Exception $e) {
            $helper->log("ERROR: ".$e->getMessage(),'generacion_guia_andreani_'.date('Y_m').'.log');
            $helper->log(print_r($dataGuia,true),'generacion_guia_andreani_'.date('Y_m').'.log');

            throw new \Magento\Framework\Exception\LocalizedException(
                __($e->getMessage())
            );
        }
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @param array $trackingNumbers
     * @param string $carrierCode
     * @param string $carrierTitle
     *
     * @return void
     */
    private function addTrackingNumbersToShipment(
        \Magento\Sales\Model\Order\Shipment $shipment,
        $trackingNumbers,
        $carrierCode,
        $carrierTitle
    ) {
        foreach ($trackingNumbers as $number)
        {
            if (is_array($number))
            {
                $this->addTrackingNumbersToShipment($shipment, $number, $carrierCode, $carrierTitle);
            }
            else
            {
                $shipment->addTrack(
                    $this->_trackFactory->create()
                        ->setNumber($number)
                        ->setCarrierCode($carrierCode)
                        ->setTitle($carrierTitle)
                );
            }
        }
    }

    public function generarEnvios($orders){
        $helper = $this->_andreaniHelper;
        if ($helper->getGeneracionGuiasConfig() == '1') {
            $data = $this->_prepareGuiaByOrder($orders);
        } else {
            $data = $this->_prepareGuiaByItem($orders);
        }

        return $data;
    }

    /**
     * @description Prepara la guía para ser generada por ítems contenidos
     * en la orden.
     * @param array $collection
     */
    protected function _prepareGuiaByItem($collection)
    {
        $helper = $this->_andreaniHelper;
        $guiasContent = [];
        if (count($collection))
        {
            /**
             * Carga cada orden y se cerciora que pertenezca a algún
             * carrier de Andreani.
             */
            foreach ($collection as $orderId)
            {
                $order = $this->_order->load($orderId);
                if(
                    $order->getShippingMethod()=='andreaniestandar_estandar' ||
                    $order->getShippingMethod()=='andreaniurgente_urgente' ||
                    $order->getShippingMethod()=='andreanisucursal_sucursal'
                )
                {
                    /**
                     * valida si tiene items por enviar.
                     *
                     */
                    $leftShipments = false;
                    foreach($order->getAllItems() AS $key => $orderItem)
                    {
                        if((int)$orderItem->getQtyOrdered() != (int)$orderItem->getQtyShipped())
                        {
                            $leftShipments = true;
                            break;
                        }
                    }
                }
                else
                {
                    continue;
                }

                if(!$order->hasShipments() || $leftShipments)
                {
                    try {
                        $this->doShipmentRequest($order->getAllItems());
                    }catch (\Exception $e){
                        $this->_messageManager->addNoticeMessage(__('Error al generar guía de la orden ' . $order->getIncrementId() . ': ' . $e->getMessage()));
                        $helper->log("ERROR: ".$e->getMessage(),'generacion_guia_andreani_'.date('Y_m').'.log');
                    }
                }
            }

            /**
             * Arma la colección de shipments para leer el json serializado
             * de la DB.
             */
            $ordersShipment = $this->shipmentCollectionFactory->create()
                ->setOrderFilter(['in' => $collection]);

            if ($ordersShipment->getSize()) {
                foreach ($ordersShipment as $order) {
                    $guiaContent = $order->getAndreaniDatosGuia();
                    if ($guiaContent) {
                        $guiasContent[$order->getIncrementId()] = json_decode(unserialize($guiaContent));
                    }
                }
            }
            try
            {
                /**
                 * si el contenido de las guías no está vacío
                 */
                if (!empty($guiasContent)) {
                    return $this->_generarGuiasMasivas($guiasContent,$collection);
                }
                //return true;
            }
            catch (\Exception $e)
            {
                $this->_messageManager->addErrorMessage(__('No hay guías creadas para las órdenes seleccionadas.'));
                $helper->log("ERROR: ".$e->getMessage(),'generacion_guia_andreani_'.date('Y_m').'.log');
            }
        }
    }

    /**
     * @description Prepara la guía para ser generada.
     * @param array $collection
     */
    protected function _prepareGuiaByOrder($collection)
    {
        $helper = $this->_andreaniHelper;
        $guiasContent = [];
        if (count($collection))
        {
            $orderIdsSinAndreani = [];

            $cantidadOrdenes = 0;
            $orderIdsExcluidos = [];

            /**
             * Carga cada orden y se cerciora que pertenezca a algún
             * carrier de Andreani.
             */
            foreach ($collection as $orderId)
            {
                $order = $this->_order->load($orderId);

                if($cantidadOrdenes >= 100)
                {
                    $orderIdsExcluidos[] = $order->getIncrementId();
                    continue;
                }
                if(
                    $order->getShippingMethod()=='andreaniestandar_estandar' ||
                    $order->getShippingMethod()=='andreaniurgente_urgente' ||
                    $order->getShippingMethod()=='andreanisucursal_sucursal'
                )
                {
                    /**
                     * valida si tiene items por enviar.
                     *
                     */
                    $leftShipments = false;
                    foreach($order->getAllItems() AS $key => $orderItem)
                    {
                        if((int)$orderItem->getQtyOrdered() != (int)$orderItem->getQtyShipped())
                        {
                            $leftShipments = true;
                            break;
                        }
                    }
                    /**
                     * Si la orden no tiene envíos, lo genera.
                     */
                    if(!$order->hasShipments() || $leftShipments)
                    {
                        try{
                            $this->doShipmentRequest($order);
                        }catch (\Exception $e){
                            $this->_messageManager->addNoticeMessage(__('Error al generar guía de la orden ' . $order->getIncrementId() . ': ' . $e->getMessage()));
                            $helper->log("ERROR: ".$e->getMessage(),'generacion_guia_andreani_'.date('Y_m').'.log');
                        }
                    }

                    $cantidadOrdenes++;
                }
            }

            if(count($orderIdsExcluidos))
            {
                $this->_messageManager->addNoticeMessage('Sólo se puede generar guías para un máximo de 100 pedidos simultáneamente. No se generaron guías para las órdenes: '.implode(',',$orderIdsExcluidos).'.');
            }

            /**
             * Arma la colección de shipments para leer el json serializado
             * de la DB.
             */
            $ordersShipment = $this->shipmentCollectionFactory->create()
                ->setOrderFilter(['in' => $collection]);

            if ($ordersShipment->getSize())
            {
                foreach ($ordersShipment as $order)
                {
                    $guiaContent = $order->getAndreaniDatosGuia();
                    if ($guiaContent) {
                        $guiasContent[$order->getIncrementId()] = json_decode(unserialize($guiaContent));
                    }
                }
            }
        }

        try
        {
            /**
             * si el contenido de las guías no está vacío
             */
            if (!empty($guiasContent))
            {
                return $this->_generarGuias($guiasContent,$collection);
            }
        }
        catch (\Exception $e)
        {
            $this->_messageManager->addErrorMessage(__('No hay guías creadas para las órdenes seleccionadas.'));
        }
    }

    /**
     * @description Genera masivamente las guías en un solo PDF
     * @param $guiasContent
     * @param $ordersIds
     */
    protected function _generarGuiasMasivas($guiasContent,$ordersIds)
    {
        try
        {
            $helper                 = $this->_andreaniHelper;
            $order_id               = '';

            if ($helper->getImpresionGuiasConfig() == '1') {

                if (file_exists(BP . "/var/export/Andreani.html")) {
                    unlink(BP . "/var/export/Andreani.html");
                }

                $file = fopen(BP . "/var/export/Andreani.html", "w") or die("Unable to open file!");

                // Impresion de la etiqueta que brinda la API de Andreani
                foreach ($guiasContent AS $key => $guiaData) {
                    foreach ($guiaData as $guia) {
                        $object = $guia->datosguia->GenerarEnviosDeEntregaYRetiroConDatosDeImpresionResult;
                        $guia_pdf = $this->_webservice->ImpresionDeConstancia($object->NumeroAndreani);
                        fwrite($file, "<a href='" . $guia_pdf . "' target='_blank'>" . $object->NumeroAndreani . "</a><br>");
                    }
                }

                $data = BP . "/var/export/Andreani.html";

                return $data;
            }

            /**
             * Concatena los ID para mandarlos por comas en la URL.
             */
            foreach($ordersIds AS $key => $orderId)
            {
                $order_id.=$orderId.',';
            }

            $order_id = rtrim($order_id, ',');

            /**
             * Accede al objeto para crear el código de barras.
             */
            foreach($guiasContent AS $key => $guiaData)
            {
                foreach ($guiaData as $guia){
                    $object = $guia->datosguia->GenerarEnviosDeEntregaYRetiroConDatosDeImpresionResult;
                    $helper->crearCodigoDeBarras($object->NumeroAndreani);
                }
            }

            /**
             * Instancia la url de la tienda que posteriormente recibirá los parámetros para armar la guía.
             */
            $pdfName        = 'guia_masiva_'.date_timestamp_get(date_create());

            /**
             * Crea el bloque dinámicamente y le pasa los parámetros por array para
             * que renderice la guía en html.
             */
            $block = $this->_view
                ->getLayout()
                ->createBlock('Ids\Andreani\Block\Generarhtmlmasivo',
                    "guiamasiva",
                    ['data' => [
                        'order_id' => $order_id
                    ]
                    ])
                ->setData('area', 'frontend')
                ->setTemplate('Ids_Andreani::guiamasiva.phtml');

            $html = $block->toHtml();

            /**
             * Espera recibir "true" después de mandarle al método del helper
             * que se encarga de generar la guía en HTML. El tercer parámetro
             * fuerza la descarga (D) o fuerza el almacenamiento en el filesystem (F)
             */
            if($helper->generateHtml2Pdf($pdfName,$html,'F'))
            {
                $filePath 		= $helper->getGuiaPdfPath($pdfName);

                $this->_messageManager->addSuccessMessage(__('La guía se generó correctamente.'));

                foreach($guiasContent AS $key => $guiaData)
                {
                    foreach ($guiaData as $guia) {
                        $object = $guia->datosguia->GenerarEnviosDeEntregaYRetiroConDatosDeImpresionResult;
                        unlink($helper->getDirectoryPath('media') . "/andreani/" . $object->NumeroAndreani . '.png');
                    }
                }

                return $filePath;
            }
        }
        catch (\Exception $e)
        {
            $this->_messageManager->addErrorMessage(__('Hubo un problema generando la guía. Inténtelo de nuevo.'));
            $this->_andreaniHelper->log("ERROR: ".$e->getMessage(),'generacion_guia_andreani_'.date('Y_m').'.log');
        }
    }

    /**
     * @description Genera la guía en PDF cuando recibe como parámetros,
     * el contenido de la misma y los ID de las órdenes.
     * @param $guiasContent
     * @param $ordersIds
     * @throws \Exception
     * @throws \Zend_Pdf_Exception
     */
    protected function _generarGuias($guiasContent,$ordersIds)
    {
        try
        {
            $helper             = $this->_andreaniHelper;
            $order_id = '';

            if ($helper->getImpresionGuiasConfig() == '1') {

                if (file_exists(BP . "/var/export/Andreani.html")) {
                    unlink(BP . "/var/export/Andreani.html");
                }

                $file = fopen(BP . "/var/export/Andreani.html", "w") or die("Unable to open file!");

                // Impresion de la etiqueta que brinda la API de Andreani
                foreach ($guiasContent AS $key => $guiaData) {
                    foreach ($guiaData as $guia) {
                        $object = $guia->datosguia->GenerarEnviosDeEntregaYRetiroConDatosDeImpresionResult;
                        $guia_pdf = $this->_webservice->ImpresionDeConstancia($object->NumeroAndreani);
                        fwrite($file, "<a href='" . $guia_pdf . "' target='_blank'>" . $object->NumeroAndreani . "</a><br>");
                    }
                }

                $data = BP . "/var/export/Andreani.html";

                return $data;
            }

            /**
             * Concatena los ID para mandarlos por comas en la URL.
             */
            foreach($ordersIds AS $key => $orderId)
            {
                $order_id.=$orderId.',';
            }

            $order_id = rtrim($order_id, ',');

            /**
             * Accede al objeto para crear el código de barras.
             */
            foreach($guiasContent AS $key => $guiaData)
            {
                foreach ($guiaData as $guia){
                    $object = $guia->datosguia->GenerarEnviosDeEntregaYRetiroConDatosDeImpresionResult;
                    $helper->crearCodigoDeBarras($object->NumeroAndreani);
                }
            }

            $pdfName        = 'guia_masiva_'.date_timestamp_get(date_create());

            /**
             * Crea el bloque dinámicamente y le pasa los parámetros por array para
             * que renderice la guía en html.
             */
            $block = $this->_view
                ->getLayout()
                ->createBlock('Ids\Andreani\Block\Generarhtmlmasivo',
                    "guiamasiva",
                    ['data' => [
                        'order_id' => $order_id
                    ]
                    ])
                ->setData('area', 'frontend')
                ->setTemplate('Ids_Andreani::guiamasiva.phtml');

            $html = $block->toHtml();

            /**
             * Espera recibir "true" después de mandarle al método del helper
             * que se encarga de generar la guía en HTML. El tercer parámetro
             * fuerza la descarga (D) o fuerza el almacenamiento en el filesystem (F)
             */
            if($helper->generateHtml2Pdf($pdfName,$html,'F'))
            {
                $filePath 		= $helper->getGuiaPdfPath($pdfName);

                $this->_messageManager->addSuccess( __('La guía se generó correctamente.') );

                foreach($guiasContent AS $key => $guiaData)
                {
                    foreach ($guiaData as $guia) {
                        $object = $guia->datosguia->GenerarEnviosDeEntregaYRetiroConDatosDeImpresionResult;
                        unlink($helper->getDirectoryPath('media') . "/andreani/" . $object->NumeroAndreani . '.png');
                    }
                }

                return $filePath;
            }

        }catch (\Exception $e)
        {
            $this->_messageManager->addError(__('Hubo un problema generando la guía. Inténtelo de nuevo.'));
            $this->_andreaniHelper->log("ERROR: ".$e->getMessage(),'generacion_guia_andreani_'.date('Y_m').'.log');
        }
    }
}