<?php

/**
 * ExtensionsMall
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ExtensionsMall EULA that is available through 
 * the world-wide-web at this URL: http://www.extensionsmall.com/license.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to support@extensionsmall.com for more information.
 *
 * @category   ExtensionsMall
 * @package    ExtensionsMall_CustomOptionSwatch
 * @author     ExtensionsMall Dev Team
 * @copyright  Copyright (c) 2015 ExtensionsMall (http://www.extensionsmall.com/)
 * @license    http://www.extensionsmall.com/license.html
 */
class ExtensionsMall_CustomOptionSwatch_Block_Product_View_Options_Type_Swatch extends Mage_Catalog_Block_Product_View_Options_Abstract {

    private $customOptionSwatchJs;

    private function getCustomOptionSwatchJs() {
        if (empty($this->customOptionSwatchJs)) {
            $this->customOptionSwatchJs = $this->getLayout()->getBlock('custom.option.swatch.js');
        }
        return $this->customOptionSwatchJs;
    }

    public function getValuesHtml() {

        $_option = $this->getOption();

        $configValue = $this->getProduct()->getPreconfiguredValues()->getData('options/' . $_option->getId());

        $swatchHtml = "<div class=\"custom-option-swatch-wrapper custom-option-swatch-{$_option->getid()} options-{$_option->getid()}-container\" >";
        $require = ($_option->getIsRequire()) ? ' required-entry' : '';

        if (!$this->getSkipJsReloadPrice()) {
            $updateOpConfig = $this->getCustomOptionSwatchJs()->getUpdateOpConfig();
            $updateOpConfig[$_option->getId()]['getSkipJsReloadPrice'] = $this->getSkipJsReloadPrice() ? true : false;
        }

        $swatchHtml .= '<ul id="options-' . $_option->getId() . '-list" class="custom-option-swatch ">';
        $count = 0;
        foreach ($_option->getValues() as $_value) {

            if ($configValue == $_value->getId()) {
                $this->getCustomOptionSwatchJs()->setReloadPrice(true);
            }
            if (!$this->getSkipJsReloadPrice()) {
                $updateOpConfig[$_option->getId()][$_value->getId()]['price'] = Mage::helper('core')->currency($_value->getPrice(true), false, false);
                $updateOpConfig[$_option->getId()][$_value->getId()]['oldPrice'] = Mage::helper('core')->currency($_value->getPrice(false), false, false);
                $updateOpConfig[$_option->getId()][$_value->getId()]['priceValue'] = $_value->getPrice(false);
                $updateOpConfig[$_option->getId()][$_value->getId()]['type'] = $_value->getPriceType();
                $updateOpConfig[$_option->getId()][$_value->getId()]['excludeTax'] = Mage::helper('tax')->getPrice($_value->getProduct(), $_value->getPrice(true), false);
                $updateOpConfig[$_option->getId()][$_value->getId()]['includeTax'] = Mage::helper('tax')->getPrice($_value->getProduct(), $_value->getPrice(true), true);
            }
            $count++;
            $swatchHtml .= '<li id="options_' . $_option->getId() . '_' . $count . '"   class="custom-option-swatch-' . $_option->getid() . '-value-' . $_value->getOptionTypeId() . ' custom-option-swatch-option ' . ($configValue == $_value->getId() ? ' active ' : '') . ' " >';
            $swatchHtml .= $this->getImgTag($_value);
        }

        $swatchHtml .= "</ul><br class=\"clearfloat\" />";
        $swatchHtml .= "<input id=\"custom-option-swatch-{$_option->getid()}-hidden-input\" type=\"hidden\" name=\"options[{$_option->getid()}]\" class=\"product-custom-option custom-option-swatch-{$_option->getid()}  {$require} \" value=\"{$configValue}\" />";
        $swatchHtml .= "</div>";

        if (!$this->getSkipJsReloadPrice()) {
            $this->getCustomOptionSwatchJs()->setUpdateOpConfig($updateOpConfig);
        }
        $swatchHtml .= '<script type="text/javascript">var customOptionSwatchPrice = ' . Mage::helper('core')->jsonEncode($updateOpConfig) . ';</script>';
        return $swatchHtml;
    }

    private function getImgTag($_value) {
        $src = Mage::helper('custom_option_swatch/image')->init($_value)->resize();
        $priceStr = $this->getPriceString($_value);
        $title = $_value->getTitle().$_value->getDescription();
        if (isset($priceStr) && !empty($priceStr)) {
            $title .= ' ' . $priceStr;
        }
        $html = '<img id="tooltipSwatch' . $_value->getId()  . '" src="' . $src . '" title="' . $title . '"  alt="' . $title . '" data-swatch-label="'.$_value->getTitle().'"  />';
        $html .= $this->getTooltip($_value->getId(), $src, $title.$_value->getData('description'));
	return $html;
    }

private function getTooltip($id, $img, $title) {
	$html = "<script>jQuery().ready(function($) { $('#tooltipSwatch" . $id  . "').tooltipster({
		content: $(
			'<div>' +
				'<img src=\"" . $img . "\" style=\"width:100%;height:auto;\" />' +
				'<p style=\"margin-top:10px;text-align:left;\">' +
					'<strong>" . $title . "</strong>' +
				'</p>' +
			'</div>'
		),
		// setting a same value to minWidth and maxWidth will result in a fixed width
		maxWidth: 400,
		side: 'left',
		triggerClose: {
			click: true
		},
		theme: 'tooltipster-light'
	});});</script>";
	return $html;
}

    private function getPriceString($_value) {
        if ($_value->getPriceType() == 'percent') {
            $percent = true;
        } else {
            $percent = false;
        }
        $priceStr = $this->_formatPrice(array(
            'is_percent' => $percent,
            'pricing_value' => $_value->getPrice(true)
        ));
        return strip_tags($priceStr);
    }

    /* public function getMultiselect() {
      $_option = $this->getOption();
      $swatchHtml = '<select multiple="multiple" name="options[' . $_option->getid() . ']">';
      foreach ($_option->getValues() as $_value) {

      $swatchHtml .= "<option>" . $_value->getOptionTypeId() . "</option>";
      }
      $swatchHtml .='</select>';
      return $swatchHtml;
      }
     */
}
