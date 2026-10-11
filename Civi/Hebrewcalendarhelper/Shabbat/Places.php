<?php

namespace Civi\Hebrewcalendarhelper\Shabbat;

use CRM_Hebrewcalendarhelper_ExtensionUtil as E;

/**
 * Turns what an admin types for the location ("Richmond, Virginia", "23229") into a place
 * Hebcal knows, using Hebcal's place search (https://www.hebcal.com/complete). Hebcal only
 * matches name prefixes, so a misspelling finds nothing; then shorter prefixes are tried and
 * the results ranked by similarity, to offer "did you mean" suggestions.
 */
class Places {

  /**
   * @var array
   *   Per-request memo of resolve() results, keyed by lowercased query.
   */
  private static array $memo = [];

  /**
   * Hebcal place labels for the settings page autocomplete: those starting with $query, or
   * close spellings when there are none.
   *
   * @return string[]
   */
  public static function search(string $query): array {
    $results = self::complete($query);
    if ($results === []) {
      return self::similar($query);
    }
    return array_column($results ?? [], 'value');
  }

  /**
   * Finds the one place $query means.
   *
   * @return array
   *   ['place' => array|null, 'error' => string|null]. A place is ['geo' => 'geoname'|'zip',
   *   'id' => geonameid or ZIP, 'title' => "Richmond, Virginia, USA", 'city' => "Richmond",
   *   'tzid' => "America/New_York"]. When there is no single match, error says why and lists
   *   suggestions.
   */
  public static function resolve(string $query): array {
    $query = trim(preg_replace('/\s+/', ' ', $query));
    $key = mb_strtolower($query);
    if (isset(self::$memo[$key])) {
      return self::$memo[$key];
    }

    $results = self::complete($query);
    if ($results === NULL) {
      return ['place' => NULL, 'error' => E::ts('Hebcal.com could not be reached to look up the location. Try again in a minute.')];
    }

    $match = NULL;
    foreach ($results as $result) {
      if (mb_strtolower((string) $result['value']) === $key || (string) $result['id'] === $query) {
        $match = $result;
        break;
      }
    }
    if ($match === NULL && count($results) === 1) {
      $match = $results[0];
    }

    if ($match === NULL) {
      if ($results) {
        $error = E::ts('Several places match "%1". Which one do you mean: %2?', [
          1 => $query,
          2 => implode('; ', array_slice(array_column($results, 'value'), 0, 6)),
        ]);
      }
      elseif ($suggestions = self::similar($query)) {
        $error = E::ts('Hebcal has no place called "%1". Did you mean: %2?', [
          1 => $query,
          2 => implode('; ', array_slice($suggestions, 0, 6)),
        ]);
      }
      else {
        $error = E::ts('Hebcal has no place called "%1". Try the city name, or a US ZIP code.', [1 => $query]);
      }
      return self::$memo[$key] = ['place' => NULL, 'error' => $error];
    }

    $place = self::confirm($match);
    if ($place === NULL) {
      return ['place' => NULL, 'error' => E::ts('Hebcal.com found "%1" but returned no Shabbat times for it. Try a nearby city.', [1 => $match['value']])];
    }
    return self::$memo[$key] = ['place' => $place, 'error' => NULL];
  }

  /**
   * Asks Hebcal for Shabbat times at the matched place, which proves it works and gives its
   * time zone.
   */
  private static function confirm(array $match): ?array {
    $geo = $match['geo'] === 'zip' ? 'zip' : 'geoname';
    $data = \Civi\Hebrewcalendarhelper\Hebcal::get('shabbat', ['cfg' => 'json', $geo === 'zip' ? 'zip' : 'geonameid' => $match['id']]);
    $location = $data['location'] ?? NULL;
    if (empty($location['tzid']) || empty($data['items'])) {
      return NULL;
    }
    return [
      'geo' => $geo,
      'id' => (string) $match['id'],
      'title' => (string) $match['value'],
      'city' => (string) ($location['city'] ?? $match['asciiname'] ?? $match['value']),
      'tzid' => (string) $location['tzid'],
    ];
  }

  /**
   * Suggestions for a query Hebcal finds nothing for: retry with shorter prefixes, then rank
   * by how close each place name is to what was typed.
   *
   * @return string[]
   */
  private static function similar(string $query): array {
    $name = mb_strtolower(trim(explode(',', $query)[0]));
    for ($length = mb_strlen($name) - 1; $length >= 3; $length--) {
      $results = self::complete(mb_substr($name, 0, $length));
      if ($results) {
        usort($results, fn($a, $b) => self::distance($name, $a) <=> self::distance($name, $b));
        return array_column($results, 'value');
      }
    }
    return [];
  }

  private static function distance(string $name, array $result): int {
    return levenshtein($name, mb_strtolower((string) ($result['asciiname'] ?? $result['value'])));
  }

  /**
   * @return array|null
   *   Hebcal's matches (an empty array for none), or NULL if Hebcal could not be reached.
   */
  private static function complete(string $query): ?array {
    if (mb_strlen($query) < 2) {
      return [];
    }
    $data = \Civi\Hebrewcalendarhelper\Hebcal::get('complete', ['q' => $query]);
    if ($data === NULL) {
      return NULL;
    }
    // No matches comes back as {}.
    return array_values(array_filter($data, fn($item) => is_array($item) && isset($item['id'], $item['value'])));
  }

  /**
   * validate_callback for hebrewcalendarhelper_shabbat_location: a blank location is allowed (the status
   * check flags it), anything else must resolve to one place.
   */
  public static function validateSetting(&$value, array $meta): bool {
    if (trim((string) $value) === '') {
      return TRUE;
    }
    $error = self::resolve((string) $value)['error'];
    if ($error !== NULL) {
      throw new \CRM_Core_Exception($error);
    }
    return TRUE;
  }

  /**
   * post_change for hebrewcalendarhelper_shabbat_location: stores the place it resolves to. (Not on_change:
   * that runs in the middle of saving the location, and saving another setting from there
   * loses the location.) The settings page then swaps what was typed for Hebcal's label, see
   * hebrewcalendarhelper_civicrm_postProcess().
   */
  public static function onLocationChange($oldValue, $newValue, array $meta, $domainId): void {
    $place = trim((string) $newValue) === '' ? NULL : self::resolve((string) $newValue)['place'];
    \Civi::settings($domainId)->set('hebrewcalendarhelper_shabbat_place', $place);
  }

}
