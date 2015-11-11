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
 * Connector translatecsv import
 *
 * @category    Ffm
 * @package     Ffm_LocaliseApi
 * @author      Sander Mangel <s.mangel@fitforme.nl>
 */
class Ffm_LocaliseApi_Model_Connectors_Importer_Translatecsv extends Ffm_LocaliseApi_Model_Connectors_Importer_Abstract
{
    protected $_assetName = 'translatecsv';

    public function process($storeId, $locale)
    {
        $data = $this->_getTranslatePackage($locale);

        if (!count($data)) return true; // no results

        // get translate CSV of template
        $package = Mage::getStoreConfig('design/package/name', $storeId);
        $translation = Mage::getStoreConfig('design/theme/locale', $storeId);
        $localeCsv = Mage::getBaseDir('design') . DS . 'frontend' . DS . $package . DS . $translation . DS . 'locale' . DS . $locale . DS . 'translate.csv';

        $ioAdapter = new Varien_Io_File();

        if (!is_dir($ioAdapter->dirname($localeCsv))) {
            mkdir($ioAdapter->dirname($localeCsv), 0755, true);
        }

        $ioAdapter->open(array('path' => $ioAdapter->dirname($localeCsv)));
        $ioAdapter->streamOpen($localeCsv, 'w+', 0644);

        foreach ($data as $key => $translation) {
            $ioAdapter-> streamWriteCsv([
                $key, $translation
            ], ',', '"');
        }

        $ioAdapter->streamClose();
    }
}