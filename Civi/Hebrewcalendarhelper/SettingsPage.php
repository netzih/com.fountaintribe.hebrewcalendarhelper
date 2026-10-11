<?php

namespace Civi\Hebrewcalendarhelper;

use Civi\Hebrewcalendarhelper\Shabbat\Calendar;
use Civi\Hebrewcalendarhelper\Shabbat\Tokens as ShabbatTokens;
use CRM_Hebrewcalendarhelper_ExtensionUtil as E;

/**
 * Additions to the generated settings page, civicrm/admin/setting/hebrewcalendarhelper:
 * section headings, place autocomplete for the Shabbat location, and a preview of this week's
 * {shabbat.*} tokens.
 */
class SettingsPage {

  public static function isSettingsForm(string $formName, $form): bool {
    return $formName === 'CRM_Admin_Form_Generic' && $form->getSettingPageFilter() === 'hebrewcalendarhelper';
  }

  public static function preProcess(\CRM_Admin_Form_Generic $form): void {
    $form->sections['shabbat'] = [
      'title' => E::ts('Shabbat times for mailings'),
      'description' => E::ts('For the {shabbat.*} tokens: candle lighting, Shabbat and Yom Tov end times and the Torah portion, from Hebcal.com.'),
      'weight' => 10,
    ];
    $form->sections['yahrzeits'] = ['title' => E::ts('Yahrzeits'), 'weight' => 20];
    $form->sections['birthdays'] = ['title' => E::ts('Birthdays and bar/bat mitzvah'), 'weight' => 30];
  }

  public static function buildForm(): void {
    \Civi::resources()
      ->addVars('hebrewcalendarhelper', ['placesUrl' => \CRM_Utils_System::url('civicrm/ajax/hebrewcalendarhelper/places', NULL, FALSE, NULL, FALSE)])
      ->addScriptFile(E::LONG_NAME, 'js/settings.js');
    \CRM_Core_Region::instance('crm-setting-hebrewcalendarhelper-section-shabbat')->add([
      'markup' => self::summary(TRUE),
      'weight' => -10,
    ]);
  }

  /**
   * After saving: show the location as Hebcal's label for the matched place ("richmond va"
   * becomes "Richmond, Virginia, USA") and say what this week's tokens will show.
   */
  public static function postProcess(): void {
    $place = \Civi::settings()->get('hebrewcalendarhelper_shabbat_place');
    if ($place) {
      \Civi::settings()->set('hebrewcalendarhelper_shabbat_location', $place['title']);
      \CRM_Core_Session::setStatus(self::summary(FALSE), E::ts('Shabbat location confirmed'), 'success');
    }
  }

  /**
   * HTML describing the matched place and this week's token values.
   *
   * @param bool $box
   *   For the settings page: a message box, with a date picker to preview another week
   *   (?preview=YYYY-MM-DD, read as 10am that day).
   */
  public static function summary(bool $box): string {
    $place = \Civi::settings()->get('hebrewcalendarhelper_shabbat_place');
    if (empty($place['title'])) {
      return $box ? '<div id="hebrewcalendarhelper-shabbat-preview" class="messages status no-popup">' . E::ts('No Shabbat location set yet. Enter a city or ZIP code below to use the {shabbat.*} tokens.') . '</div>' : '';
    }

    $preview = $box ? (string) \CRM_Utils_Request::retrieveValue('preview', 'String') : '';
    $now = NULL;
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $preview)) {
      $now = new \DateTimeImmutable($preview . ' 10:00', new \DateTimeZone($place['tzid']));
    }
    $week = Calendar::current($now);
    $values = ShabbatTokens::values($week);
    $h = fn($text) => htmlspecialchars((string) $text, ENT_QUOTES);

    $html = '<p><strong>' . E::ts('Hebcal location') . ':</strong> ' . $h($place['title'] . ' (' . $place['tzid'] . ')')
      . '<br><strong>{shabbat.location}:</strong> ' . $h($values['location'])
      . ($values['holiday'] !== '' ? '<br><strong>{shabbat.holiday}:</strong> ' . $h($values['holiday']) : '') . '</p>';
    $html .= $week ? ShabbatTokens::scheduleHtml($week) : '<p>' . E::ts('Hebcal.com could not be reached.') . '</p>';
    if (!$box) {
      return $html;
    }

    $title = $now
      ? E::ts('The week of %1, as a mailing sent that morning would show it ({shabbat.schedule}):', [1 => $h($now->format('l, F j, Y'))])
      : E::ts('This week, as mailings will show it ({shabbat.schedule}):');
    $picker = '<p>' . E::ts('Preview another week') . ': <input type="date" class="crm-form-text" id="hebrewcalendarhelper-preview" value="' . $h($preview) . '"> '
      . '<a href="#" id="hebrewcalendarhelper-preview-go" class="button">' . E::ts('Show') . '</a>'
      . ($now ? ' <a href="' . $h(\CRM_Utils_System::url('civicrm/admin/setting/hebrewcalendarhelper', 'reset=1')) . '">' . E::ts('Back to this week') . '</a>' : '')
      . '</p>';
    return '<div id="hebrewcalendarhelper-shabbat-preview" class="messages status no-popup"><p>' . $title . '</p>' . $html . $picker . '</div>';
  }

}
