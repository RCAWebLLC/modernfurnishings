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
class ExtensionsMall_CustomOptionSwatch_Model_Import extends Mage_Core_Model_Abstract {

    protected $postData = NULL;
    protected $unzipDir = NULL;
    protected $swatchDir = NULL;
    protected $files = NULL;
    protected $extensions = array("jpg", "bmp", "jpeg", "tif", "png");
    protected $replacedFiles = 0;
    protected $log = array();

    public function _construct() {
        $this->_init('custom_option_swatch/import');
    }

    /*
     * Proccess post
     */

    public function processImport($data) {
        $this->postData = $data;
        if (isset($_FILES['file']) && $_FILES['file']['type'] == 'application/zip') {
            $this->manageZipArchive();
        }
        if ($this->postData['action'] == 'import') {
            $this->importSwatch();
        } elseif ($this->postData['action'] == 'update') {
            $this->updateSwatch();
        }
        $this->writeLog();
    }

    /*
     * get swatches from DB
     * 
     */

    public function getSwatches() {
        $swatchModel = Mage::getModel('custom_option_swatch/swatches');
        $collection = $swatchModel->getCollection();
        $swatches = array();
        foreach ($collection as $obj) {
            $swatches[$obj->getId()] = $obj->getImageBase();
        }
        return $swatches;
    }

    /*
     * Match swatches
     * 
     */

    public function updateSwatch() {
        $files = $this->getFiles($this->getUnzipDir());
        $swatches = $this->getSwatches();
        foreach ($files as $file) {
            $pathinfo = pathinfo($file);
            if (in_array($pathinfo['extension'], $this->extensions) && in_array($pathinfo['basename'], $swatches)) {
                $swatchId = array_search($pathinfo['basename'], $swatches);
                $source = $this->getUnzipDir() . DIRECTORY_SEPARATOR . $file;
                $this->moveFile($swatchId, $source, $pathinfo['basename']);
            } else {
                $this->log['not-found'][] = $file;
            }
        }
    }

    /*
     * Import swatches
     * 
     */

    public function importSwatch() {

        $files = $this->getFiles($this->getUnzipDir());
        $swatchModel = Mage::getModel('custom_option_swatch/swatches');
        foreach ($files as $file) {
            $data = $this->generateNames($file);
            $swatchModel->setData($data);
            $swatchModel->setCreatedTime(now())->setUpdateTime(now());
            $swatchModel->save();
            $id = $swatchModel->getData('entity_id');
            $source = $this->getUnzipDir() . DIRECTORY_SEPARATOR . $file;
            $this->moveFile($id, $source, $data['image_base']);
            $swatchModel->unsetData();
        }
    }

    protected function writeLog() {
        $data = array(
            'num_submited' => count($this->files),
            'num_processed' => count($this->log['processed']));
        $data['notes'] = "Files processed: \n";
        $data['notes'] .= implode("\n", $this->log['processed']) . "\n";
        $data['notes'] .= "Files not found: \n";
        $data['notes'] .= implode("\n", $this->log['not-found']) . "\n";
        $logModel = Mage::getModel('custom_option_swatch/log');
        $logModel->setData($data);
        $logModel->save();
    }

    /*
     * Get file info
     * 
     * @return array
     */

    protected function generateNames($file) {
        $pathinfo = pathinfo($file);
        if (in_array($pathinfo['extension'], $this->extensions)) {
            $temp = explode('-', $pathinfo['filename']);
            $reversed = array_reverse($temp);
            $colors = explode('_', $reversed[0]);
            $lowercase = array_map("strtolower", $colors);
            $ucfirst = array_map("ucfirst", $lowercase);
            $swatchName = implode('/', $ucfirst);
            $filename = preg_replace('/[^a-zA-Z0-9-]/', '-', strtolower($pathinfo['filename']));
            $basename = $filename . '.' . strtolower($pathinfo['extension']);
            return array('name' => $swatchName, 'image_base' => $basename);
        }
    }

    /*
     * Unpack zip archive with swatches and delete temporary file
     */

    public function manageZipArchive() {
        $zip = new ZipArchive();
        $x = $zip->open($_FILES['file']['tmp_name']);
        if ($x === true) {
            $zip->extractTo($this->getUnzipDir()); // place in the directory with same name
            $zip->close();
            unlink($_FILES['file']['tmp_name']);
        }
    }

    /*
     * Move swatch from temporary location to swatch dir
     */

    protected function moveFile($id, $source, $imagename) {
        $fileDir = $this->getSwatchDir() . DIRECTORY_SEPARATOR . $id;
        if (!file_exists($fileDir)) {
            mkdir($fileDir);
        }
        $replaced = rename($source, $fileDir . DIRECTORY_SEPARATOR . $imagename);
        if ($replaced) {
            $this->log['processed'][] = $imagename;
            $this->replacedFiles++;
        }
    }

    /*
     * Get folder where swatches are stored and create it if not exist
     * 
     * @return string
     */

    public function getSwatchDir() {
        if (!isset($this->swatchDir)) {
            $baseDir = Mage::getBaseDir('media');
            $this->swatchDir = $baseDir . DIRECTORY_SEPARATOR . 'custom_option_swatch';
            if (!file_exists($this->swatchDir)) {
                mkdir($this->swatchDir, 0777, true);
            }
        }
        return $this->swatchDir;
    }

    /*
     * Get folder for temporary unziped files and create it if not exist
     * 
     * @return string
     */

    public function getUnzipDir() {
        if (!isset($this->unzipDir)) {
            $baseDir = Mage::getBaseDir('media');
            $this->unzipDir = $baseDir . '/swatch_import';
            if (!file_exists($this->unzipDir)) {
                mkdir($this->unzipDir, 0777, true);
            }
        }
        return $this->unzipDir;
    }

    /*
     * Scan folder and subfolder for files 
     * 
     * @return array
     */

    protected function getFiles($dir) {
        if (!isset($this->files)) {
            $this->files = $this->dirToArray($dir);
        }
        return $this->files;
    }

    /*
     * Scan folder and subfolder for files 
     * 
     * @return array
     */

    protected function dirToArray($dir) {
        $result = array();
        $cdir = scandir($dir);
        foreach ($cdir as $key => $value) {
            if (!in_array($value, array(".", ".."))) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $subdir = $this->dirToArray($dir . DIRECTORY_SEPARATOR . $value);
                    foreach ($subdir as $sub) {
                        $result[] = $value . DIRECTORY_SEPARATOR . $sub;
                    }
                } else {
                    $result[] = $value;
                }
            }
        }
        return $result;
    }

}
