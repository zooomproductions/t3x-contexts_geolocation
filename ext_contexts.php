<?php
/**
 * Geolocation contexts: Context registration
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
defined('TYPO3_MODE') or die('Access denied.');

// Register context
Tx_Contexts_Api_Configuration::registerContextType(
    'geolocation_country',
    'LLL:EXT:contexts_geolocation/Resources/Private/Language/locallang_db.xml:title_country',
    'Tx_ContextsGeolocation_Context_Type_Country',
    'FILE:EXT:contexts_geolocation/Configuration/flexform/Country.xml'
);
Tx_Contexts_Api_Configuration::registerContextType(
    'geolocation_continent',
    'LLL:EXT:contexts_geolocation/Resources/Private/Language/locallang_db.xml:title_continent',
    'Tx_ContextsGeolocation_Context_Type_Continent',
    'FILE:EXT:contexts_geolocation/Configuration/flexform/Continent.xml'
);
Tx_Contexts_Api_Configuration::registerContextType(
    'geolocation_distance',
    'LLL:EXT:contexts_geolocation/Resources/Private/Language/locallang_db.xml:title_distance',
    'Tx_ContextsGeolocation_Context_Type_Distance',
    'FILE:EXT:contexts_geolocation/Configuration/flexform/Distance.xml'
);

?>
