<?php

namespace Civi\Hebrewcalendarhelper\Shabbat;

use Civi\Token\Event\TokenRegisterEvent;
use Civi\Token\Event\TokenValueEvent;
use CRM_Hebrewcalendarhelper_ExtensionUtil as E;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * The {shabbat.*} tokens. They are the same for every recipient, so Hebcal is asked once per
 * batch at most, and not at all for messages that do not use them.
 *
 * There are two ways to show a holiday week, when there are more candle lightings than one:
 * - {shabbat.schedule}: the whole week as one block, which grows and shrinks to fit;
 * - {shabbat.time1} .. {shabbat.time5}: one line each, also split into _label, _date and
 *   _time for separate styling. Unused ones are blank.
 *
 * Registered as a service in hebrewcalendarhelper_civicrm_container().
 */
class Tokens implements EventSubscriberInterface {

  private const ENTITY = 'shabbat';

  /**
   * How many numbered {shabbat.timeN} tokens there are. The longest weeks (a Yom Tov midweek,
   * then Shabbat) have five times.
   */
  public const SLOTS = 5;

  public static function getSubscribedEvents(): array {
    return [
      'civi.token.list' => 'listTokens',
      'civi.token.eval' => 'evaluateTokens',
    ];
  }

  public static function tokenLabels(): array {
    $labels = [
      'location' => E::ts('Shabbat: Location name'),
      'schedule' => E::ts('Shabbat: Full schedule, including Yom Tov (block)'),
      'holiday' => E::ts('Shabbat: Yom Tov this week (blank in other weeks)'),
      'candle_lighting' => E::ts('Shabbat: Candle lighting (date and time)'),
      'candle_lighting_date' => E::ts('Shabbat: Candle lighting date'),
      'candle_lighting_time' => E::ts('Shabbat: Candle lighting time'),
      'ends' => E::ts('Shabbat: Shabbat ends (date and time)'),
      'ends_date' => E::ts('Shabbat: Shabbat ends date'),
      'ends_time' => E::ts('Shabbat: Shabbat ends time'),
      'parsha' => E::ts('Shabbat: Torah portion'),
    ];
    for ($n = 1; $n <= self::SLOTS; $n++) {
      $labels["time$n"] = E::ts('Shabbat: Time %1 (whole line)', [1 => $n]);
      $labels["time{$n}_label"] = E::ts('Shabbat: Time %1 label', [1 => $n]);
      $labels["time{$n}_date"] = E::ts('Shabbat: Time %1 date', [1 => $n]);
      $labels["time{$n}_time"] = E::ts('Shabbat: Time %1 time', [1 => $n]);
    }
    return $labels;
  }

  public function listTokens(TokenRegisterEvent $e): void {
    foreach (self::tokenLabels() as $field => $label) {
      $e->entity(self::ENTITY)->register($field, $label);
    }
  }

  public function evaluateTokens(TokenValueEvent $e): void {
    $used = $e->getTokenProcessor()->getMessageTokens()[self::ENTITY] ?? [];
    if (!array_intersect($used, array_keys(self::tokenLabels()))) {
      return;
    }

    $week = Calendar::current();
    $values = self::values($week);
    $scheduleHtml = self::scheduleHtml($week);
    foreach ($e->getRows() as $row) {
      $row->format('text/plain');
      foreach ($values as $field => $value) {
        $row->tokens(self::ENTITY, $field, $value);
      }
      $row->format('text/html')->tokens(self::ENTITY, 'schedule', $scheduleHtml);
    }
  }

  /**
   * Plain-text token values for a week from Calendar::current(). Without one (no location set,
   * or Hebcal unreachable) the time tokens are blank rather than failing the mailing.
   */
  public static function values(?array $week): array {
    $settings = \Civi::settings();
    $values = [
      'location' => (string) ($settings->get('hebrewcalendarhelper_shabbat_location_name') ?: ($settings->get('hebrewcalendarhelper_shabbat_place')['city'] ?? '')),
      'parsha' => (string) ($week['parsha'] ?? ''),
      'holiday' => (string) ($week['holiday'] ?? ''),
    ];
    foreach (['candle_lighting' => 'candles', 'ends' => 'ends'] as $token => $key) {
      $time = $week[$key] ?? NULL;
      $values[$token . '_date'] = $time ? self::date($time) : '';
      $values[$token . '_time'] = $time ? self::time($time) : '';
      $values[$token] = $time ? self::date($time) . ', ' . self::time($time) : '';
    }

    $lines = self::lines($week);
    for ($n = 1; $n <= self::SLOTS; $n++) {
      $line = $lines[$n - 1] ?? NULL;
      $values["time$n"] = $line ? "{$line['label']}: {$line['date']}, {$line['time']}" : '';
      $values["time{$n}_label"] = $line['label'] ?? '';
      $values["time{$n}_date"] = $line['date'] ?? '';
      $values["time{$n}_time"] = $line['time'] ?? '';
    }
    $values['schedule'] = implode("\n", array_map(
      fn($line) => "{$line['label']}: {$line['date']}" . ($line['time'] === '' ? '' : ", {$line['time']}"),
      self::lines($week, TRUE)
    ));
    return $values;
  }

  /**
   * The schedule as label/date/time strings, e.g. ['label' => 'Pesach candle lighting',
   * 'date' => 'Thursday, Apr 22', 'time' => 'after 8:33 pm'].
   *
   * @param bool $withParsha
   *   Add the Torah portion as a last line, as the schedule block does.
   */
  public static function lines(?array $week, bool $withParsha = FALSE): array {
    $lines = [];
    foreach ($week['schedule'] ?? [] as $entry) {
      $names = implode(' & ', $entry['names']);
      if ($entry['kind'] === 'ends') {
        $label = $names === ''
          ? E::ts('Yom Tov ends')
          : (count($entry['names']) > 1 ? E::ts('%1 end', [1 => $names]) : E::ts('%1 ends', [1 => $names]));
      }
      else {
        $label = $names === '' ? E::ts('Candle lighting') : E::ts('%1 candle lighting', [1 => $names]);
      }
      $lines[] = [
        'label' => $label,
        'date' => self::date($entry['time']),
        'time' => $entry['kind'] === 'candles_after' ? E::ts('after %1', [1 => self::time($entry['time'])]) : self::time($entry['time']),
      ];
    }
    if ($withParsha && !empty($week['parsha'])) {
      $lines[] = ['label' => E::ts('Torah portion'), 'date' => $week['parsha'], 'time' => ''];
    }
    return $lines;
  }

  /**
   * The schedule block for HTML mail. It sets no fonts or colours, so it takes on the
   * styling of whatever it is placed in.
   */
  public static function scheduleHtml(?array $week): string {
    $lines = self::lines($week, TRUE);
    if (!$lines) {
      return '';
    }
    $h = fn(string $text) => htmlspecialchars($text, ENT_QUOTES);

    if (\Civi::settings()->get('hebrewcalendarhelper_shabbat_schedule_layout') === 'table') {
      $rows = '';
      foreach ($lines as $line) {
        $rows .= '<tr><td style="padding:2px 16px 2px 0;font-weight:bold;vertical-align:top">' . $h($line['label']) . '</td>'
          . '<td style="padding:2px 16px 2px 0;vertical-align:top">' . $h($line['date']) . '</td>'
          . '<td style="padding:2px 0;vertical-align:top">' . $h($line['time']) . '</td></tr>';
      }
      return '<table class="shabbat-schedule" role="presentation" cellpadding="0" cellspacing="0" style="border-collapse:collapse">' . $rows . '</table>';
    }

    $html = '';
    foreach ($lines as $line) {
      $html .= '<div><strong>' . $h($line['label']) . ':</strong> ' . $h($line['date'])
        . ($line['time'] === '' ? '' : ', ' . $h($line['time'])) . '</div>';
    }
    return '<div class="shabbat-schedule">' . $html . '</div>';
  }

  private static function date(\DateTimeInterface $time): string {
    return $time->format(\Civi::settings()->get('hebrewcalendarhelper_shabbat_date_format') ?: 'l, M j');
  }

  private static function time(\DateTimeInterface $time): string {
    return $time->format(\Civi::settings()->get('hebrewcalendarhelper_shabbat_time_format') ?: 'g:i a');
  }

}
