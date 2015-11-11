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
 * Connector pages export
 *
 * @category    Ffm
 * @package     Ffm_LocaliseApi
 * @author      Sander Mangel <s.mangel@fitforme.nl>
 */
class Ffm_LocaliseApi_Model_Connectors_Exporter_Pages extends Ffm_LocaliseApi_Model_Connectors_Exporter_Abstract
{
    protected $_assetName = 'pages';
    protected $_attributeCodes = [
        'title',
        'meta_keywords',
        'meta_description',
        'identifier',
        'content_heading',
        'content',
        'i8l_identifier',
    ];

    public function process($locale, $dateFrom)
    {
        $collection = Mage::getModel('cms/page')->getCollection()
            ->addStoreFilter(0)
            ->addFieldToFilter('updated_at', array('gteq' => $dateFrom))
            ->addFieldToSelect($this->_attributeCodes);

        $this->_queue($collection, $locale);
    }
}