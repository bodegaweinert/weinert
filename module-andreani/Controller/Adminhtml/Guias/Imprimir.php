<?php

namespace Ids\Andreani\Controller\Adminhtml\Guias;

use Ids\Andreani\Helper\Data as AndreaniHelper;
use Magento\Framework\App\Response\Http\FileFactory;
use Ids\Andreani\Model\Webservice;

class Imprimir extends  \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var AndreaniHelper
     */
    protected $_andreaniHelper;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    private $_webservice;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry    $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        FileFactory $fileFactory,
        AndreaniHelper $andreaniHelper,
        Webservice $_webservice
    )
    {
        parent::__construct($context);
        $this->_coreRegistry        = $coreRegistry;
        $this->resultPageFactory    = $resultPageFactory;
        $this->fileFactory          = $fileFactory;
        $this->_andreaniHelper      = $andreaniHelper;
        $this->_webservice                  = $_webservice;
    }
    /**
     * Add New Row Form page.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $params             = $this->getRequest()->getParams();
        $incrementId        = $params['increment_id'];
        $order_id           = $params['order_id'];

        if($incrementId && $order_id)
        {
            try
            {
                $helper             = $this->_andreaniHelper;

                $shipmentOrder      = $helper->loadByIncrementId($incrementId);
                $andreaniDatosGuia  = $shipmentOrder->getAndreaniDatosGuia();
                $guiaContent        = json_decode(unserialize($andreaniDatosGuia));

                if ($helper->getImpresionGuiasConfig() == '1') {
                    if (file_exists(BP."/var/export/Andreani.html")) {
                        unlink(BP."/var/export/Andreani.html");
                    }

                    $file = fopen(BP."/var/export/Andreani.html", "w") or die("Unable to open file!");

                    // Impresion de la etiqueta que brinda la API de Andreani
                    foreach ($guiaContent as $guia){
                        $object = $guia->datosguia->GenerarEnviosDeEntregaYRetiroConDatosDeImpresionResult;
                        $guia_pdf = $this->_webservice->ImpresionDeConstancia($object->NumeroAndreani);
                        fwrite($file, "<a href='" . $guia_pdf . "' target='_blank'>" . $object->NumeroAndreani . "</a><br>");
                    }

                    $data = BP."/var/export/Andreani.html";

                    return $this->fileFactory->create(
                        "guias.html",
                        @file_get_contents($data)
                    );
                }

                foreach ($guiaContent as $guia){
                    $object             = $guia->datosguia->GenerarEnviosDeEntregaYRetiroConDatosDeImpresionResult;
                    $helper->crearCodigoDeBarras($object->NumeroAndreani);
                }


                $objectManager  = \Magento\Framework\App\ObjectManager::getInstance();
                $storeManager   = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
//                $urlHtml        = $storeManager->getStore()->getBaseUrl().'andreani/generarguia/shipmentguia/increment_id/'.$incrementId;
                $pdfName        = $incrementId.'_'.date_timestamp_get(date_create());

                /**
                 * Crea el bloque dinámicamente y le pasa los parámetros por array para
                 * que renderice la guía en html.
                 */
                $block = $this->_view
                    ->getLayout()
                    ->createBlock('Ids\Andreani\Block\Shipmentguia',
                        "shipmentguia",
                        ['data' => [
                            'increment_id' => $incrementId
                        ]
                        ])
                    ->setData('area', 'frontend')
                    ->setTemplate('Ids_Andreani::shipmentguia.phtml');

                $html = $block->toHtml();

                /**
                 * Espera recibir "true" después de mandarle al método del helper
                 * que se encarga de generar la guía en HTML. El tercer parámetro
                 * fuerza la descarga (D) o fuerza el almacenamiento en el filesystem (F)
                 */
                if($helper->generateHtml2Pdf($pdfName,$html,'D'))
                {
                    $this->messageManager->addSuccess( __('La guía se generó correctamente.') );
                    unlink($helper->getDirectoryPath('media')."/andreani/".$object->NumeroAndreani.'.png');
                }

            }catch (Exception $e)
            {
                $this->messageManager->addError(__('Hubo un problema generando la guía. Inténtelo de nuevo.'));
            }
        }

        $resultRedirect = $this->resultRedirectFactory->create()->setPath('sales/shipment/index');
        return $resultRedirect;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ids_Andreani::guias_edit');
    }

}