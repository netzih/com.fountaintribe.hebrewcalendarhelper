<?php

namespace Civi\Hebrewcalendarhelper\Shabbat;

use CRM_Hebrewcalendarhelper_ExtensionUtil as E;

/**
 * Looks up this week's Shabbat and Yom Tov times for the configured location from the Hebcal
 * API (https://www.hebcal.com/home/195/jewish-calendar-rest-api) and caches them.
 *
 * "This week" runs from the moment of the call to the end of the coming Shabbat, extended
 * when Yom Tov runs on into Sunday or Monday. Mail tokens are evaluated when a message is
 * sent, so a mailing scheduled for Monday of Pesach week lists Wednesday's and Thursday's
 * candle lighting as well as Friday's, however early it was written. Once it is all over,
 * the next week is shown.
 */
class Calendar {

  private const CACHE_TTL = 86400;

  /**
   * @var array
   *   Per-request memo of Hebcal responses, keyed like the cache.
   */
  private static array $memo = [];

  /**
   * The week coming up or under way at $now (default: now).
   *
   * @return array|null
   *   See parse(), or NULL when the location is not configured or Hebcal could not be reached.
   */
  public static function current(?\DateTimeInterface $now = NULL): ?array {
    $params = self::locationParams();
    if ($params === NULL) {
      return NULL;
    }
    $now = \DateTimeImmutable::createFromInterface($now ?? new \DateTimeImmutable())
      ->setTimezone(new \DateTimeZone($params['tzid']));

    $friday = self::fridayOf($now);
    $week = self::week($params, $now, $friday);
    if ($week !== NULL && $week['until'] !== NULL && $now >= $week['until']) {
      $week = self::week($params, $now, $friday->modify('+7 days'));
    }
    return $week;
  }

  /**
   * The Friday that starts the Shabbat coming up or under way on $now's date: the same day on
   * Friday, the day before on Saturday, otherwise the next Friday.
   */
  public static function fridayOf(\DateTimeImmutable $now): \DateTimeImmutable {
    $day = $now->setTime(0, 0);
    $weekday = (int) $day->format('N');
    if ($weekday === 6) {
      return $day->modify('-1 day');
    }
    return $weekday === 5 ? $day : $day->modify('next friday');
  }

  /**
   * Picks this week's times out of a Hebcal response.
   *
   * - candles, ends, parsha: the Shabbat that starts on $friday. Shabbat ends at Saturday's
   *   havdalah or, when Yom Tov follows, at Saturday night's candle lighting. With no weekly
   *   portion (Yom Tov, Chol HaMoed) parsha is the day's holiday name.
   * - schedule: every candle lighting and end of Shabbat or Yom Tov from $now through the
   *   end of that Shabbat, or of the Yom Tov joined to it. Each entry is
   *   ['kind' => 'candles'|'candles_after'|'ends', 'names' => ['Pesach'], 'time' => DateTime].
   *   'candles_after' is a second-night lighting, after nightfall from an existing flame.
   * - holiday: names of the Yom Tov in the schedule, or NULL in an ordinary week.
   * - until: when the schedule is over, after which the next week is shown.
   *
   * @param array $response
   *   Decoded Hebcal JSON covering $now (or the Friday) through Monday after it.
   * @param string $friday
   *   Y-m-d.
   * @param \DateTimeInterface|null $now
   *   Times before this are left out of the schedule.
   * @param array $overrides
   *   Hebcal spelling => custom spelling.
   */
  public static function parse(array $response, string $friday, ?\DateTimeInterface $now = NULL, array $overrides = []): array {
    $saturday = (new \DateTimeImmutable($friday))->modify('+1 day')->format('Y-m-d');
    $week = ['candles' => NULL, 'ends' => NULL, 'parsha' => NULL, 'schedule' => [], 'holiday' => NULL, 'until' => NULL];
    $saturdayHoliday = NULL;

    // Yom Tov names by date, for labelling what each candle lighting starts and each end ends.
    $yomTov = [];
    $times = [];
    foreach ($response['items'] ?? [] as $item) {
      $date = (string) ($item['date'] ?? '');
      $day = substr($date, 0, 10);
      $category = $item['category'] ?? '';
      if ($category === 'holiday' && !empty($item['yomtov'])) {
        $yomTov[$day][] = self::respell(self::holidayName((string) $item['title']), $overrides);
      }
      if ($category === 'holiday' && $day === $saturday && $saturdayHoliday === NULL) {
        $saturdayHoliday = (string) $item['title'];
      }
      if ($category === 'parashat' && $day === $saturday) {
        // "Parashat Bereshit", "Parshas Noach", "פָּרָשַׁת נֹחַ": drop the first word.
        $week['parsha'] = preg_replace('/^\S+\s+/u', '', (string) $item['title']);
      }
      if (in_array($category, ['candles', 'havdalah'], TRUE) && strlen($date) > 10) {
        $times[] = [$category, $day, new \DateTimeImmutable($date)];
      }
    }
    $week['parsha'] = self::respell($week['parsha'] ?? $saturdayHoliday, $overrides);

    foreach ($times as [$category, $day, $time]) {
      if ($category === 'candles' && $day === $friday && $week['candles'] === NULL) {
        $week['candles'] = $time;
      }
      elseif ($day === $saturday && $week['ends'] === NULL) {
        $week['ends'] = $time;
      }
    }

    $holidays = [];
    foreach ($times as [$category, $day, $time]) {
      $weekday = (int) $time->format('N');
      if ($category === 'candles') {
        // Lit for whatever starts that evening.
        $next = $time->modify('+1 day')->format('Y-m-d');
        $names = $yomTov[$next] ?? [];
        $kind = ($weekday === 6 || (isset($yomTov[$day]) && $weekday !== 5)) ? 'candles_after' : 'candles';
      }
      else {
        $names = $yomTov[$day] ?? [];
        $kind = 'ends';
      }
      if ($weekday === ($category === 'candles' ? 5 : 6)) {
        array_unshift($names, E::ts('Shabbat'));
      }

      if ($now === NULL || $time >= $now) {
        $week['schedule'][] = ['kind' => $kind, 'names' => array_values(array_unique($names)), 'time' => $time];
        $holidays = array_merge($holidays, $yomTov[$category === 'candles' ? $time->modify('+1 day')->format('Y-m-d') : $day] ?? []);
      }
      if ($kind === 'ends' && $day >= $saturday) {
        $week['until'] = $time;
        break;
      }
    }
    $week['holiday'] = $holidays ? implode(' & ', array_unique($holidays)) : NULL;
    return $week;
  }

  /**
   * "Pesach I" => "Pesach", "Rosh Hashana 5788" => "Rosh Hashana".
   */
  public static function holidayName(string $title): string {
    $name = preg_replace('/\s*\([^)]*\)$/u', '', $title);
    return preg_replace('/\s+(\d{4}|[IVX]+)$/u', '', $name);
  }

  /**
   * Applies custom spellings to a whole name, or else to each part of a double portion such
   * as "Vayakhel-Pekudei".
   */
  public static function respell(?string $name, array $overrides): ?string {
    if ($name === NULL || !$overrides) {
      return $name;
    }
    $lower = array_change_key_case($overrides, CASE_LOWER);
    if (isset($lower[mb_strtolower($name)])) {
      return $lower[mb_strtolower($name)];
    }
    return implode('-', array_map(
      fn(string $part) => $lower[mb_strtolower($part)] ?? $part,
      explode('-', $name)
    ));
  }

  /**
   * Parses the "Hebcal spelling = your spelling" lines of the custom spellings setting.
   */
  public static function parseOverrides(?string $text): array {
    $overrides = [];
    foreach (preg_split('/\R/', (string) $text) as $line) {
      [$from, $to] = array_map('trim', explode('=', $line, 2)) + [1 => ''];
      if ($from !== '' && $to !== '') {
        $overrides[$from] = $to;
      }
    }
    return $overrides;
  }

  /**
   * Hebcal query parameters for the configured location, or NULL if it is incomplete.
   */
  public static function locationParams(): ?array {
    $settings = \Civi::settings();
    $place = $settings->get('hebrewcalendarhelper_shabbat_place');
    if (empty($place['id']) || empty($place['tzid'])) {
      return NULL;
    }

    $params = [
      'cfg' => 'json',
      $place['geo'] === 'zip' ? 'zip' : 'geonameid' => $place['id'],
      'tzid' => $place['tzid'],
      'b' => (int) ($settings->get('hebrewcalendarhelper_shabbat_candle_minutes') ?? 18),
      'lg' => $settings->get('hebrewcalendarhelper_shabbat_language') ?: 's',
      'leyning' => 'off',
    ];
    $havdalah = $settings->get('hebrewcalendarhelper_shabbat_havdalah') ?: 'tzeit';
    if ($havdalah === 'tzeit') {
      $params['M'] = 'on';
    }
    else {
      $params['m'] = (int) $havdalah;
    }
    return $params;
  }

  private static function week(array $params, \DateTimeImmutable $now, \DateTimeImmutable $friday): ?array {
    $query = $params + [
      'v' => 1,
      'maj' => 'on',
      's' => 'on',
      'c' => 'on',
      'start' => min($now->format('Y-m-d'), $friday->format('Y-m-d')),
      // Yom Tov after Shabbat can run to Monday night.
      'end' => $friday->modify('+3 days')->format('Y-m-d'),
    ];
    unset($query['tzid']);
    $key = 'hebrewcalendarhelper_shabbat_' . md5(serialize($query));

    if (!array_key_exists($key, self::$memo)) {
      $cache = \Civi::cache('long');
      $response = $cache->get($key);
      if (!is_array($response)) {
        $response = self::fetch($query);
        if ($response !== NULL) {
          $cache->set($key, $response, self::CACHE_TTL);
        }
      }
      // A failed lookup is memoized too, so a large mailing batch asks Hebcal only once.
      self::$memo[$key] = $response;
    }
    if (self::$memo[$key] === NULL) {
      return NULL;
    }
    $overrides = self::parseOverrides(\Civi::settings()->get('hebrewcalendarhelper_shabbat_name_overrides'));
    return self::parse(self::$memo[$key], $friday->format('Y-m-d'), $now, $overrides);
  }

  private static function fetch(array $params): ?array {
    $data = \Civi\Hebrewcalendarhelper\Hebcal::get('hebcal', $params);
    return isset($data['items']) ? $data : NULL;
  }

  public static function havdalahOptions(): array {
    return [
      'tzeit' => E::ts('Nightfall (sun 8.5° below the horizon)'),
      '42' => E::ts('42 minutes after sunset'),
      '50' => E::ts('50 minutes after sunset'),
      '72' => E::ts('72 minutes after sunset'),
    ];
  }

  public static function languageOptions(): array {
    return [
      's' => E::ts('Sephardic transliteration'),
      'a' => E::ts('Ashkenazi transliteration'),
      'h' => E::ts('Hebrew'),
    ];
  }

  public static function layoutOptions(): array {
    return [
      'lines' => E::ts('Lines: "Shabbat candle lighting: Friday, Oct 9, 6:22 pm"'),
      'table' => E::ts('Table: label, date and time in columns'),
    ];
  }

  /**
   * Date formats offered on the settings page, labelled with an example.
   */
  public static function dateFormats(): array {
    return self::examples(['l, M j', 'l, F j', 'l, F jS', 'D, M j', 'l n/j', 'n/j', 'l, j F', 'D j M', 'l']);
  }

  /**
   * Time formats offered on the settings page, labelled with an example.
   */
  public static function timeFormats(): array {
    return self::examples(['g:i a', 'g:i A', 'g:ia', 'H:i']);
  }

  /**
   * Labels each PHP date() format with how candle lighting on Friday, October 9, 2026 looks.
   */
  private static function examples(array $formats): array {
    $sample = new \DateTimeImmutable('2026-10-09 18:22');
    $options = [];
    foreach ($formats as $format) {
      $options[$format] = $sample->format($format);
    }
    return $options;
  }

  /**
   * Clears this request's memo; for tests.
   */
  public static function reset(): void {
    self::$memo = [];
  }

}
