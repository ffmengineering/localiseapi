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
abstract class Ffm_LocaliseApi_Model_Connectors_Exporter_Abstract
{
    protected $_attributeCodes = [];
    protected $_identifierCode = 'i8l_identifier';
    protected $_assetName = 'misc';

    protected function _queue($collection, $locale)
    {
        foreach ($collection as $_item) {
            $result = [];
            foreach ($this->_attributeCodes as $code) {
                $value = $_item->getData($code);
                if (empty($value)) continue;

                $result[$code] = $value;
            }

            $identifier = $result[$this->_identifierCode];
            unset($result[$this->_identifierCode]);

            Mage::getModel('ffm_localiseapi/observer')->queueData($identifier, $result, $locale, $this->_assetName);
        }
    }
}