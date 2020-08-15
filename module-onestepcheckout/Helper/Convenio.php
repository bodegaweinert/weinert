<?php
namespace Aheadworks\OneStepCheckout\Helper;

class Convenio extends \Magento\Framework\App\Helper\AbstractHelper{

    const LOCAL_INSCRIPTO_VAL = 0.0075;
    const LOCAL_INSCRIPTO_LABEL = '0,75';
    const LOCAL_NO_INSCRIPTO_VAL = 0.025;
    const LOCAL_NO_INSCRIPTO_LABEL = '2,5%';
    const MULTILATERAL_INSCRIPTO_VAL = 0.01;
    const MULTILATERAL_INSCRIPTO_LABEL = '1%';
    const MULTILATERAL_NO_INSCRIPTO_VAL = 0.02;
    const MULTILATERAL_NO_INSCRIPTO_LABEL = '2';

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
    }

    public function getTaxPercentage($convenio){
        $taxPercentage = 0;

        switch($convenio){
            case "local_inscripto"          : { $taxPercentage = self::LOCAL_INSCRIPTO_VAL; }; break;
            case "local_no_inscripto"       : { $taxPercentage = self::LOCAL_NO_INSCRIPTO_VAL; }; break;
            case "multilateral_inscripto"   : { $taxPercentage = self::MULTILATERAL_INSCRIPTO_VAL; }; break;
            case "multilateral_no_inscripto": { $taxPercentage = self::MULTILATERAL_NO_INSCRIPTO_VAL; }; break;
        }

        return $taxPercentage;
    }

    public function getTaxLabel($convenio){
        $taxLabel = '';

        switch($convenio){
            case "local_inscripto"          : { $taxLabel = self::LOCAL_INSCRIPTO_LABEL; }; break;
            case "local_no_inscripto"       : { $taxLabel = self::LOCAL_NO_INSCRIPTO_LABEL; }; break;
            case "multilateral_inscripto"   : { $taxLabel = self::MULTILATERAL_INSCRIPTO_LABEL; }; break;
            case "multilateral_no_inscripto": { $taxLabel = self::MULTILATERAL_NO_INSCRIPTO_LABEL; }; break;
        }

        return $taxLabel;
    }
}