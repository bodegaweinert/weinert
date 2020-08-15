<?php
namespace Combinatoria\Slider\Ui\Component\Listing\Slider\Grid\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class PageActions extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource["data"]["items"])) {
            foreach ($dataSource["data"]["items"] as & $item) {
                $name = $this->getData("name");
                $id = "X";
                if(isset($item["id"]))
                {
                    $id = $item["id"];
                }
                $item[$name]["view"] = [
                    "href"=>$this->getContext()->getUrl(
                        "combinatoria_slider/slider/edit",["id"=>$id]),
                    "label"=>__("Edit")
                ];
            }
        }

        return $dataSource;
    }    
}