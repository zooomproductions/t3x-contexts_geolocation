<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Netresearch GmbH & Co. KG <typo3.org@netresearch.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once PATH_tslib . 'class.tslib_pibase.php';

/**
 * Plugin to display the location of an IP address on a map
 *
 * @category   TYPO3-Extensions
 * @package    Contexts
 * @subpackage Geolocation
 * @author     Christian Weiske <christian.weiske@netresearch.de>
 * @license    http://opensource.org/licenses/gpl-license GPLv2 or later
 * @link       http://github.com/netresearch/contexts_geolocation
 */
class tx_contextsgeolocation_position extends tslib_pibase
{
    /**
     * The extension key.
     *
     * @var string
     */
    public $extKey = 'contexts_geolocation';

    /**
     * Same as class name.
     *
     * @var string
     */
    public $prefixId = __CLASS__;

    /**
     * Path to this script relative to the extension dir.
     *
     * @var string
     */
    public $scriptRelPath = 'pi/class.tx_contextsgeolocation_position.php';

    /**
     * Plugin is a USER_INT (page not cached).
     *
     * @var boolean
     */
    public $pi_USER_INT_obj = true;

    /**
     * Plugin main function.
     *
     * @param string $strContent The content given from Typo3
     * @param mixed  $arConf     The Typo3 configuration for this plugin
     *
     * @return string The content after this plugin.
     */
    public function main($strContent, $arConf)
    {
        $ip    = $this->getIp();
        $geoip = Tx_ContextsGeolocation_Adapter::getInstance($ip);
        $data  = $geoip->getLocation();

        return $this->pi_wrapInBaseClass(
            $strContent
            . $this->renderForm($ip)
            . $this->renderMap($ip, $data)
            . $this->renderData($ip, $data)
        );
    }

    /**
     * Returns IP from $_GET['ip'] or user's address.
     *
     * If $_GET[ip] is invalid, th user's address is used.
     *
     * @return string IP address
     */
    protected function getIp()
    {
        if (!isset($_GET['ip'])) {
            return $_SERVER['REMOTE_ADDR'];
        }

        $strGetIp = $_GET['ip'];
        $bIpv4 = filter_var(
            $strGetIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4
        ) !== false;
        $bIpv6 = filter_var(
            $strGetIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6
        ) !== false;

        if (!$bIpv4 && !$bIpv6) {
            //invalid IP
            return $_SERVER['REMOTE_ADDR'];
        }

        return $strGetIp;
    }

    /**
     * Renders the IP input form
     *
     * @param string $ip IP number
     *
     * @return string HTML code
     */
    protected function renderForm($ip)
    {
        $strIp     = htmlspecialchars($ip);
        $strPageId = htmlspecialchars($GLOBALS['TSFE']->id);
        $actionUrl = $this->pi_getPageLink($GLOBALS['TSFE']->id);

        return <<<HTM
<form method="get" action="$actionUrl">
    <input type="hidden" name="id" value="$strPageId" />
    <input type="text" size="16" name="ip" value="$strIp" placeholder="IP address" />
    <input type="submit" value="Submit" />
</form>
HTM;
    }

    /**
     * Renders the data we get from the geolocation database
     *
     * @param string $ip   IP number
     * @param array  $data Array of location data from geoip query
     *
     * @return string HTML code
     */
    protected function renderData($ip, $data)
    {
        return '<h3>Data about this IP</h3>'
            . '<pre>'
            . htmlspecialchars(var_export($data, true))
            . '</pre>';
    }

    /**
     * Renders the map
     *
     * @param string $ip   IP number
     * @param array  $data Array of location data from geoip query
     *
     * @return string HTML code
     */
    protected function renderMap($ip, $data)
    {
        if ($data['latitude'] == 0 && $data['longitude'] == 0) {
            //broken position
            return '';
        }

        $flLat = (float) $data['latitude'];
        $flLon = (float) $data['longitude'];
        $jLat  = json_encode($flLat);
        $jLon  = json_encode($flLon);
        $jZoom = 8;

        return <<<HTM
<link rel="stylesheet" href="/typo3conf/ext/contexts_geolocation/Resources/Public/JavaScript/Leaflet/leaflet.css" />
<!--[if lte IE 8]>
    <link rel="stylesheet" href="/typo3conf/ext/contexts_geolocation/Resources/Public/JavaScript/Leaflet/leaflet.ie.css" />
<![endif]-->
<script src="/typo3conf/ext/contexts_geolocation/Resources/Public/JavaScript/Leaflet/leaflet.js"></script>
<div id="map" style="margin: 10px 0px"></div>
<style type="text/css">
#map { height: 300px; }
</style>
<script type="text/javascript">
//<![CDATA[

$(document).ready(function()
{
    // Create map and set view to chosen geographical coordinates
    var map = L.map('map')
        .setView([$jLat, $jLon], $jZoom);

    // create the tile layer with correct attribution
    var osmUrl     = 'http://{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.jpg';
    var subDomains = ['otile1', 'otile2', 'otile3', 'otile4'];

    var osmAttrib = 'Data, imagery and map information provided by'
        + ' <a href="http://open.mapquest.co.uk" target="_blank">MapQuest</a>,'
        + ' <a href="http://www.openstreetmap.org/" target="_blank">OpenStreetMap</a>'
        + ' and contributors.';

    // Add tile layer
    L.tileLayer(osmUrl, {
        attribution : osmAttrib,
        subdomains  : subDomains
    }).addTo(map);

    // Add marker of current coordinates
    L.marker([$jLat, $jLon]).addTo(map);
});

//]]>
</script>
HTM;
    }
}
?>
