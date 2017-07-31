<?php
class Magehit_CallForPrice_Model_Loadtemplate
{
	public function loademailtemplate(){			
		define('TEMPLATE_PREFIX', 'template="email:');
		try
		{
		    $filename = Mage::getModel('core/config')->getOptions()->getCodeDir() . DS . 'local' . DS . 'Magehit' . DS . 'CallForPrice' . DS . 'sql' . DS . 'magehit_callforprice_setup' . DS . 'emailtemplates' .DS. 'emailtemplates.xml';			
            mage::log("filename");
            mage::log($filename);
			$model = Mage::getModel('adminhtml/email_template');
			$templates = array();
		    $existingTemplates = array();
			
		    $templatesXml = simplexml_load_file($filename);

		    if (!$templatesXml) {		        
		        return;
		    }		    		    		   
		    
		    foreach ($templatesXml as $template) {
		        $dataTemp = array();
		        foreach ($template as $fieldName => $value) {
		            $dataTemp[$fieldName] = (string) $template->$fieldName;
		        }

		        if (!isset($dataTemp['template_code'])) continue;
		        if ($model->loadByCode($dataTemp['template_code'])->getId()) {
		            continue;
		        }

		        $templates[] = $dataTemp;
		    }
			$temId = 20;
		    foreach ($templates as $dataTemp) {
		        $model
		            ->setData($dataTemp)
		            ->setTemplateType(Mage_Core_Model_Email_Template::TYPE_HTML)
		            ->setTemplateActual(1)
		            ->save();        
				$temId ++;
		    }
		} catch (Exception $e) {
		    Mage::logException($e);		
		}
	}
}