<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */
namespace Amasty\Label\Block\Adminhtml\Data\Form\Element;

use Magento\Framework\Escaper;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface;

class File extends \Magento\Framework\Data\Form\Element\File
{
    /**
     * @var \Amasty\Label\Helper\Shape
     */
    protected $shapehelper;
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        $data = [],
        \Amasty\Label\Helper\Shape $shapehelper,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->shapehelper = $shapehelper;
        $this->urlBuilder = $urlBuilder;
    }

    public function getElementHtml()
    {
        $id = $this->getHtmlId();
        list($textOnly, $shareChecked, $downloadChecked) = $this->_getChecked();
        $html = '<div class="amlabel-choose-container" id="amlabel-choose-' . $id . '">';
            $html .= $this->getTypeInput('text_only', $textOnly, __('Text Only'))
                . $this->getTypeInput('shape', $shareChecked, __('Select Shape'))
                . $this->getTypeInput('download', $downloadChecked, __('Upload Image'));
        $html .= '</div>';
        $html .= $this->_getDownloadHtml();
        $html .= $this->_getShapeHtml();
        $html .= $this->_getJsHtml($id);

        return $html;
    }

    protected function _getShapeHtml()
    {
        $value = $this->getValue();
        $shapes = $this->shapehelper->getShapes();

        $html = '<div id="amlabel-shape' . $this->getHtmlId() . '" class="additional">';
            $html .= '<div class="amlabel-shapes-container">';
                foreach ($shapes as $shape => $shapeName) {
                    $checked = ($value && strpos($value, $shape) !== FALSE)? 'checked' : '';
                    $html .=  $this->shapehelper->generateShape($shape, $this->getHtmlId(), $checked);
                }
            $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    protected function _getDownloadHtml()
    {
        $html = '<div id="amlabel-download' . $this->getHtmlId() . '" class="additional">';

        $img = $this->getValue();
        if ($img) {
            $html .= '<div class="amlabel-image-preview">';
                $html .= '<img id="image_preview' . $this->getHtmlId() .
                    '" src="' . $this->_getMediaPath() . $img . '" />';
                $html .= '</div><div class="amlabel-image-upload">';
                $html .= '<input
                            style="margin-bottom: 3px;"
                            id="' . $this->getHtmlId() . '"
                            name="' . $this->getName() . '"
                            value="' . $this->getEscapedValue() . '"
                            ' . $this->serialize($this->getHtmlAttributes())
                        . '/>';
                $html .= '<br/><input
                                type="checkbox"
                                value="1"
                                name="remove_' . $this->getHtmlId() .
                          '"/> ' . __('Remove');
                $html .= '<input type="hidden" value="' . $img . '" name="old_' . $this->getHtmlId() . '"/>';
            $html .= '</div>';
        }
        else {
            $html .=
                '<input style="margin-bottom: 3px;"
                    id="' . $this->getHtmlId() . '"
                    name="' . $this->getName() . '"
                    value="' . $this->getEscapedValue() . '"
                    ' . $this->serialize($this->getHtmlAttributes())
                . '/>';
        }

        $html .= '<p class="note" id="note_prod_img"><span>' .
            __('Click <a href="%1">here</a> to download the packs of label images.',
                'https://amasty.com/media/downloads/labels/labels-images.zip') .
            '</span></p>';
        $html .= '</div>';

        return $html;
    }

    /**
     * @return array
     */
    protected function _getChecked(){
        $valueTypes = ['', '', ''];
        if ($this->getValue()) {
            end($valueTypes);
        } else {
            reset($valueTypes);
        }
        $valueTypes[key($valueTypes)] = 'checked';

        return $valueTypes;
    }

    protected function _getJsHtml($field){
        $html = '<script>
            require([
              "jquery",
              "Amasty_Label/js/amlabel"
            ], function ($) {
               $("#amlabel-choose-' . $field . '").amLabelChoose();
            });
        </script>';

        return $html;
    }

    protected function _getMediaPath(){
        $path = $this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
        $path .= 'amasty/amlabel/';
        return $path;
    }

    /**
     * @param $code
     * @param $value
     * @param $label
     *
     * @return string
     */
    private function getTypeInput($code, $value, $label)
    {
        return sprintf(
            '<input %3$s
                      type="radio"
                      name="label_type%1$s"
                      id="%2$s_%1$s"
                      value="%2$s%1$s"
                ><label for="%2$s_%1$s">%4$s</label>',
            $this->getHtmlId(),
            $code,
            $value,
            $label
        );
    }
}
