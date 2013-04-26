<?php
$extensionPath        = t3lib_extMgm::extPath('contexts_geolocation');
$extensionClassesPath = $extensionPath . 'Classes/';

return array(
    'tx_contexts_geolocation_backend'
        => $extensionClassesPath . 'Backend.php',

    'tx_contexts_geolocation_context_type_continent'
        => $extensionClassesPath . 'Context/Type/Continent.php',
    'tx_contexts_geolocation_context_type_country'
        => $extensionClassesPath . 'Context/Type/Country.php',
);
?>
