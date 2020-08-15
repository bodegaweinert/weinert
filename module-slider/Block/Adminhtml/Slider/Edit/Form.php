<?php
namespace Combinatoria\Slider\Block\Adminhtml\Slider\Edit;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;

/**
 * Adminhtml slider edit form
 */
class Form extends Generic
{
    /**
     * @var Store
     */
    protected $_systemWebsite;

    /** @var \Magento\Customer\Model\Config\Source\Group */
    protected $_customerGroupSource;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Store $systemWebsite
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemWebsite,
        array $data = []
    ) {
        $this->_systemWebsite = $systemWebsite;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('slider');
        $this->setTitle(__('Slider Information'));
    }
    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Combinatoria\Slider\Model\Slider $model */
        $model = $this->_coreRegistry->registry('combinatoria_slider_slider');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                ]
            ]
        );

        $form->setHtmlIdPrefix('slider_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
        );

        if ( $model->getId() ) {
            $fieldset->addField('id','hidden',['name' => 'id']);
        }


        $fieldset->addField(
            'alt',
            'text',
            ['name' => 'alt', 'label' => __('Alt'), 'title' => __('Alt'), 'required' => true]
        );

        $fieldset->addField(
            'link',
            'text',
            ['name' => 'link', 'label' => __('Link'), 'title' => __('Link'), 'required' => true]
        );

        $fieldset->addField(
            'from',
            'date',
            ['name' => 'from', 'label' => __('From'), 'title' => __('From'),'date_format' => 'yyyy-MM-dd', 'required' => true]
        );

        $fieldset->addField(
            'to',
            'date',
            ['name' => 'to', 'label' => __('To'), 'title' => __('To'),'date_format' => 'yyyy-MM-dd', 'required' => true]
        );

        $fieldset->addField(
            'photo_uploader',
            'file',
            ['name' => 'photo_uploader', 'label' => __('Photo Uploader'), 'title' => __('Photo Uploader'), 'required' => false]
        );
        $fieldset->addField(
            'image',
            'hidden',
            ['name' => 'image', 'label' => __('Image'), 'title' => __('Image'), 'required' => false]
        );


        $fieldset->addField(
            'sort_order',
            'text',
            ['name' => 'sort_order', 'label' => __('Sort Order'), 'title' => __('Sort Order'), 'required' => true]
        );

        $fieldset->addField(
            'is_mobile',
            'select',
            [
                'name' => 'is_mobile',
                'label' => __('Desktop/Mobile'),
                'title' => __('Desktop/Mobile'),
                'required' => true,
                'values' => ['0' => 'Desktop' , '1' => 'Mobile']
            ]
        );

        $fieldset->addField(
            'is_active',
            'select',
            [
                'name' => 'is_active',
                'label' => __('Status'),
                'title' => __('Status'),
                'required' => true,
                'values' => ['1' => 'Enabled' , '0' => 'Disabled']
            ]
        );

        $form->setValues( $model->getData() );
        $form->setUseContainer( true );
        $this->setForm( $form );

        return parent::_prepareForm();
    }
}