<?php

/**
 * Upgrade steps. Install and uninstall are handled by the AutomaticUpgrader (info.xml) and
 * hook_civicrm_enable.
 */
class CRM_Hebrewcalendarhelper_Upgrader extends CRM_Extension_Upgrader_Base {

  /**
   * 4.1: rebuild the yahrzeit table as utf8mb4, then recalculate everything with the date
   * fixes of this release.
   */
  public function upgrade_4100(): bool {
    $this->ctx->log->info('Rebuilding the yahrzeit table as utf8mb4 and recalculating Hebrew dates');
    require_once __DIR__ . '/../../utils/HebrewCalendar.php';
    // Recreates the table and runs AllHebrewDates.calculate, as enabling the extension does.
    (new HebrewCalendar())->createExtensionConfigs();
    return TRUE;
  }

}
