<?php
/**
 * Ffm_LocaliseApi extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the OSL 3.0 License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 *
 * @category       Ffm
 * @package        Ffm_LocaliseApi
 * @copyright      Copyright (c) 2015
 * @license        OSL 3.0 http://opensource.org/licenses/OSL-3.0
 */
/**
 * Connector abstract import & interface
 *
 * @category    Ffm
 * @package     Ffm_LocaliseApi
 * @author      Sander Mangel <s.mangel@fitforme.nl>
 */

require Mage::getBaseDir('lib') . DS . 'Localise' . DS . 'Import.php';

use Localise;

abstract class Ffm_LocaliseApi_Model_Connectors_Importer_Abstract
{
    protected function _getTranslatePackage($locale)
    {
        $apiKey = Mage::helper('ffm_localiseapi')->getApiKey($this->_assetName);

        $import = new Localise\Import;

        try {
            $result = $import->get($locale, $apiKey, [
                'index' => 'name',
                'status' => 'translated',
            ]);
        } catch(Exception $e) {
            Mage::log("getTranslatePackage API error: {$e->getMessage()}", Zend_Log::INFO, 'translation.log', true);
            throw new Exception($error);
            return null;
        }

        return $result;
    }
}