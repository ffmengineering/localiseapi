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
 * Connector translatecsv export
 *
 * @category    Ffm
 * @package     Ffm_LocaliseApi
 * @author      Sander Mangel <s.mangel@fitforme.nl>
 */
class Ffm_LocaliseApi_Model_Connectors_Exporter_Translatecsv extends Ffm_LocaliseApi_Model_Connectors_Exporter_Abstract
{
    protected $_assetName = 'translatecsv';
    protected $_attributeCodes = [
        'identifier',
        'translation',
    ];
    protected $_identifierCode = 'identifier';

    public function process($locale, $dateFrom)
    {
        $csvCollection = [];
        $collection = new Varien_Data_Collection();

        // Get all the 3rd party translate files from the base locale
        $baseCsvPath = Mage::getBaseDir('locale') . DS . $locale . DS;
        foreach (glob("{$baseCsvPath}*.csv") as $filename) {
            if (stristr($filename, 'Mage_') || stristr($filename, 'Enterprise_')) // no core translate CSVs
                continue;
            if (filemtime($filename) < strtotime($dateFrom)) // filter by date
                continue;

            $csvCollection[] = $filename;
        }

        // Get the translation files from themes
        $stores = Mage::getModel('core/store')->getCollection();
        foreach ($stores as $store) {
            $package = Mage::getStoreConfig('design/package/name', $store->getId());
            $theme = Mage::getStoreConfig('design/theme/locale', $store->getId());

            $path = Mage::getBaseDir('design') . DS . 'frontend' . DS . $package . DS . $theme . DS . 'locale' . DS . $locale . DS . 'translate.csv';

            if (file_exists($path) && filemtime($path) < strtotime($path)) { // filter by date
                $csvCollection[] = $path;
            }
        }

        // process the strings
        foreach ($csvCollection as $file) {
            if (($handle = fopen($file, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $varienObject = new Varien_Object([
                        'identifier' => (strstr($data[0], '::')) ? $data[0] : basename($file, ".csv") . "::{$data[0]}", // already has a scope
                        'translation' => $data[1],
                    ]);
                    if (!$collection->hasFlag($varienObject->getIdentifier())) {
                        $collection->setFlag($varienObject->getIdentifier());
                        $collection->addItem($varienObject);
                    }
                }
                fclose($handle);
            }
        }

        $this->_queue($collection, $locale);
    }
}