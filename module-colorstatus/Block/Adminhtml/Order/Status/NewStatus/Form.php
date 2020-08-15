<?php

/**
 * Color Status
 *
 * Set the each sales row with a background color on sales grid.
 *
 * @package Igorludgero\Colorstatus
 * @author Igor Ludgero Miura <igor@imaginemage.com>
 * @copyright Copyright (c) 2017 Igor Ludgero Miura (https://www.igorludgero.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

namespace Igorludgero\Colorstatus\Block\Adminhtml\Order\Status\NewStatus;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('new_order_status');
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_status');

        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Order Status Information')]);

        $fieldset->addType('color', \Igorludgero\Colorstatus\Block\Adminhtml\Field\Color::class);

        $fieldset->addField('is_new', 'hidden', ['name' => 'is_new', 'value' => 1]);

        $fieldset->addField(
            'status',
            'text',
            [
                'name' => 'status',
                'label' => __('Status Code'),
                'class' => 'required-entry validate-code',
                'required' => true
            ]
        );

        $fieldset->addField(
            'label',
            'text',
            ['name' => 'label', 'label' => __('Status Label'), 'class' => 'required-entry', 'required' => true]
        );

        $fieldset->addField(
            'color',
            'color',
            [
                'name' => 'color',
                'label' => __('Status Color'),
                'class' => 'color',
                'required' => false
            ]
        );

        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->_addStoresFieldset($model, $form);
        }

        if ($model) {
            $form->addValues($model->getData());
        }
        $form->setAction($this->getUrl('sales/order_status/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    protected function _addStoresFieldset($model, $form)
    {
        $labels = $model ? $model->getStoreLabels() : [];
        $fieldset = $form->addFieldset(
            'store_labels_fieldset',
            ['legend' => __('Store View Specific Labels'), 'class' => 'store-scope']
        );
        $renderer = $this->getLayout()->createBlock('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset');
        $fieldset->setRenderer($renderer);

        foreach ($this->_storeManager->getWebsites() as $website) {
            $fieldset->addField(
                "w_{$website->getId()}_label",
                'note',
                ['label' => $website->getName(), 'fieldset_html_class' => 'website']
            );
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                if (count($stores) == 0) {
                    continue;
                }
                $fieldset->addField(
                    "sg_{$group->getId()}_label",
                    'note',
                    ['label' => $group->getName(), 'fieldset_html_class' => 'store-group']
                );
                foreach ($stores as $store) {
                    $fieldset->addField(
                        "store_label_{$store->getId()}",
                        'text',
                        [
                            'name' => 'store_labels[' . $store->getId() . ']',
                            'required' => false,
                            'label' => $store->getName(),
                            'value' => isset($labels[$store->getId()]) ? $labels[$store->getId()] : '',
                            'fieldset_html_class' => 'store'
                        ]
                    );
                }
            }
        }
    }

}
