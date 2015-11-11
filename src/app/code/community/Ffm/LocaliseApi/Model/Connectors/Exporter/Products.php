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
 * Connector products export
 *
 * @category    Ffm
 * @package     Ffm_LocaliseApi
 * @author      Sander Mangel <s.mangel@fitforme.nl>
 */
class Ffm_LocaliseApi_Model_Connectors_Exporter_Products extends Ffm_LocaliseApi_Model_Connectors_Exporter_Abstract
{
    protected $_assetName = 'products';

    public function process($locale, $dateFrom)
    {
        $this->_attributeCodes = explode(',', Mage::getStoreConfig('general/localiseapi/productattributes', 0));

        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter('updated_at', array('gteq' => $dateFrom))
            ->addAttributeToSelect($this->_attributeCodes)
            ->addAttributeToSelect('i8l_identifier');

        $this->_queue($collection, $locale);
    }
}