<?php
use CRM_Hebrewcalendarhelper_ExtensionUtil as E;

return [
  'type' => 'search',
  'title' => E::ts('Yahrzeits'),
  'description' => E::ts('Mourners and the yahrzeits they observe, with filters for dates, Hebrew month and groups.'),
  'icon' => 'fa-fire',
  'server_route' => 'civicrm/yahrzeits',
  'permission' => ['access CiviCRM', 'view all contacts'],
  'navigation' => [
    'parent' => 'Search',
    'label' => E::ts('Yahrzeits'),
    'weight' => 20,
  ],
];
