<?php
namespace Aheadworks\OneStepCheckout\Block\Customer\Address;

use Magento\Customer\Model\AttributeChecker;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\ObjectManager;
use Aheadworks\OneStepCheckout\Model\Source\Convenio;

class Edit extends \Magento\Customer\Block\Address\Edit
{
    /** @var Convenio $convenio*/
    protected $convenio;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Magento\Customer\Api\Data\AddressInterfaceFactory $addressDataFactory
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param Convenio $convenio
     * @param array $data
     * @param AttributeChecker $attributeChecker
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $addressDataFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        Convenio $convenio,
        array $data = [],
        AttributeChecker $attributeChecker = null
    ) {

        $this->convenio = $convenio;

        parent::__construct(
            $context,
            $directoryHelper,
            $jsonEncoder,
            $configCacheType,
            $regionCollectionFactory,
            $countryCollectionFactory,
            $customerSession,
            $addressRepository,
            $addressDataFactory,
            $currentCustomer,
            $dataObjectHelper,
            $data,
            $attributeChecker
        );

    }

    public function getConvenioSelectHtml(){

        $options =  $this->convenio->getAllOptions();
        $defaultValue = ($this->getAddress()->getCustomAttribute('convenio'))?$this->getAddress()->getCustomAttribute('convenio')->getValue():'';

        $html = $this->getLayout()->createBlock(
            \Magento\Framework\View\Element\Html\Select::class
        )->setName(
            'convenio'
        )->setId(
            'convenio'
        )->setTitle(
            __('Convenio')
        )->setValue(
            $defaultValue
        )->setOptions(
            $options
        )->setExtraParams(
            'data-validate="{\'validate-select\':false}"'
        )->getHtml();

        return $html;

    }

    public function getRequireInvoiceHtml(){

        $defaultValue = ($this->getAddress()->getCustomAttribute('require_invoice'))?$this->getAddress()->getCustomAttribute('require_invoice')->getValue():'';
        $options = [['label'=>__('Yes'), 'value'=>1],['label'=>__('No'), 'value'=>0]];

        $html = $this->getLayout()->createBlock(
            \Magento\Framework\View\Element\Html\Select::class
        )->setName(
            'require_invoice'
        )->setId(
            'require_invoice'
        )->setTitle(
            __('Require Invoice')
        )->setValue(
            $defaultValue
        )->setOptions(
            $options
        )->setExtraParams(
            'data-validate="{\'validate-select\':false}"'
        )->getHtml();

        return $html;
    }


}
