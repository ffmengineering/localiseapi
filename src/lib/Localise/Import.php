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
 * Localise import API
 *
 * @category    Ffm
 * @package     Ffm_LocaliseApi
 * @author      Sander Mangel <s.mangel@fitforme.nl>
 */

namespace Localise;

class Import
{
    /**
     * @param $locale
     * @param $apiKey
     * @param array $params
     * @param string $format
     * @return mixed|null
     * @throws \Exception
     */
    public function get($locale, $apiKey, $params=[], $format = 'json')
    {
        $params = array_merge([
            'key' => $apiKey
        ], $params);

        $apiUrl = "https://localise.biz:443/api/export/locale/{$locale}.{$format}?" . http_build_query($params, '', '&');

        if ($ch = curl_init()) {
            curl_setopt($ch,CURLOPT_URL, $apiUrl);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch,CURLOPT_HEADER, true);
            //curl_setopt($ch,CURLOPT_USERPWD, "$apiKey:");

            $result = curl_exec($ch);
            $result = json_decode($result, true);
            $error = curl_error($ch);

            curl_close($ch);
        }

        if (isset($result['error']) && $result['error'] != '200') { // only 200 indicates succesfull data retrieval
            $error = $result['error'];
        }

        if (strlen($error)) {
            throw new \Exception($error);
            return null;
        } else {
            return $result;
        }
    }
}