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
 * Connector blocks export
 *
 * @category    Ffm
 * @package     Ffm_LocaliseApi
 * @author      Sander Mangel <s.mangel@fitforme.nl>
 */
class Ffm_LocaliseApi_Model_Connectors_Exporter_Blocks extends Ffm_LocaliseApi_Model_Connectors_Exporter_Abstract
{
    protected $_assetName = 'blocks';
    protected $_attributeCodes = [
        'title',
        'identifier',
        'content',
    ];
    protected $_identifierCode = 'identifier';

    public function process($locale, $dateFrom)
    {
        $collection = Mage::getModel('cms/block')->getCollection()
            ->addStoreFilter(0)
            ->addFieldToFilter('update_time', array('gteq' => $dateFrom))
            ->addFieldToSelect($this->_attributeCodes);

        $this->_queue($collection, $locale);
    }
}