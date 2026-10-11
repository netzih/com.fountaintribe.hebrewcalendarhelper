<?php
use CRM_Hebrewcalendarhelper_ExtensionUtil as E;

/**
 * Yahrzeits: one row per deceased person, mourner and year, for both the Hebrew-date and the
 * English-date yahrzeit. AllHebrewDates.calculate rebuilds the rows, so they are read-only.
 *
 * The table keeps its historical name. Contact IDs have no foreign keys, so the job can build
 * a fresh copy of the table and swap it in.
 */
return [
  'name' => 'Yahrzeit',
  'table' => 'civicrm_fountaintribe_yahrzeits_temp',
  'class' => 'CRM_Hebrewcalendarhelper_DAO_Yahrzeit',
  'getInfo' => fn() => [
    'title' => E::ts('Yahrzeit'),
    'title_plural' => E::ts('Yahrzeits'),
    'description' => E::ts('Calculated yahrzeit dates, per deceased person and mourner.'),
    'icon' => 'fa-fire',
    'label_field' => 'deceased_name',
  ],
  'getIndices' => fn() => [
    'mourner_to_yah_date' => [
      'fields' => [
        'mourner_contact_id' => TRUE,
        'deceased_contact_id' => TRUE,
        'yahrzeit_date' => TRUE,
        'yahrzeit_relationship_id' => TRUE,
        'yahrzeit_type' => TRUE,
      ],
      'unique' => TRUE,
    ],
  ],
  'getFields' => fn() => [
    'id' => [
      'title' => E::ts('ID'),
      'sql_type' => 'int unsigned',
      'input_type' => 'Number',
      'required' => TRUE,
      'primary_key' => TRUE,
      'auto_increment' => TRUE,
    ],
    'mourner_contact_id' => [
      'title' => E::ts('Mourner'),
      'sql_type' => 'int unsigned',
      'input_type' => 'EntityRef',
      'required' => TRUE,
      'entity_reference' => ['entity' => 'Contact', 'key' => 'id', 'fk' => FALSE],
    ],
    'mourner_name' => [
      'title' => E::ts('Mourner Name'),
      'sql_type' => 'varchar(500)',
      'input_type' => 'Text',
      'required' => TRUE,
    ],
    'deceased_contact_id' => [
      'title' => E::ts('Deceased'),
      'sql_type' => 'int unsigned',
      'input_type' => 'EntityRef',
      'required' => TRUE,
      'entity_reference' => ['entity' => 'Contact', 'key' => 'id', 'fk' => FALSE],
    ],
    'deceased_name' => [
      'title' => E::ts('Deceased Name'),
      'sql_type' => 'varchar(500)',
      'input_type' => 'Text',
      'required' => TRUE,
    ],
    'deceased_date' => [
      'title' => E::ts('Date of Death'),
      'sql_type' => 'datetime',
      'input_type' => 'Select Date',
      'required' => TRUE,
    ],
    'd_before_sunset' => [
      'title' => E::ts('Died Before Sunset'),
      'sql_type' => 'varchar(5)',
      'input_type' => 'Text',
    ],
    'hebrew_deceased_date' => [
      'title' => E::ts('Hebrew Date of Death'),
      'sql_type' => 'varchar(256)',
      'input_type' => 'Text',
    ],
    'yahrzeit_date' => [
      'title' => E::ts('Yahrzeit (evening)'),
      'sql_type' => 'datetime',
      'input_type' => 'Select Date',
      'description' => E::ts('The evening the yahrzeit begins, when the candle is lit.'),
    ],
    'yahrzeit_date_morning' => [
      'title' => E::ts('Yahrzeit (morning)'),
      'sql_type' => 'datetime',
      'input_type' => 'Select Date',
    ],
    'yahrzeit_hebrew_date_format_hebrew' => [
      'title' => E::ts('Yahrzeit Hebrew Date (Hebrew letters)'),
      'sql_type' => 'varchar(256)',
      'input_type' => 'Text',
    ],
    'yahrzeit_hebrew_date_format_english' => [
      'title' => E::ts('Yahrzeit Hebrew Date'),
      'sql_type' => 'varchar(256)',
      'input_type' => 'Text',
    ],
    'yahrzeit_hebrew_year' => [
      'title' => E::ts('Yahrzeit Hebrew Year'),
      'sql_type' => 'int',
      'input_type' => 'Number',
    ],
    'yahrzeit_hebrew_month' => [
      'title' => E::ts('Yahrzeit Hebrew Month'),
      'sql_type' => 'int',
      'input_type' => 'Select',
      'pseudoconstant' => ['callback' => 'Civi\Hebrewcalendarhelper\Yahrzeits::hebrewMonths'],
    ],
    'yahrzeit_hebrew_day' => [
      'title' => E::ts('Yahrzeit Hebrew Day'),
      'sql_type' => 'int',
      'input_type' => 'Number',
    ],
    'relationship_name_formatted' => [
      'title' => E::ts('Relationship to Mourner'),
      'sql_type' => 'varchar(256)',
      'input_type' => 'Text',
    ],
    'yahrzeit_type' => [
      'title' => E::ts('Yahrzeit Calendar'),
      'sql_type' => 'varchar(256)',
      'input_type' => 'Select',
      'description' => E::ts('Whether this row is the Hebrew-date or English-date yahrzeit. Each pair has both; the mourner observes one of them.'),
      'pseudoconstant' => ['callback' => 'Civi\Hebrewcalendarhelper\Yahrzeits::calendarTypes'],
    ],
    'mourner_observance_preference' => [
      'title' => E::ts('Mourner Observes'),
      'sql_type' => 'varchar(256)',
      'input_type' => 'Select',
      'description' => E::ts('Which yahrzeit the mourner observes. Rows where this equals Yahrzeit Calendar are the ones observed.'),
      'pseudoconstant' => ['callback' => 'Civi\Hebrewcalendarhelper\Yahrzeits::calendarTypes'],
    ],
    'plaque_location' => [
      'title' => E::ts('Plaque Location'),
      'sql_type' => 'varchar(500)',
      'input_type' => 'Text',
    ],
    'shabbat_before_parashat' => [
      'title' => E::ts('Parashat of the Shabbat Before'),
      'sql_type' => 'varchar(250)',
      'input_type' => 'Text',
    ],
    'shabbat_before_parashat_hebrew' => [
      'title' => E::ts('Parashat of the Shabbat Before (Hebrew)'),
      'sql_type' => 'varchar(250)',
      'input_type' => 'Text',
    ],
    'shabbat_before_holiday' => [
      'title' => E::ts('Holiday on the Shabbat Before'),
      'sql_type' => 'varchar(250)',
      'input_type' => 'Text',
    ],
    'shabbat_before_holiday_hebrew' => [
      'title' => E::ts('Holiday on the Shabbat Before (Hebrew)'),
      'sql_type' => 'varchar(250)',
      'input_type' => 'Text',
    ],
    'shabbat_after_parashat' => [
      'title' => E::ts('Parashat of the Shabbat After'),
      'sql_type' => 'varchar(250)',
      'input_type' => 'Text',
    ],
    'shabbat_after_parashat_hebrew' => [
      'title' => E::ts('Parashat of the Shabbat After (Hebrew)'),
      'sql_type' => 'varchar(250)',
      'input_type' => 'Text',
    ],
    'shabbat_after_holiday' => [
      'title' => E::ts('Holiday on the Shabbat After'),
      'sql_type' => 'varchar(250)',
      'input_type' => 'Text',
    ],
    'shabbat_after_holiday_hebrew' => [
      'title' => E::ts('Holiday on the Shabbat After (Hebrew)'),
      'sql_type' => 'varchar(250)',
      'input_type' => 'Text',
    ],
    'shabbat_before_hebrew_date_format_english' => [
      'title' => E::ts('Hebrew Date of the Shabbat Before'),
      'sql_type' => 'varchar(256)',
      'input_type' => 'Text',
    ],
    'shabbat_before_hebrew_year_num' => [
      'title' => E::ts('Hebrew Year of the Shabbat Before'),
      'sql_type' => 'int',
      'input_type' => 'Number',
    ],
    'shabbat_before_hebrew_month_num' => [
      'title' => E::ts('Hebrew Month of the Shabbat Before'),
      'sql_type' => 'int',
      'input_type' => 'Number',
    ],
    'shabbat_before_hebrew_day_num' => [
      'title' => E::ts('Hebrew Day of the Shabbat Before'),
      'sql_type' => 'int',
      'input_type' => 'Number',
    ],
    'yahrzeit_erev_shabbat_before' => [
      'title' => E::ts('Erev Shabbat Before'),
      'sql_type' => 'datetime',
      'input_type' => 'Select Date',
    ],
    'yahrzeit_shabbat_morning_before' => [
      'title' => E::ts('Shabbat Morning Before'),
      'sql_type' => 'datetime',
      'input_type' => 'Select Date',
    ],
    'yahrzeit_erev_shabbat_after' => [
      'title' => E::ts('Erev Shabbat After'),
      'sql_type' => 'datetime',
      'input_type' => 'Select Date',
    ],
    'yahrzeit_shabbat_morning_after' => [
      'title' => E::ts('Shabbat Morning After'),
      'sql_type' => 'datetime',
      'input_type' => 'Select Date',
    ],
    'shabbat_after_hebrew_date_format_english' => [
      'title' => E::ts('Hebrew Date of the Shabbat After'),
      'sql_type' => 'varchar(256)',
      'input_type' => 'Text',
    ],
    'shabbat_after_hebrew_year_num' => [
      'title' => E::ts('Hebrew Year of the Shabbat After'),
      'sql_type' => 'int',
      'input_type' => 'Number',
    ],
    'shabbat_after_hebrew_month_num' => [
      'title' => E::ts('Hebrew Month of the Shabbat After'),
      'sql_type' => 'int',
      'input_type' => 'Number',
    ],
    'shabbat_after_hebrew_day_num' => [
      'title' => E::ts('Hebrew Day of the Shabbat After'),
      'sql_type' => 'int',
      'input_type' => 'Number',
    ],
    'yahrzeit_relationship_id' => [
      'title' => E::ts('Relationship ID'),
      'sql_type' => 'varchar(25)',
      'input_type' => 'Text',
    ],
    'created_date' => [
      'title' => E::ts('Calculated'),
      'sql_type' => 'timestamp',
      'input_type' => 'Select Date',
      'default' => 'CURRENT_TIMESTAMP',
    ],
  ],
];
