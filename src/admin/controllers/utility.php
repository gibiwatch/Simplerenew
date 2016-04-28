<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewControllerUtility extends SimplerenewControllerBase
{
    public function checkssl()
    {
        $userAgent = sprintf(
            'User-Agent: OSTraining Tester/1.0; PHP %s [%s]',
            phpversion(),
            php_uname('s')
        );

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL            => 'https://www.howsmyssl.com/a/check',
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            //CURLOPT_CAINFO =>
            //CURLOPT_SSLVERSION     => CURL_SSLVERSION_TLSv1_2,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS      => 1,
            CURLOPT_HEADER         => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 45,
            CURLOPT_HTTPHEADER     => array(
                'Content-Type: application/json; charset=utf-8',
                'Accept: application/json',
                $userAgent,
                'Accept-Language: en-US'
            )
        ));

        $response = curl_exec($ch);
        $info     = curl_getinfo($ch);

        curl_close($ch);

        $headerSize = $info['header_size'];
        $headers    = substr($response, 0, $headerSize);
        $body       = substr($response, $headerSize - 1);

        echo '<pre>';
        print_r($info);
        print_r($headers);
        print_r(json_decode($body));
        echo '</pre>';
        return;

        $params      = SimplerenewComponentHelper::getParams();
        $apiKey      = $params->get('gateways.recurly.live.apiKey');
        $recurlyPath = JPATH_PLUGINS
            . '/simplerenew/recurly/library/simplerenew/Gateway/Recurly/api/autoloader.php';

        require_once $recurlyPath;

        $userAgent = sprintf(
            'User-Agent: Recurly Tester/%s; PHP %s [%s]',
            Recurly_Client::API_CLIENT_VERSION,
            phpversion(),
            php_uname('s')
        );

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL            => 'https://ostraining.recurly.com/v2/accounts',
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            //CURLOPT_CAINFO =>
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS      => 1,
            CURLOPT_HEADER         => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 45,
            CURLOPT_HTTPHEADER     => array(
                'Content-Type: application/xml; charset=utf-8',
                'Accept: application/xml',
                $userAgent,
                'Accept-Language: en-US',
                'X-Api-Version: ' . Recurly_Client::$apiVersion
            ),
            CURLOPT_USERPWD        => $apiKey

        ));

        $response = curl_exec($ch);
        $info     = curl_getinfo($ch);

        curl_close($ch);

        echo '<pre>';
        print_r($info);
        echo '</pre>';

    }
}
