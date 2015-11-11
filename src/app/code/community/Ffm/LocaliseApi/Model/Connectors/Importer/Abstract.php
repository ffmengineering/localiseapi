<?php
/**
 * Ffm_LocaliseApi extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Ffm
 * @package        Ffm_LocaliseApi
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Connector abstract import & interface
 *
 * @category    Ffm
 * @package     Ffm_LocaliseApi
 * @author      Sander Mangel <s.mangel@fitforme.nl>
 */


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