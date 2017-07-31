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
CustomOptionSwatch = Class.create();
CustomOptionSwatch.prototype = {
    initialize: function () {
    },
    swatchClick: function (event) {
        var element = event.element();
        element.up('li').siblings().each(function (item) {
            item.removeClassName('active');
        });
        element.up('li').addClassName('active');
        this.setSwatchLabel(element);

        this.updatePrice(element);
        this.updateImage(element);
    },
    updatePrice: function (element) {
        optionTypeId = element.up('li').className.split(' ').first().split('-')[5];
        optionId = element.up('.custom-option-swatch-wrapper').className.split(' ')[1].split('-')[3];
        $('custom-option-swatch-' + optionId + '-hidden-input').value = optionTypeId;
        this.opConfig.config[optionId] = this.customOptionSwatchPrice[optionId][optionTypeId];
        if (!this.customOptionSwatchPrice[optionId].getSkipJsReloadPrice) {
            this.opConfig.reloadPrice();
        }
    },
    setSwatchLabel: function (element) {
        var alt = element.alt;
        swatchLabel = element.up('dd').previous().select('.info');
        swatchLabel.first().update(alt);
    },
    updateImage: function (element) {
        var swatchLabel = element.readAttribute('data-swatch-label');
		if ($$('.product-image-thumbs').length > 0){
			var thumb = $$('.product-image-thumbs').first().select('[title="' + swatchLabel + '"]');
			if (thumb.length > 0) {
				var thumbIndex = Element.readAttribute(thumb[0], 'data-image-index');
				var image = $('image-' + thumbIndex).addClassName('visible');
				image.siblings().each(function (item) {
					item.removeClassName('visible');
				});
			}
		}	
    }
};

document.observe("dom:loaded", function () {
    var CustomOptionSwatchObject = new CustomOptionSwatch();
    CustomOptionSwatchObject.customOptionSwatchPrice = customOptionSwatchPrice;
    CustomOptionSwatchObject.opConfig = opConfig;
    $$('.custom-option-swatch-wrapper img').each(function (element) {
        element.observe('click', function (event) {
            CustomOptionSwatchObject.swatchClick(event);
        });
    });
    $$('.custom-option-swatch-wrapper  input.required-entry ').each(function (element, index) {
        element.callbackFunction = 'validateCustomOptionSwatch';
    });
});

function validateCustomOptionSwatch(elmId, result) {
    var container = $(elmId).up('div.custom-option-swatch-wrapper').select("ul").first();
    if (result === 'failed') {
        container.removeClassName('validation-passed');
        container.addClassName('validation-failed');
    } else {
        container.removeClassName('validation-failed');
        container.addClassName('validation-passed');
    }
}