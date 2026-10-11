<?php
use CRM_Hebrewcalendarhelper_ExtensionUtil as E;

/**
 * "Yahrzeits": a SearchKit search of mourners with the yahrzeits they observe, shown by the
 * Search > Yahrzeits form (ang/afsearchYahrzeits.aff.*). It lists contacts, so the usual
 * contact actions (email, PDF letter, add to group) work, with the {yahrzeit.*} tokens.
 */
if (!CRM_Extension_System::singleton()->getMapper()->isActiveModule('search_kit')) {
  return [];
}

$column = fn(string $key, string $label, array $extra = []) => [
  'type' => 'field',
  'key' => $key,
  'label' => $label,
  'sortable' => TRUE,
] + $extra;

return [
  [
    'name' => 'SavedSearch_Hebrewcalendarhelper_Yahrzeits',
    'entity' => 'SavedSearch',
    'cleanup' => 'always',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Hebrewcalendarhelper_Yahrzeits',
        'label' => E::ts('Yahrzeits'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'id',
            'sort_name',
            'email_primary.email',
            'yahrzeit.deceased_contact_id',
            'yahrzeit.deceased_name',
            'yahrzeit.relationship_name_formatted',
            'yahrzeit.yahrzeit_date',
            'yahrzeit.yahrzeit_hebrew_date_format_english',
            'yahrzeit.yahrzeit_type:label',
            'yahrzeit.hebrew_deceased_date',
            'yahrzeit.yahrzeit_shabbat_morning_before',
            'yahrzeit.shabbat_before_parashat',
            'yahrzeit.plaque_location',
          ],
          'orderBy' => [],
          'where' => [
            ['is_deleted', '=', FALSE],
          ],
          'groupBy' => [],
          'join' => [
            [
              'Yahrzeit AS yahrzeit',
              'INNER',
              ['id', '=', 'yahrzeit.mourner_contact_id'],
              // Each yahrzeit has a Hebrew-date and an English-date row; keep the one the
              // mourner observes.
              ['yahrzeit.yahrzeit_type', '=', 'yahrzeit.mourner_observance_preference'],
            ],
          ],
          'having' => [],
        ],
        'description' => E::ts('Mourners and the yahrzeits they observe. Calculated by the "Call AllHebrewDates.Calculate API" scheduled job.'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Hebrewcalendarhelper_Yahrzeits_SearchDisplay_Table',
    'entity' => 'SearchDisplay',
    'cleanup' => 'always',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Hebrewcalendarhelper_Yahrzeits_Table',
        'label' => E::ts('Yahrzeits'),
        'saved_search_id.name' => 'Hebrewcalendarhelper_Yahrzeits',
        'type' => 'table',
        'settings' => [
          'actions' => TRUE,
          'limit' => 50,
          'classes' => ['table', 'table-striped'],
          'pager' => ['show_count' => TRUE, 'expose_limit' => TRUE],
          'placeholder' => 5,
          'sort' => [
            ['yahrzeit.yahrzeit_date', 'ASC'],
            ['sort_name', 'ASC'],
          ],
          'columns' => [
            $column('yahrzeit.yahrzeit_date', E::ts('Yahrzeit (evening)'), ['format' => 'dateformatFull']),
            $column('yahrzeit.yahrzeit_hebrew_date_format_english', E::ts('Hebrew Date')),
            $column('yahrzeit.deceased_name', E::ts('In Memory Of'), [
              'link' => ['path' => 'civicrm/contact/view?reset=1&cid=[yahrzeit.deceased_contact_id]', 'target' => ''],
            ]),
            $column('yahrzeit.relationship_name_formatted', E::ts('Relationship')),
            $column('sort_name', E::ts('Mourner'), [
              'link' => ['entity' => 'Contact', 'action' => 'view', 'join' => '', 'target' => ''],
            ]),
            $column('email_primary.email', E::ts('Email')),
            $column('yahrzeit.yahrzeit_type:label', E::ts('Observes')),
            $column('yahrzeit.hebrew_deceased_date', E::ts('Hebrew Date of Death')),
            $column('yahrzeit.yahrzeit_shabbat_morning_before', E::ts('Shabbat Before'), ['format' => 'dateformatFull']),
            $column('yahrzeit.shabbat_before_parashat', E::ts('Parashat')),
            $column('yahrzeit.plaque_location', E::ts('Plaque')),
          ],
        ],
      ],
      'match' => ['saved_search_id', 'name'],
    ],
  ],
];
