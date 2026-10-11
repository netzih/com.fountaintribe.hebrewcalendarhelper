<?php

namespace Civi\Hebrewcalendarhelper;

use Civi\Token\Event\TokenRegisterEvent;
use Civi\Token\Event\TokenValueEvent;
use CRM_Hebrewcalendarhelper_ExtensionUtil as E;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

require_once __DIR__ . '/../../utils/HebrewCalendar.php';

/**
 * The {hebrewcalendar.*} and {yahrzeit.*} tokens.
 *
 * Yahrzeit tokens are named {yahrzeit.PART___WINDOW}, for example
 * {yahrzeit.deceased_name___day_7}: for the recipient (the mourner), the PART of every yahrzeit
 * they observe whose date falls in the WINDOW, joined with ", ". One message can mix windows.
 * The values come from the table rebuilt by AllHebrewDates.calculate, with one query per
 * window for the whole batch of recipients.
 *
 * Registered as a service in hebrewcalendarhelper_civicrm_container().
 */
class YahrzeitTokens implements EventSubscriberInterface {

  public static function getSubscribedEvents(): array {
    return [
      'civi.token.list' => 'listTokens',
      'civi.token.eval' => 'evaluateTokens',
    ];
  }

  /**
   * @return array
   *   PART => label.
   */
  public static function parts(): array {
    return [
      'deceased_name' => E::ts('Name of Deceased'),
      'deceased_hebrew_name' => E::ts('Hebrew Name of Deceased'),
      'deceased_parents_hebrew_names' => E::ts("Hebrew Names of Deceased's Parents"),
      'english_date' => E::ts('English Yahrzeit Date (evening)'),
      'morning_format_english' => E::ts('English Yahrzeit Date (morning)'),
      'hebrew_date' => E::ts('Hebrew Yahrzeit Date'),
      'hebrew_date_hebrew' => E::ts('Hebrew Yahrzeit Date (Hebrew letters)'),
      'dec_death_english_date' => E::ts('English Date of Death'),
      'dec_death_hebrew_date' => E::ts('Hebrew Date of Death'),
      'relationship_name' => E::ts('Relationship of Deceased to Mourner'),
      'erev_shabbat_before' => E::ts('Erev (evening) of the Shabbat before yahrzeit'),
      'parashat_shabbat_before' => E::ts('Parashat of the Shabbat before yahrzeit'),
      'shabbat_morning_before' => E::ts('Morning of the Shabbat before yahrzeit'),
      'erev_shabbat_after' => E::ts('Erev (evening) of the Shabbat after yahrzeit'),
      'parashat_shabbat_after' => E::ts('Parashat of the Shabbat after yahrzeit'),
      'shabbat_morning_after' => E::ts('Morning of the Shabbat after yahrzeit'),
    ];
  }

  /**
   * @return array
   *   WINDOW => label.
   */
  public static function windows(): array {
    return [
      'day_7' => E::ts('in exactly 7 days'),
      'day_10' => E::ts('in exactly 10 days'),
      'day_14' => E::ts('in exactly 14 days'),
      'day_30' => E::ts('in exactly 30 days'),
      'week_cur' => E::ts('during current week'),
      'week_next' => E::ts('during next week'),
      'week_2' => E::ts('2 weeks from now'),
      'week_3' => E::ts('3 weeks from now'),
      'week_4' => E::ts('4 weeks from now'),
      'month_cur' => E::ts('during current month'),
      'month_next' => E::ts('during next month'),
      'month_2' => E::ts('2 months from now'),
      'month_3' => E::ts('3 months from now'),
      'month_4' => E::ts('4 months from now'),
    ];
  }

  public function listTokens(TokenRegisterEvent $e): void {
    if (!in_array('contactId', $e->getTokenProcessor()->context['schema'] ?? [], TRUE)) {
      return;
    }
    $e->entity('hebrewcalendar')
      ->register('today___hebrew_trans', E::ts('Today (Hebrew transliterated)'))
      ->register('today___hebrew', E::ts('Today (Hebrew)'));
    foreach (self::windows() as $window => $windowLabel) {
      foreach (self::parts() as $part => $partLabel) {
        $e->entity('yahrzeit')->register("{$part}___{$window}", "$partLabel $windowLabel :: " . E::ts('Yahrzeits for this Mourner'));
      }
    }
  }

  public function evaluateTokens(TokenValueEvent $e): void {
    $used = $e->getTokenProcessor()->getMessageTokens();

    if (!empty($used['hebrewcalendar']) && function_exists('gregoriantojd')) {
      $values = self::todayValues(self::today());
      foreach ($e->getRows() as $row) {
        $row->format('text/plain');
        foreach ($values as $field => $value) {
          $row->tokens('hebrewcalendar', $field, $value);
        }
      }
    }

    // Group the yahrzeit tokens in use by window.
    $wanted = [];
    foreach ($used['yahrzeit'] ?? [] as $field) {
      [$part, $window] = array_pad(explode('___', $field, 2), 2, '');
      if (isset(self::parts()[$part]) && self::range($window, self::today()) !== NULL) {
        $wanted[$window][] = $part;
      }
    }
    if (!$wanted) {
      return;
    }

    $rowsByContact = [];
    foreach ($e->getRows() as $row) {
      if (!empty($row->context['contactId'])) {
        $rowsByContact[(int) $row->context['contactId']][] = $row;
      }
    }
    if (!$rowsByContact) {
      return;
    }

    foreach ($wanted as $window => $parts) {
      $values = self::values(array_keys($rowsByContact), self::range($window, self::today()));
      foreach ($rowsByContact as $contactId => $rows) {
        foreach ($rows as $row) {
          $row->format('text/plain');
          foreach ($parts as $part) {
            $row->tokens('yahrzeit', "{$part}___{$window}", implode(', ', $values[$contactId][$part] ?? []));
          }
        }
      }
    }
  }

  /**
   * Today in the site's time zone (a cron job may run in UTC).
   */
  public static function today(): \DateTimeImmutable {
    $tz = \CRM_Core_Config::singleton()->userSystem->getTimeZoneString() ?: date_default_timezone_get();
    return new \DateTimeImmutable('today', new \DateTimeZone($tz));
  }

  /**
   * The first and last date of a WINDOW, counted from $today. Weeks start on Sunday.
   *
   * @return \DateTimeImmutable[]|null
   *   [first, last], or NULL for an unknown window.
   */
  public static function range(string $window, \DateTimeImmutable $today): ?array {
    if (!isset(self::windows()[$window])) {
      return NULL;
    }
    [$unit, $n] = explode('_', $window);
    $n = ['cur' => 0, 'next' => 1][$n] ?? (int) $n;
    switch ($unit) {
      case 'day':
        $day = $today->modify("+$n days");
        return [$day, $day];

      case 'week':
        $sunday = $today->modify('-' . $today->format('w') . ' days')->modify('+' . (7 * $n) . ' days');
        return [$sunday, $sunday->modify('+6 days')];

      case 'month':
        $first = $today->modify('first day of this month')->modify("+$n months");
        return [$first, $first->modify('last day of this month')];
    }
    return NULL;
  }

  /**
   * Token values for the yahrzeits observed by $contactIds between the two dates.
   *
   * @return array
   *   [mourner contact ID => [PART => [value per deceased, in date order]]].
   */
  public static function values(array $contactIds, array $range): array {
    $table = \HebrewCalendar::YAHRZEIT_TEMP_TABLE_NAME;
    [$hebrewNameJoin, $hebrewNameSelect] = self::hebrewNameSql();
    $ids = implode(',', array_map('intval', $contactIds));
    $dao = \CRM_Core_DAO::executeQuery("
      SELECT y.mourner_contact_id, y.deceased_contact_id, d.display_name, d.deceased_date,
        y.yahrzeit_date, y.yahrzeit_date_morning, y.hebrew_deceased_date,
        y.yahrzeit_hebrew_date_format_english, y.yahrzeit_hebrew_date_format_hebrew,
        y.relationship_name_formatted,
        y.yahrzeit_erev_shabbat_before, y.yahrzeit_shabbat_morning_before,
        y.yahrzeit_erev_shabbat_after, y.yahrzeit_shabbat_morning_after,
        y.shabbat_before_parashat, y.shabbat_before_holiday,
        y.shabbat_after_parashat, y.shabbat_after_holiday
        $hebrewNameSelect
      FROM $table y
      JOIN civicrm_contact d ON d.id = y.deceased_contact_id
      $hebrewNameJoin
      WHERE y.yahrzeit_type = y.mourner_observance_preference
        AND y.mourner_contact_id IN ($ids)
        AND DATE(y.yahrzeit_date) BETWEEN %1 AND %2
      ORDER BY y.mourner_contact_id, y.yahrzeit_date, d.sort_name", [
        1 => [$range[0]->format('Y-m-d'), 'String'],
        2 => [$range[1]->format('Y-m-d'), 'String'],
      ]);

    $date = fn($value) => $value ? date(self::dateFormat(), strtotime($value)) : '';
    $values = $seen = [];
    while ($dao->fetch()) {
      $mourner = (int) $dao->mourner_contact_id;
      // One entry per deceased person.
      if (isset($seen[$mourner][$dao->deceased_contact_id])) {
        continue;
      }
      $seen[$mourner][$dao->deceased_contact_id] = TRUE;
      $row = [
        'deceased_name' => $dao->display_name,
        'deceased_hebrew_name' => $dao->hebrew_name ?? '',
        'deceased_parents_hebrew_names' => $dao->hebrew_names_of_parents ?? '',
        'english_date' => $date($dao->yahrzeit_date),
        'morning_format_english' => $date($dao->yahrzeit_date_morning),
        'hebrew_date' => $dao->yahrzeit_hebrew_date_format_english,
        'hebrew_date_hebrew' => $dao->yahrzeit_hebrew_date_format_hebrew,
        'dec_death_english_date' => $date($dao->deceased_date),
        'dec_death_hebrew_date' => $dao->hebrew_deceased_date,
        'relationship_name' => $dao->relationship_name_formatted ?: E::ts('loved one'),
        'erev_shabbat_before' => $date($dao->yahrzeit_erev_shabbat_before),
        // With no weekly portion (a holiday), the holiday's name.
        'parashat_shabbat_before' => $dao->shabbat_before_parashat ?: $dao->shabbat_before_holiday,
        'shabbat_morning_before' => $date($dao->yahrzeit_shabbat_morning_before),
        'erev_shabbat_after' => $date($dao->yahrzeit_erev_shabbat_after),
        'parashat_shabbat_after' => $dao->shabbat_after_parashat ?: $dao->shabbat_after_holiday,
        'shabbat_morning_after' => $date($dao->yahrzeit_shabbat_morning_after),
      ];
      foreach ($row as $part => $value) {
        $values[$mourner][$part][] = (string) $value;
      }
    }
    return $values;
  }

  /**
   * "Today" in transliterated Hebrew ("29 Tishrei 5787") and in Hebrew letters.
   */
  public static function todayValues(\DateTimeImmutable $today): array {
    $cal = new \HebrewCalendar();
    $y = $today->format('Y');
    $m = $today->format('n');
    $d = $today->format('j');
    $beforeSunset = '1';
    $trans = 'dd MM yy';
    $hebrew = 'hebrew';
    return [
      'today___hebrew_trans' => $cal->util_convert2hebrew_date($y, $m, $d, $beforeSunset, $trans),
      'today___hebrew' => $cal->util_convert2hebrew_date($y, $m, $d, $beforeSunset, $hebrew),
    ];
  }

  /**
   * "March 17, 2027" or "17 March 2027", following the site's date input format, as before.
   */
  private static function dateFormat(): string {
    return \CRM_Core_Config::singleton()->dateInputFormat === 'mm/dd/yy' ? 'F j, Y' : 'j F Y';
  }

  /**
   * SQL for the deceased's Hebrew name and parents' Hebrew names from the "Religious" custom
   * fields, if they exist.
   *
   * @return string[]
   *   [JOIN clause, SELECT columns starting with a comma].
   */
  private static function hebrewNameSql(): array {
    $fields = \Civi\Api4\CustomField::get(FALSE)
      ->addSelect('name', 'column_name', 'custom_group_id.table_name')
      ->addWhere('custom_group_id.name', '=', 'Religious')
      ->addWhere('name', 'IN', ['Hebrew_Name', 'Hebrew_Names_of_Parents'])
      ->execute()->indexBy('name');
    if (!count($fields)) {
      return ['', ''];
    }
    $table = $fields->first()['custom_group_id.table_name'];
    $select = '';
    foreach (['Hebrew_Name' => 'hebrew_name', 'Hebrew_Names_of_Parents' => 'hebrew_names_of_parents'] as $name => $alias) {
      if (isset($fields[$name])) {
        $select .= ", rel.`{$fields[$name]['column_name']}` AS $alias";
      }
    }
    return ["LEFT JOIN `$table` rel ON rel.entity_id = d.id", $select];
  }

}
