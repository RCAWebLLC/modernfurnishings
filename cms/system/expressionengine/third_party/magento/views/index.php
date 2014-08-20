<h1>Purge Magento Products Cache</h1>
<br />
<?=form_open('C=addons_modules&M=show_module_cp&module=magento&method=do_purge', array(), array('redirect_to' => $this->cp->get_safe_refresh()))?>
<?=form_submit(array( 'name' => 'purge', 'value' => 'Purge!', 'class' => 'submit'))?>
<?=form_close()?>