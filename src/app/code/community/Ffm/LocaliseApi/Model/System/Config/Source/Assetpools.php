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
 * Assetpool source
 *
 * @category    Ffm
 * @package     Ffm_LocaliseApi
 * @author      Sander Mangel <s.mangel@fitforme.nl>
 */
class Ffm_LocaliseApi_Model_System_Config_Source_Assetpools
{
    public function toOptionArray()
    {
        $options = [];

        $importers = $this->getConnectors();
        foreach ($importers as $importer) {
            $options[] = ['value' => $importer['code'], 'label' => $importer['code']];
        }

        return $options;
    }

    /**
     * @return array
     */
    public function getConnectors()
    {
        $xmlString = Mage::getConfig()
            ->loadModulesConfiguration('config.xml')
            ->getNode('localise_connectors')
            ->asXML();

        $dom = new DOMDocument();
        $dom->loadXml($xmlString);

        $options = [];

        $importNode = $dom->getElementsByTagName('localise_connectors')->item(0);
        if ($importNode->hasChildNodes()) {
            foreach ($importNode->childNodes as $importer) {
                $options[] = [
                    'code' => $importer->nodeName,
                    'importer' => $importer->getElementsByTagName('importer')->item(0)->nodeValue,
                    'exporter' => $importer->getElementsByTagName('exporter')->item(0)->nodeValue
                ];
            }
        }

        return $options;
    }
}