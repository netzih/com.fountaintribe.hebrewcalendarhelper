<?php
// This file declares a managed database record of type "CustomSearch".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
// The legacy custom search needs the legacycustomsearches extension; Search > Yahrzeits
// (SearchKit) replaces it.
if (!CRM_Extension_System::singleton()->getMapper()->isActiveModule('legacycustomsearches')) {
  return [];
}
return [
  0 => 
  [
    'name' => 'CRM_Hebrewcalendarhelper_Form_Search_YahrzeitSearch',
    'entity' => 'CustomSearch',
    'params' => 
    [
      'version' => 3,
      'label' => 'Yahrzeit Search',
      'description' => 'Yahrzeit Search (com.fountaintribe.hebrewcalendarhelper)',
      'class_name' => 'CRM_Hebrewcalendarhelper_Form_Search_YahrzeitSearch',
    ],
  ],
];
