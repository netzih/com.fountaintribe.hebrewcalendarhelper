<?php

/**
 * Place autocomplete for the Shabbat location setting: proxies Hebcal's place search, which
 * browsers cannot call directly (no CORS).
 */
class CRM_Hebrewcalendarhelper_Page_Places extends CRM_Core_Page {

  public function run() {
    $query = trim((string) CRM_Utils_Request::retrieveValue('q', 'String', ''));
    CRM_Utils_JSON::output(\Civi\Hebrewcalendarhelper\Shabbat\Places::search($query));
  }

}
