<?php
namespace MercadoPago\Core\Block;

/**
 * Class Info
 *
 * @package MercadoPago\Core\Block
 */
class Info extends \Magento\Payment\Block\Info
{
    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Prepare information specific to current payment method
     *
     * @param null | array $transport
     * @return \Magento\Framework\DataObject
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        $transport = parent::_prepareSpecificInformation($transport);
        $data = [];
        $info = $this->getInfo();
        $fields = [
//            ["field" => "trunc_card", "title" => __("Card Number")],
            ["field" => "payment_method", "title" => __("Medio de pago")],
//            ["field" => "expiration_date", "title" => __("Expiration Date")],
            ["field" => "installments", "title" => __("Cuotas")],
            ["field" => "cardholderName", "title" => __("Titular de la Tarjeta")],
//            ["field" => "statement_descriptor", "title" => __("Statement Descriptor")],
//            ["field" => "payment_id", "title" => __("Payment id (MercadoPago)")],
//            ["field" => "status", "title" => __("Payment Status")],
//            ["field" => "status_detail", "title" => __("Payment Detail")],
            ["field" => "activation_uri", "title" => ""],
            ["field" => "payment_id_detail", "title" => __("Mercado Pago Payment Id")]
        ];

        foreach ($fields as $field) {

            if ($info->getAdditionalInformation($field['field']) != "") {
                $text = __($field['title'], $info->getAdditionalInformation($field['field']));

                if ($field['field'] == 'activation_uri'){
                    $data[$text->getText()] = '<a href="'. $this->escapeUrl(urldecode($info->getAdditionalInformation($field['field']))).'" target="_blank"  class="btn-boleto-mercadopago button-primary">'. __('Generar ticket de pago'). '</a>';
                } else {
                    $data[$text->getText()] = ucfirst($info->getAdditionalInformation($field['field']));
                }
            };
        };

        if ($info->getAdditionalInformation('payer_identification_type') != "") {
            $text = __($info->getAdditionalInformation('payer_identification_type'), $info->getAdditionalInformation('payer_identification_number'));
            $data[$text->getText()] = $info->getAdditionalInformation('payer_identification_number');
        }

        return $transport->setData(array_merge($data, $transport->getData()));
    }

}
