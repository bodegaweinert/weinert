<?php
namespace Aheadworks\OneStepCheckout\Ui\Component\Listing\Column\SalesOrder;

use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Ui\Component\Listing\Columns\Column;
use \Magento\Framework\Api\SearchCriteriaBuilder;

class RequireInvoice extends Column
{

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {

                $requireInvoice = $item['require_invoice'];


                switch ($requireInvoice) {
                    case "0":
                        $value = __("No");
                        break;
                    case "1";
                        $value = __("Yes");
                        break;
                    default:
                        $value = __("No");
                        break;

                }

                // $this->getData('name') returns the name of the column so in this case it would return export_status
                $item[$this->getData('name')] = $value;
            }
        }

        return $dataSource;
    }
}