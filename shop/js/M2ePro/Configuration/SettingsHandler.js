SettingsHandler = Class.create();
SettingsHandler.prototype = Object.extend(new CommonHandler(), {

    //----------------------------------

    initialize: function()
    {
        Validation.add('M2aPro-require-select-component', M2ePro.text.component_not_selected, function(value) {
            var isComponentSelected = false;

            $$('.M2ePro-component-mode').each(function(el) {
                if ($(el).value == 1) {
                    isComponentSelected = true;
                }
            });

            return isComponentSelected;
        });

        Validation.add('M2aPro-check-default-component', M2ePro.text.default_component_disabled, function(value) {
            var componentDefault = $('component_default').value.toLowerCase(),
                componentsEnabledNumber = 0;

            $$('.M2ePro-component-mode').each(function(el) {
                if ($(el).value == 1) {
                    componentsEnabledNumber++;
                }
            });

            if (componentsEnabledNumber <= 1) {
                // M2aPro-require-select-component should be invalid
                return true;
            }

            return $('component_' + componentDefault + '_mode') &&
                   $('component_' + componentDefault + '_mode').value == 1;
        });
    },

    //----------------------------------

    component_mode_change : function()
    {
        var self = SettingsHandlerObj,
            enabledComponents = 0;

        $$('.M2ePro-component-mode').each(function(el) {
            if ($(el).value == 1) {
                enabledComponents++;
            }
        });

        $('component_ebay_mode').value == 0
            ? $('negative_feedbacks_row').hide()
            : $('negative_feedbacks_row').show();

        if (enabledComponents >= 2) {
            $('component_default_tr').show();
        } else {
            $('component_default').value = $('component_amazon_mode').value == 1 ? self.AMAZON : self.EBAY;
            $('component_default_tr').hide();
        }
    },

    //----------------------------------

    completeStep : function()
    {
        var self = this;
        new Ajax.Request( M2ePro.url.formSubmit + '?' + $('edit_form').serialize() ,
        {
            method: 'get',
            asynchronous: true,
            onSuccess: function(transport)
            {
                window.opener.completeStep = 1;
                window.close();
            }
        });
    }

    //----------------------------------
});