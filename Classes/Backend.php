<?php
/**
 * Part of geolocation context extension.
 *
 * PHP version 5
 *
 * @category   TYPO3-Extensions
 * @package    Contexts
 * @subpackage Geolocation
 * @author     Christian Weiske <christian.weiske@netresearch.de>
 * @license    http://opensource.org/licenses/gpl-license GPLv2 or later
 * @link       http://github.com/netresearch/contexts_geolocation
 */

/**
 * Provides methods used in the backend by flexforms.
 *
 * @category   TYPO3-Extensions
 * @package    Contexts
 * @subpackage Geolocation
 * @author     Christian Weiske <christian.weiske@netresearch.de>
 * @license    http://opensource.org/licenses/gpl-license GPLv2 or later
 * @link       http://github.com/netresearch/contexts_geolocation
 */
class Tx_Contexts_Geolocation_Backend
{
    /**
     * Get all countries from static info tables.
     * Uses the three-letter country code as key instead of the uid.
     *
     * @param array  &$params      Additional parameters
     * @param object $parentObject Parent object instance
     *
     * @return void
     */
    public function getCountries(array &$params, $parentObject)
    {
        $arRows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            'cn_iso_3 AS code, cn_short_en AS name',
            'static_countries',
            '', 'name ASC'
        );
        $params['items'][] = array('- unknown -', '*unknown*');
        foreach ($arRows as $arRow) {
            $params['items'][] = array(
                $arRow['name'], $arRow['code']
            );
        }
    }
}
?>
