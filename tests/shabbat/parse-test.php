<?php

/**
 * Checks Calendar's parsing and the token lines against saved Hebcal responses for
 * Richmond, VA (18 minutes, nightfall, Sephardic) and one for Jerusalem. Needs no CiviCRM:
 *
 *   php tests/shabbat/parse-test.php
 */

// Just enough of CiviCRM for Calendar and Tokens.
namespace {
  function ts($text, $params = []) {
    foreach ($params as $k => $v) {
      $text = is_int($k) ? str_replace("%$k", $v, $text) : $text;
    }
    return $text;
  }
  class CRM_Hebrewcalendarhelper_ExtensionUtil {
    public static function ts($text, $params = []) {
      return ts($text, $params);
    }
  }
  class Civi {
    public static array $settings = [];
    public static function settings() {
      return new class {
        public function get($key) {
          return \Civi::$settings[$key] ?? NULL;
        }
      };
    }
  }
}

namespace Symfony\Component\EventDispatcher {
  if (!interface_exists(EventSubscriberInterface::class)) {
    interface EventSubscriberInterface {}
  }
}

namespace {
  require_once dirname(__DIR__, 2) . '/Civi/Hebrewcalendarhelper/Shabbat/Calendar.php';
  require_once dirname(__DIR__, 2) . '/Civi/Hebrewcalendarhelper/Shabbat/Tokens.php';

  use Civi\Hebrewcalendarhelper\Shabbat\Calendar;
  use Civi\Hebrewcalendarhelper\Shabbat\Tokens;

  $failures = 0;
  function check(string $label, $actual, $expected): void {
    global $failures;
    if ($actual === $expected) {
      echo "ok   $label\n";
      return;
    }
    $failures++;
    echo "FAIL $label:\n  expected " . var_export($expected, TRUE) . "\n  got      " . var_export($actual, TRUE) . "\n";
  }

  /**
   * Parses a fixture as of $now (America/New_York unless given) for the Shabbat on $friday.
   */
  function week(string $fixture, string $friday, string $now, array $overrides = [], string $tz = 'America/New_York'): array {
    $json = json_decode(file_get_contents(__DIR__ . "/fixtures/$fixture.json"), TRUE);
    return Calendar::parse($json, $friday, new DateTimeImmutable($now, new DateTimeZone($tz)), $overrides);
  }

  function summary(array $week): array {
    return [
      $week['candles']?->format('D Y-m-d H:i'),
      $week['ends']?->format('D Y-m-d H:i'),
      $week['parsha'],
    ];
  }

  function schedule(array $week): string {
    return Tokens::values($week)['schedule'];
  }

  Civi::$settings = ['hebrewcalendarhelper_shabbat_date_format' => 'l, M j', 'hebrewcalendarhelper_shabbat_time_format' => 'g:i a'];

  // A regular week, sent Monday: matches the weekly email.
  $regular = week('regular-2026-10-05', '2026-10-09', '2026-10-05 10:00', ['bereshit' => 'Bereishit']);
  check('regular: Shabbat', summary($regular), ['Fri 2026-10-09 18:22', 'Sat 2026-10-10 19:18', 'Bereishit']);
  check('regular: no holiday', $regular['holiday'], NULL);
  check('regular: schedule', schedule($regular), implode("\n", [
    'Shabbat candle lighting: Friday, Oct 9, 6:22 pm',
    'Shabbat ends: Saturday, Oct 10, 7:18 pm',
    'Torah portion: Bereishit',
  ]));
  $values = Tokens::values($regular);
  check('regular: numbered tokens', [$values['time1'], $values['time2_label'], $values['time2_date'], $values['time2_time'], $values['time3']], [
    'Shabbat candle lighting: Friday, Oct 9, 6:22 pm', 'Shabbat ends', 'Saturday, Oct 10', '7:18 pm', '',
  ]);

  // Pesach 2027: Yom Tov Thursday and Friday, then Shabbat Chol HaMoed.
  $pesach = week('pesach-2027-04-19', '2027-04-23', '2027-04-19 10:00');
  check('Pesach: Shabbat', summary($pesach), ['Fri 2027-04-23 19:34', 'Sat 2027-04-24 20:35', 'Shabbat Chol HaMoed Pesach']);
  check('Pesach: holiday', $pesach['holiday'], 'Pesach');
  check('Pesach: schedule', schedule($pesach), implode("\n", [
    'Pesach candle lighting: Wednesday, Apr 21, 7:32 pm',
    'Pesach candle lighting: Thursday, Apr 22, after 8:33 pm',
    'Shabbat candle lighting: Friday, Apr 23, 7:34 pm',
    'Shabbat ends: Saturday, Apr 24, 8:35 pm',
    'Torah portion: Shabbat Chol HaMoed Pesach',
  ]));
  check('Pesach: sent Thursday afternoon, Wednesday is gone', schedule(week('pesach-2027-04-19', '2027-04-23', '2027-04-22 15:00')), implode("\n", [
    'Pesach candle lighting: Thursday, Apr 22, after 8:33 pm',
    'Shabbat candle lighting: Friday, Apr 23, 7:34 pm',
    'Shabbat ends: Saturday, Apr 24, 8:35 pm',
    'Torah portion: Shabbat Chol HaMoed Pesach',
  ]));

  // End of Pesach 2027: Yom Tov Tuesday night to Thursday night, then a separate Shabbat.
  $pesachEnd = week('pesach-end-2027-04-26', '2027-04-30', '2027-04-26 10:00');
  check('end of Pesach: schedule', schedule($pesachEnd), implode("\n", [
    'Pesach candle lighting: Tuesday, Apr 27, 7:38 pm',
    'Pesach candle lighting: Wednesday, Apr 28, after 8:39 pm',
    'Pesach ends: Thursday, Apr 29, 8:40 pm',
    'Shabbat candle lighting: Friday, Apr 30, 7:40 pm',
    'Shabbat ends: Saturday, May 1, 8:42 pm',
    'Torah portion: Achrei Mot',
  ]));
  $values = Tokens::values($pesachEnd);
  check('end of Pesach: five numbered tokens', [$values['time3_label'], $values['time5']], ['Pesach ends', 'Shabbat ends: Saturday, May 1, 8:42 pm']);

  // Rosh Hashana 2027 starts Friday night: Yom Tov runs on to Sunday night.
  $rh = week('roshhashana-2027-09-27', '2027-10-01', '2027-09-27 10:00');
  check('Rosh Hashana: Shabbat', summary($rh), ['Fri 2027-10-01 18:35', 'Sat 2027-10-02 19:30', 'Rosh Hashana (1st day)']);
  check('Rosh Hashana: holiday', $rh['holiday'], 'Rosh Hashana');
  check('Rosh Hashana: schedule', schedule($rh), implode("\n", [
    'Shabbat & Rosh Hashana candle lighting: Friday, Oct 1, 6:35 pm',
    'Rosh Hashana candle lighting: Saturday, Oct 2, after 7:30 pm',
    'Rosh Hashana ends: Sunday, Oct 3, 7:29 pm',
    'Torah portion: Rosh Hashana (1st day)',
  ]));
  check('Rosh Hashana: stays on this week until Sunday night', $rh['until']->format('D H:i'), 'Sun 19:29');

  // Shmini Atzeret 2026 is on Shabbat, Simchat Torah on Sunday.
  check('Shmini Atzeret: schedule', schedule(week('shminiatzeret-2026-09-28', '2026-10-02', '2026-09-28 10:00')), implode("\n", [
    'Shabbat & Shmini Atzeret candle lighting: Friday, Oct 2, 6:33 pm',
    'Simchat Torah candle lighting: Saturday, Oct 3, after 7:29 pm',
    'Simchat Torah ends: Sunday, Oct 4, 7:27 pm',
    'Torah portion: Shmini Atzeret',
  ]));

  // Jerusalem: one day of Yom Tov.
  check('Jerusalem Pesach: schedule', schedule(week('jerusalem-pesach-2027-04-19', '2027-04-23', '2027-04-19 10:00', [], 'Asia/Jerusalem')), implode("\n", [
    'Pesach candle lighting: Wednesday, Apr 21, 6:31 pm',
    'Pesach ends: Thursday, Apr 22, 7:51 pm',
    'Shabbat candle lighting: Friday, Apr 23, 6:33 pm',
    'Shabbat ends: Saturday, Apr 24, 7:52 pm',
    'Torah portion: Shabbat Chol HaMoed Pesach',
  ]));

  // The parsha link: only in the HTML block, never in plain text or {shabbat.parsha}.
  Civi::$settings['hebrewcalendarhelper_shabbat_parsha_link'] = 'https://www.chabad.org/parsha';
  check('parsha link: HTML lines', str_contains(Tokens::scheduleHtml($regular), '<strong>Torah portion:</strong> <a href="https://www.chabad.org/parsha">Bereishit</a></div>'), TRUE);
  Civi::$settings['hebrewcalendarhelper_shabbat_schedule_layout'] = 'table';
  check('parsha link: HTML table', str_contains(Tokens::scheduleHtml($regular), '<a href="https://www.chabad.org/parsha">Bereishit</a></td>'), TRUE);
  unset(Civi::$settings['hebrewcalendarhelper_shabbat_schedule_layout']);
  check('parsha link: not in text', [str_contains(Tokens::values($regular)['schedule'], 'http'), Tokens::values($regular)['parsha']], [FALSE, 'Bereishit']);
  check('parsha link: dates not linked', substr_count(Tokens::scheduleHtml($pesach), '<a '), 1);
  unset(Civi::$settings['hebrewcalendarhelper_shabbat_parsha_link']);
  check('parsha link: none when blank', str_contains(Tokens::scheduleHtml($regular), '<a '), FALSE);

  check('readable holiday portions', array_map([Calendar::class, 'readableHoliday'], [
    'Rosh Hashana 5787', 'Rosh Hashana II', 'Sukkot I', 'Sukkos I', 'Sukkot III (CH’’M)', "Sukkos IV (CH''M)",
    'Shmini Atzeret', 'Simchat Torah', 'Yom Kippur', 'Pesach VII', 'Pesach VIII', 'Shavuot II', 'Shavuos II', 'פסח ג׳ (חוה״מ)', NULL,
  ]), [
    'Rosh Hashana (1st day)', 'Rosh Hashana (2nd day)', 'Sukkot (1st day)', 'Sukkos (1st day)', 'Shabbat Chol HaMoed Sukkot', 'Shabbat Chol HaMoed Sukkos',
    'Shmini Atzeret', 'Simchat Torah', 'Yom Kippur', 'Pesach (7th day)', 'Pesach (8th day)', 'Shavuot (2nd day)', 'Shavuos (2nd day)', 'פסח ג׳ (חוה״מ)', NULL,
  ]);
  check('custom spelling overrides a readable name', week('pesach-2027-04-19', '2027-04-23', '2027-04-19 10:00', ['Shabbat Chol HaMoed Pesach' => 'Shabbos Chol HaMoed Pesach'])['parsha'], 'Shabbos Chol HaMoed Pesach');

  check('holiday names', array_map([Calendar::class, 'holidayName'], ['Pesach I', 'Rosh Hashana 5788', 'Sukkot VII (Hoshana Raba)', 'Shmini Atzeret']), ['Pesach', 'Rosh Hashana', 'Sukkot', 'Shmini Atzeret']);
  check('double portion respelled per part', Calendar::respell('Vayakhel-Pekudei', ['Pekudei' => 'Pekudey']), 'Vayakhel-Pekudey');
  check('overrides parsed', Calendar::parseOverrides("Bereshit = Bereishit\n\nbad line\nNoach=Noach "), ['Bereshit' => 'Bereishit', 'Noach' => 'Noach']);

  $tz = new DateTimeZone('America/New_York');
  foreach ([
    '2026-10-04 12:00' => '2026-10-09', // Sunday
    '2026-10-08 23:00' => '2026-10-09', // Thursday
    '2026-10-09 20:00' => '2026-10-09', // Friday night
    '2026-10-10 23:00' => '2026-10-09', // Saturday night; current() rolls on after havdalah
  ] as $now => $friday) {
    check("Friday for $now", Calendar::fridayOf(new DateTimeImmutable($now, $tz))->format('Y-m-d'), $friday);
  }

  echo $failures ? "\n$failures failed\n" : "\nall passed\n";
  exit($failures ? 1 : 0);
}
