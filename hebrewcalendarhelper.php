<?php

require_once 'hebrewcalendarhelper.civix.php';

/**
 * Implements hook_civicrm_post().
 */
function hebrewcalendarhelper_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  // if an individual is being created or edited, rebuild yahrzeit data, hebrew birthday info.
  if ($objectName == 'Individual' && ($op == 'create' || $op == 'edit' || $op == 'restore')) {
    // If there is a date of birth or date of death, then calculate Hebrew dates.
    // Note that this works with InlineEdit, as long as the section modified has relevant info.
    // Without ext-calendar there is nothing to calculate; the status check says so.
    if ((!empty($objectRef->birth_date) || !empty($objectRef->deceased_date)) && function_exists('gregoriantojd')) {
      // Calculate Hebrew demographic dates, such as next yahrzeit date, next hebrew birthday date for this contact.
      civicrm_api3('AllHebrewDates', 'calculate', [
        'sequential' => 1,
        'contact_ids' => $objectId,
      ]);
    }
  }
}

/**
 * Implements hook_civicrm_container().
 *
 * Registers the token subscribers. (Not with the scan-classes mixin: it would load every class
 * file, including the legacy custom search, which fails without legacycustomsearches.)
 */
function hebrewcalendarhelper_civicrm_container(\Symfony\Component\DependencyInjection\ContainerBuilder $container) {
  $container->register('civi.hebrewcalendarhelper.shabbatTokens', \Civi\Hebrewcalendarhelper\Shabbat\Tokens::class)
    ->addTag('kernel.event_subscriber')
    ->setPublic(TRUE);
  $container->register('civi.hebrewcalendarhelper.yahrzeitTokens', \Civi\Hebrewcalendarhelper\YahrzeitTokens::class)
    ->addTag('kernel.event_subscriber')
    ->setPublic(TRUE);
}

/**
 * Implements hook_civicrm_preProcess().
 */
function hebrewcalendarhelper_civicrm_preProcess($formName, &$form) {
  if (\Civi\Hebrewcalendarhelper\SettingsPage::isSettingsForm($formName, $form)) {
    \Civi\Hebrewcalendarhelper\SettingsPage::preProcess($form);
  }
}

/**
 * Implements hook_civicrm_buildForm().
 */
function hebrewcalendarhelper_civicrm_buildForm($formName, &$form) {
  if (\Civi\Hebrewcalendarhelper\SettingsPage::isSettingsForm($formName, $form)) {
    \Civi\Hebrewcalendarhelper\SettingsPage::buildForm();
  }
}

/**
 * Implements hook_civicrm_postProcess().
 */
function hebrewcalendarhelper_civicrm_postProcess($formName, $form) {
  if (\Civi\Hebrewcalendarhelper\SettingsPage::isSettingsForm($formName, $form)) {
    \Civi\Hebrewcalendarhelper\SettingsPage::postProcess();
  }
}

/**
 * Implements hook_civicrm_check().
 *
 * The Hebrew date math needs PHP's calendar extension, which many hosts leave out.
 */
function hebrewcalendarhelper_civicrm_check(&$messages, $statusNames = [], $includeDisabled = FALSE) {
  if ($statusNames && !in_array('hebrewcalendarhelperExtCalendar', $statusNames, TRUE)) {
    return;
  }
  if (!function_exists('gregoriantojd')) {
    $messages[] = new CRM_Utils_Check_Message(
      'hebrewcalendarhelperExtCalendar',
      ts('Hebrew Calendar Helper needs the PHP calendar extension (ext-calendar). Until your host enables it, Hebrew dates, yahrzeits and bar/bat mitzvah dates are not calculated.'),
      ts('PHP calendar extension missing'),
      \Psr\Log\LogLevel::ERROR,
      'fa-calendar'
    );
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function hebrewcalendarhelper_civicrm_config(&$config) {
  _hebrewcalendarhelper_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function hebrewcalendarhelper_civicrm_install() {
  _hebrewcalendarhelper_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function hebrewcalendarhelper_civicrm_enable() {
  _hebrewcalendarhelper_civix_civicrm_enable();

  require_once 'utils/HebrewCalendar.php';
  $tmp_cal = new HebrewCalendar();
  $tmp_cal->createExtensionConfigs();  // this function does not do anything if custom data/configs already exist.
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function hebrewcalendarhelper_civicrm_disable() {

  // This only removes temp stuff, ie stuff that is safely re-created when the extension is re-enabled.
  // Things that the organization is using to store their data is NOT removed.
  // ie extension-created CiviCRM custom data sets, relationship types, etc are left in place.
  require_once 'utils/HebrewCalendar.php';
  $tmp_cal = new HebrewCalendar();
  $tmp_cal->removeExtensionConfigs();

}
