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
 * Localise export API
 *
 * @category    Ffm
 * @package     Ffm_LocaliseApi
 * @author      Sander Mangel <s.mangel@fitforme.nl>
 */

namespace Localise;

class Export
{
    /**
     * @param $locale
     * @param $apiKey
     * @param array $params
     * @param string $format
     * @return mixed|null
     * @throws \Exception
     */
    public function post($locale, $strings, $apiKey, $params=[], $format = 'json')
    {
        $params = array_merge([
            'index' => 'id',
            'locale' => $locale,
            'key' => $apiKey,
        ], $params);

        $postUrl = "https://localise.biz/api/import/{$format}?" . http_build_query($params, '', '&');

        if ($ch = curl_init()) {
            curl_setopt($ch,CURLOPT_URL, $postUrl);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch,CURLOPT_POST, true);
            curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type: text/json'));
            curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($strings));

            $result = curl_exec($ch);
            $cError = curl_error($ch);
            $headers = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);
        }

        $error = '';
        if ($result === false) {
            $error = $cError;
        } else if (!strstr($headers, '200') && !strstr($headers, '304')) {
            $error = "HTTP Status was: {$headers}";
        }


        if (strlen($error)) {
            throw new \Exception($error);
            return null;
        } else {
            return $result;
        }
    }
}