<?php

namespace Civi\Hebrewcalendarhelper;

use CRM_Hebrewcalendarhelper_ExtensionUtil as E;

/**
 * The Yahrzeit table (entity Yahrzeit): option lists, and the staging copy the scheduled job
 * fills so the live table is never empty while it runs.
 */
class Yahrzeits {

  public const TABLE = 'civicrm_fountaintribe_yahrzeits_temp';

  /**
   * Values of yahrzeit_type and mourner_observance_preference.
   */
  public static function calendarTypes(): array {
    return [
      '0' => E::ts('Hebrew date'),
      '1' => E::ts('English date'),
    ];
  }

  /**
   * Hebrew months as PHP's calendar extension numbers them (7 is Adar in a regular year).
   */
  public static function hebrewMonths(): array {
    return [
      1 => E::ts('Tishrei'),
      2 => E::ts('Cheshvan'),
      3 => E::ts('Kislev'),
      4 => E::ts('Tevet'),
      5 => E::ts("Sh'vat"),
      6 => E::ts('Adar I'),
      7 => E::ts('Adar / Adar II'),
      8 => E::ts('Nisan'),
      9 => E::ts('Iyyar'),
      10 => E::ts('Sivan'),
      11 => E::ts('Tamuz'),
      12 => E::ts('Av'),
      13 => E::ts('Elul'),
    ];
  }

  /**
   * Creates an empty copy of the table to fill.
   *
   * @return string
   *   Its name.
   */
  public static function createStaging(): string {
    $staging = self::TABLE . '_new';
    \CRM_Core_DAO::executeQuery("DROP TABLE IF EXISTS $staging");
    \CRM_Core_DAO::executeQuery("CREATE TABLE $staging LIKE " . self::TABLE);
    return $staging;
  }

  /**
   * Swaps the filled copy in, in one step, so readers see either the old rows or the new.
   */
  public static function swapInStaging(string $staging): void {
    $old = self::TABLE . '_old';
    \CRM_Core_DAO::executeQuery("DROP TABLE IF EXISTS $old");
    \CRM_Core_DAO::executeQuery('RENAME TABLE ' . self::TABLE . " TO $old, $staging TO " . self::TABLE);
    \CRM_Core_DAO::executeQuery("DROP TABLE $old");
  }

}
