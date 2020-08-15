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

namespace Igorludgero\Colorstatus\Block\Adminhtml\Order\Status\Edit;

class Form extends \Igorludgero\Colorstatus\Block\Adminhtml\Order\Status\NewStatus\Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('new_order_status');
    }

    protected function _prepareForm()
    {
        parent::_prepareForm();
        $form = $this->getForm();
        $form->getElement('base_fieldset')->removeField('is_new');
        $form->getElement('base_fieldset')->removeField('status');
        $form->setAction(
            $this->getUrl('sales/order_status/save', ['status' => $this->getRequest()->getParam('status')])
        );
        return $this;
    }
}
