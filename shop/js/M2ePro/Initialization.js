// Create main objects
// ----------------------------------
CommonHandlerObj = new CommonHandler();

MagentoMessageObj = new MagentoMessage();
MagentoBlockObj = new MagentoBlock();

ModuleNoticeObj = new BlockNotice('Module');
ServerNoticeObj = new BlockNotice('Server');

MagentoFieldTipObj = new MagentoFieldTip();
// ----------------------------------

// Set main observers
// ----------------------------------
Event.observe(window, 'load', function() {

    CommonHandlerObj.initCommonValidators();

    $$('.block_notices_module').each(function(blockObj) {
        ModuleNoticeObj.observeModulePrepareStart(blockObj);
    });
    $$('.block_notices_server').each(function(blockObj) {
        ServerNoticeObj.observeServerPrepareStart(blockObj);
    });

    $$('div.entry-edit').each(function(blockObj) {

        if (blockObj.select('div.entry-edit-head').length == 0) {
            return;
        }

        blockObj.select('div.entry-edit-head')[0].innerHTML = '<div class="entry-edit-head-left" style="float: left; width: 86%;">' + blockObj.select('div.entry-edit-head')[0].innerHTML + '</div>' +
                                                              '<div class="entry-edit-head-right" style="float: right; width: 12%;"></div>';
        MagentoBlockObj.observePrepareStart(blockObj);

        if (blockObj.select('div.fieldset div.hor-scroll table.form-list tr td.value p.note').length > 0) {
            MagentoFieldTipObj.observePrepareStart(blockObj);
        }
    });
});
// ----------------------------------