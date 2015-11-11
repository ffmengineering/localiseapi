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
 * Product entity resource model additions
 *
 * @category    Ffm
 * @package     Ffm_LocaliseApi
 * @author      Sander Mangel <s.mangel@fitforme.nl>
 */
class Ffm_LocaliseApi_Model_Resource_Product extends Mage_Catalog_Model_Resource_Product
{

    /**
     * Get product identifier by sku
     *
     * @param string $sku
     * @return int|false
     */
    public function getIdByI8l($i8l, $websiteId)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from(array('e' => $this->getEntityTable()), 'entity_id')
            ->where('i8l_identifier = :i8l');

        $select->joinInner(
            array('w' => $this->getTable('catalog/product_website')),
            $adapter->quoteInto(
                'w.product_id = e.entity_id AND w.website_id = ?', $websiteId
            ),
            array()
        );

        $bind = array(':i8l' => (string)$i8l);

        return $adapter->fetchOne($select, $bind);
    }
}
