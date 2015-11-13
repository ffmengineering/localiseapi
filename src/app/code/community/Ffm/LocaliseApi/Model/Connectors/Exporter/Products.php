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
        $this->_attributeCodes[] = $this->_identifierCode;

        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter('updated_at', array('gteq' => $dateFrom))
            ->addAttributeToSelect($this->_attributeCodes);

		$collection->getSelect()->group('i8l_identifier'); 

        $this->_queue($collection, $locale);
    }
}
