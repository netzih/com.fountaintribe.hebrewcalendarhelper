<?php

namespace Civi\Hebrewcalendarhelper;

/**
 * The one way this extension calls the Hebcal.com APIs (https://www.hebcal.com/home/developer-apis):
 * with a timeout, optional caching, and failures logged instead of shown on the page.
 */
class Hebcal {

  private const BASE_URL = 'https://www.hebcal.com/';

  /**
   * @param string $path
   *   'hebcal', 'shabbat' or 'complete'.
   * @param array $query
   * @param int $cacheTtl
   *   Seconds to cache a successful response; 0 for no caching.
   *
   * @return array|null
   *   The decoded JSON (an empty array for "not found"), or NULL when Hebcal could not be
   *   reached or answered with an error.
   */
  public static function get(string $path, array $query, int $cacheTtl = 0): ?array {
    $key = 'hebrewcalendarhelper_hebcal_' . md5($path . '?' . http_build_query($query));
    if ($cacheTtl) {
      $cached = \Civi::cache('long')->get($key);
      if (is_array($cached)) {
        return $cached;
      }
    }

    try {
      $response = (new \GuzzleHttp\Client(['timeout' => 10, 'http_errors' => FALSE]))
        ->get(self::BASE_URL . $path, ['query' => $query]);
      // The place search answers "no matches" with a 404.
      if ($response->getStatusCode() === 404) {
        return [];
      }
      if ($response->getStatusCode() !== 200) {
        throw new \RuntimeException('HTTP ' . $response->getStatusCode());
      }
      $data = json_decode((string) $response->getBody(), TRUE);
      if (!is_array($data) || isset($data['error'])) {
        throw new \RuntimeException('Unexpected response: ' . substr((string) $response->getBody(), 0, 200));
      }
    }
    catch (\Throwable $e) {
      \Civi::log()->error('Hebrew Calendar Helper: Hebcal {path} lookup failed: {message}', ['path' => $path, 'message' => $e->getMessage()]);
      return NULL;
    }

    if ($cacheTtl) {
      \Civi::cache('long')->set($key, $data, $cacheTtl);
    }
    return $data;
  }

}
