<?php
namespace Civi\Api4;

/**
 * Calculated yahrzeit dates, per deceased person and mourner (read-only: rebuilt by the
 * AllHebrewDates.calculate scheduled job).
 *
 * @searchable secondary
 * @searchFields deceased_name,mourner_name
 * @package Civi\Api4
 */
class Yahrzeit extends Generic\DAOEntity {

  public static function permissions() {
    return [
      'meta' => ['access CiviCRM'],
      'default' => ['administer CiviCRM'],
      'get' => ['access CiviCRM', 'view all contacts'],
    ];
  }

}
