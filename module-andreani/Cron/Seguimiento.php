<?php
namespace Ids\Andreani\Cron;

use Ids\Andreani\Helper\Data as AndreaniHelper;

class Seguimiento
{
    protected $_andreaniHelper;
    protected $_transportBuilder;

    /**
     * BorrarPdf constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param AndreaniHelper $andreaniHelper
     */
    public function __construct(
        AndreaniHelper $andreaniHelper,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_andreaniHelper  = $andreaniHelper;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_transportBuilder = $transportBuilder;
        $this->_storeManager = $storeManager;
    }


    /**
     * Método que se ejecuta cuando corre el cron.
     */
    public function execute() {
        $helper = $this->_andreaniHelper;
        $helper->log("Se ejecuto cron segumiento",'seguimiento_andreani.log');

        if(!$helper->getSeguimientoHabilitado()){
            return false;
        }

        $date = (new \DateTime())->modify('-1 month');

        $collection = $this->_orderCollectionFactory->create()->addAttributeToSelect('*');

        $collection->addFieldToFilter('created_at', ['gt'=>$date->format('Y-m-d'),]);
        $collection->addFieldToFilter('shipping_method', ['like'=>'%andreani%']);
        $collection->addFieldToFilter('status', 'delivered');

        $reporte = array();

        foreach ($collection as $order){
            $tracking = $order->getTracksCollection()->getFirstItem()->getTrackNumber();

            $estado = $this->consultarEstado($tracking);

            if($estado){
                $orderEntity = $order;
                $row = array();
                if($order->getCustomerFirstname() != null) {
                    $row['nombre'] = $order->getCustomerFirstname() . " " . $order->getCustomerLastname();
                }else{
                    $row['nombre'] = $orderEntity->getShippingAddress()->getFirstname() . " " . $orderEntity->getShippingAddress()->getLastname();
                }
                $row['email'] = $order->getCustomerEmail();
                $row['pedido'] = $order->getIncrementId();
                $row['pago'] = $orderEntity->getPayment()->getMethod();
                $row['fecha_envio'] = substr($order->getUpdatedAt(),0,10);
                $row['guia'] = $tracking;

                $to = $helper->getEmailsReporte();

                $row['dias'] = 0;
                $row['envio_mail'] = false;

                switch ($estado){
                    case 1:
                        $orderDate = substr($order->getUpdatedAt(),0,10);
                        $currentDate = date("Y-m-d");

                        //para reporte
                        $date1 = new \DateTime($orderDate);
                        $date2 = new \DateTime($currentDate);
                        $diff = $date1->diff($date2);
                        $row['estado'] = 'Pendiente hace ' . $diff->days . ' días';
                        $row['dias'] = $diff->days;
                        $row['envio_mail'] = true;

                        break;

                    case 2:
                        $row['estado'] = 'Entregado';
                        $order->setStatus('received');
                        $order->save();
                        break;

                    case 3:
                        $row['estado'] = 'Primera visita';
                        $row['envio_mail'] = true;
                        break;

                    case 4:
                        $row['estado'] = 'Siniestro';
                        $emailName = null;
                        $row['envio_mail'] = true;
                        $order->setStatus('siniestro_andreani');
                        $order->save();

                        break;

                    case 5:
                        $row['estado'] = 'Rechazado';
                        $row['envio_mail'] = true;
                        $order->setStatus('rechazado_andreani');
                        $order->save();
                }

                $reporte[] = $row;
            }
        }

        if(count($reporte)>0){
            foreach ($reporte as $key => $row){
                $aux[$key] = $row['dias'];
            }

            array_multisort($aux, SORT_DESC, $reporte);

            $body2 = "<table><tr><th>Nombre</th><th>Email</th><th>Pedido</th><th>Pago</th><th>Fecha envío</th><th>Estado</th><th>Guía</th></tr>";
            foreach ($reporte as $row){
                if($row['envio_mail']){
                    $body2 .= "<tr style='font-weight: bold'><td>".$row['nombre']."</td><td>".$row['email']."</td><td>".$row['pedido']."</td><td>".$row['pago']."</td><td>".$row['fecha_envio']."</td><td>".$row['estado']."</td><td>".$row['guia']."</td></tr>";
                }else{
                    $body2 .= "<tr><td>".$row['nombre']."</td><td>".$row['email']."</td><td>".$row['pedido']."</td><td>".$row['pago']."</td><td>".$row['fecha_envio']."</td><td>".$row['estado']."</td><td>".$row['guia']."</td></tr>";
                }
            }
            $body2 .= "</table>";

            $to = explode(",",$to);
            $store = $this->_storeManager->getStore()->getId();
            $transport = $this->_transportBuilder->setTemplateIdentifier('andreani_seguimiento_template')
                ->setTemplateOptions(['area' => 'frontend', 'store' => $store])
                ->setTemplateVars(
                    [
                        'store' => $this->_storeManager->getStore(),
                        'html' => $body2
                    ]
                )
                ->setFrom('general')
                // you can config general email address in Store -> Configuration -> General -> Store Email Addresses
                ->addTo($to)
                ->getTransport();
            $transport->sendMessage();
        }

        return $this;
    }


    public function consultarEstado($tracking){
        $uno = array(1,2,3,4,8,15,17,18,19,20,21,22,25,27,28,30,31,32,33,34);
        $dos = array(23,24,5,6,7);
        $tres = array(35,36);
        $cuatro = array(9,10,11,12,13,14);
        $cinco = array(16,26,29);

        $datos['urlConfirmar'] = 'https://www.e-andreani.com/eAndreaniWS/Service.svc?wsdl';
        $datos["cliente"]       = $this->_andreaniHelper->getNroCliente();
        $datos['nro_envio'] = $tracking;

        $options = array(
            'soap_version' => SOAP_1_2,
            'exceptions' => true,
            'trace' => 1,
            'wdsl_local_copy' => true
        );

        $client = new \SoapClient($datos["urlConfirmar"], $options);

        try{
            $phpresponse = $client->ObtenerTrazabilidadCodificado(array(
                'NroPieza' => array(
                    'NroPieza' => '',
                    'NroAndreani' => $datos['nro_envio'],
                    'CodigoCliente' => $datos["cliente"]
                )));
        }catch (\Exception $e){
            return false;
        }

        if(isset($phpresponse->Respuesta->Envios->Envio_->Eventos->Evento_)){
            $ultimo = end($phpresponse->Respuesta->Envios->Envio_->Eventos->Evento_);

            $estado = $ultimo->IdEstado;

            if(in_array($estado,$uno)){
                return 1;
            }

            if(in_array($estado,$dos)){
                return 2;
            }

            if(in_array($estado,$tres)){
                return 3;
            }

            if(in_array($estado,$cuatro)){
                return 4;
            }

            if(in_array($estado,$cinco)){
                return 5;
            }
        }else{
            return false;
        }
    }
}