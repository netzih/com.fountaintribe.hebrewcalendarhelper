<?php

/**
 * Settings for Hebrew Calendar Helper. The page is created by the setting-admin mixin at
 * civicrm/admin/setting/hebrewcalendarhelper (Administer > System Settings).
 */
return [
  'hebrewcalendarhelper_bmitzvah_age_female' => [
    'name' => 'hebrewcalendarhelper_bmitzvah_age_female',
    'type' => 'Integer',
    'html_type' => 'select',
    'options' => [12 => ts('12 years old'), 13 => ts('13 years old')],
    // Sites that never chose an age have always been calculated at 13.
    'default' => 13,
    'is_domain' => 1,
    'is_contact' => 0,
    'title' => ts('Earliest bat mitzvah age'),
    'description' => ts('Used for the "Earliest Possible Bar/Bat Mitzvah Date" of female contacts. Everyone else, including contacts with no gender, uses 13. The age is counted on the Hebrew calendar, so the date of birth and "born before sunset" need to be filled in.'),
    'settings_pages' => ['hebrewcalendarhelper' => ['weight' => 10]],
  ],
  'hebrewcalendarhelper_yahrzeit_adar' => [
    'name' => 'hebrewcalendarhelper_yahrzeit_adar',
    'type' => 'String',
    'html_type' => 'select',
    'options' => [
      'adar2' => ts('Adar II'),
      'adar1' => ts('Adar I'),
    ],
    'default' => 'adar2',
    'is_domain' => 1,
    'is_contact' => 0,
    'title' => ts('Yahrzeit in a leap year, for a death in Adar of a regular year'),
    'description' => ts('A leap year has two months of Adar. Customs differ on which one holds the yahrzeit of someone who passed away in Adar of a regular year; ask your Rav. Deaths in Adar I or Adar II of a leap year always keep their own month.'),
    'settings_pages' => ['hebrewcalendarhelper' => ['weight' => 20]],
  ],
];
