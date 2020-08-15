<?php
namespace Aheadworks\OneStepCheckout\Model\ResourceModel\Order\Grid;

use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as OriginalCollection;

/**
 * Order grid extended collection
 */
class Collection extends OriginalCollection
{
    protected function _renderFiltersBefore()
    {
        $joinTable = $this->getTable('sales_order');
        $this->getSelect()->joinLeft($joinTable, 'main_table.entity_id = sales_order.entity_id', ['coupon_code']);

        $where = $this->getSelect()->getPart('where');
        $index = 0;

        foreach ($where as $filter){
            if (strpos($filter,'`main_table`.') === false ) {
                $filter = str_replace('(','(`main_table`.', $filter);
            }

            if (strpos($filter, '`coupon_code`') !== false) {
                $filter = str_replace('`main_table`', '`sales_order`', $filter);
            }


            $filter = str_replace("IN(`main_table`.","IN(",$filter);
            $where[$index] = $filter;
            $index++;
        }

        $this->getSelect()->setPart('where', $where);


        parent::_renderFiltersBefore();
    }
}