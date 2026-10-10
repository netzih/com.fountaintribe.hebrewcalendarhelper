<?php

/**
 * The old configuration page. Its setting now lives on the standard settings page; this keeps
 * old bookmarks and links working.
 */
class CRM_Hebrewcalendarhelper_Page_MainConfig extends CRM_Core_Page {

  public function run() {
    CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/admin/setting/hebrewcalendarhelper', 'reset=1'));
  }

}
