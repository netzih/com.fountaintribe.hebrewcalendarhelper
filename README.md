# Hebrew Calendar Helper CiviCRM extension

Hebrew calendar tools for CiviCRM:
- Hebrew dates of birth and death.
- Yahrzeits and bar/bat mitzvah dates.
- A Yahrzeits search.
- Mail tokens for yahrzeits, today's Hebrew date, and this week's Shabbat and Yom Tov times.

Requires CiviCRM 5.78 or later and PHP's calendar extension (`ext-calendar`). If ext-calendar is missing, a System Status check reports it.

Older documentation is at https://github.com/sgladstone/com.fountaintribe.hebrewcalendarhelper/wiki

# Features added by this extension

## Settings

**Administer > System Settings > Hebrew Calendar Helper Settings** (`civicrm/admin/setting/hebrewcalendarhelper`). The old config page redirects here.

- **Shabbat times for mailings:**
  - **Location:** a city or US ZIP code, checked against Hebcal.com. If the name is ambiguous or misspelled, you get suggestions.
  - **Times:** candle lighting minutes, and how the end of Shabbat is calculated.
  - **Display:** the parsha transliteration and custom spellings, and date and time formats picked from examples.
  - **Preview:** a box shows this week's tokens, and you can preview any other week.
- **Yahrzeits:** in a leap year, which Adar holds the yahrzeit of someone who passed away in Adar of a regular year. The default is Adar I; versions before 4.1 always used Adar II. Customs differ.
- **Birthdays:** the earliest bat mitzvah age, 12 or 13. The default is 13.

## Tokens

### Shabbat times: `{shabbat.*}`

These cover the coming Shabbat, and any Yom Tov during the week or joined to Shabbat, at the configured location. They are filled in when a message is sent. Times come from [Hebcal.com](https://www.hebcal.com) (CC BY 4.0).

| Token | Example |
|---|---|
| `{shabbat.schedule}` | Every candle lighting and end time until Shabbat ends, plus the Torah portion, as a block that takes on the surrounding style (lines or a table) |
| `{shabbat.candle_lighting}` / `_date` / `_time` | Friday, Oct 9, 6:22 pm |
| `{shabbat.ends}` / `_date` / `_time` | Saturday, Oct 10, 7:18 pm |
| `{shabbat.parsha}` | Bereishit (or the holiday's name) |
| `{shabbat.holiday}` | Pesach (blank in an ordinary week) |
| `{shabbat.location}` | Richmond |
| `{shabbat.time1}` … `{shabbat.time5}`, each with `_label`, `_date`, `_time` | The lines of the schedule, for your own layout |

Pesach 2027, sent on the Monday:

```
Pesach candle lighting: Wednesday, Apr 21, 7:32 pm
Pesach candle lighting: Thursday, Apr 22, after 8:33 pm
Shabbat candle lighting: Friday, Apr 23, 7:34 pm
Shabbat ends: Saturday, Apr 24, 8:35 pm
Torah portion: Pesach III (CH''M)
```

### Yahrzeits for this mourner: `{yahrzeit.PART___WINDOW}`

For each yahrzeit the recipient observes in the WINDOW, the PART is filled in. Several yahrzeits are joined with ", ". One message can use several windows.

- **WINDOW:**
  - `day_7`, `day_10`, `day_14`, `day_30` (exactly that many days from now)
  - `week_cur`, `week_next`, `week_2` … `week_4` (Sunday to Saturday)
  - `month_cur`, `month_next`, `month_2` … `month_4`
- **PART:**
  - `deceased_name`, `deceased_hebrew_name`, `deceased_parents_hebrew_names`
  - `english_date` (the evening the yahrzeit begins), `morning_format_english`
  - `hebrew_date`, `hebrew_date_hebrew`
  - `dec_death_english_date`, `dec_death_hebrew_date`
  - `relationship_name`
  - `erev_shabbat_before`, `shabbat_morning_before`, `parashat_shabbat_before`
  - `erev_shabbat_after`, `shabbat_morning_after`, `parashat_shabbat_after`

Example: `{yahrzeit.deceased_name___month_next}`.

### Today: `{hebrewcalendar.*}`

- `{hebrewcalendar.today___hebrew_trans}`: 29 Tishrei 5787
- `{hebrewcalendar.today___hebrew}`: the same date in Hebrew letters

## Search > Yahrzeits

A SearchKit search (`civicrm/yahrzeits`) of mourners and the yahrzeits they observe.
- **Filters:** yahrzeit date (default: next 30 days), Hebrew month, group, deceased and mourner.
- **Actions:** it is a contact search, so its actions work as usual (email, PDF letter, add to group, export), with the `{yahrzeit.*}` tokens.
- **Requirements:** needs the SearchKit and Form Builder extensions, which come with CiviCRM.

The old "Yahrzeit Search" custom search is still available where the `legacycustomsearches` extension is enabled.

## APIs

- **`AllHebrewDates.calculate`:** recalculates everything; this is the scheduled job. With `contact_ids` it recalculates only those contacts.
- **`YahrzeitDate.get`:** yahrzeit dates for a list of mourners.
- **APIv4 `Yahrzeit`:** the calculated yahrzeits, read-only.

---

# CiviCRM Configurations created by this extension

## Custom Data Sets
- "Extended Date Information" Used to indicate if an individual was born/died before sunset. This can be edited when editing any individual. 
- "Yahrzeit Preferences" Used to indicate track mourner preferences for various yahrzeits observed. This can be edited when editing a yahrzeit relationship.
- "Yahrzeit Dates (Calculated Automatically)" - Nothing in this area is editable.
- "Hebrew Birth Dates (Calculated Automatically)" - Nothing in this area is editable.
- "Memorial Plaque Info" used for track information, such as if a deceased person has a memorial plaque, the location of the plaque, etc. This can be edited when editing any deceased individual. This area is also used to integrate with the electronic yahrzeit system from Yahrzeitronix (sales@yahrzeitronix.com) 
- "Religious"  Used to track religious information about individuals, such as their Hebrew name, Hebrew names of parents, etc. This can be edited when edited any individual.

## Custom Contact Types

"Deceased" based on Individual. This is set automatically. There is no need for the user to edit this.

## Custom Relationship Types

"Yahrzeit observed by"  --- "Yahrzeit observed in memory of"

 **Its strongly recommended** to create additional relationships types manually. See details at: https://github.com/sgladstone/com.fountaintribe.hebrewcalendarhelper/wiki

## CiviCRM Scheduled Jobs

"Call AllHebrewDates.Calculate API"   --- Recalculates all yahrzeits, Hebrew birthdays, and other observances tied to the Hebrew calendar.  This should run at least daily. It can run more frequently as preferred. 


---

## MySQL tables

"civicrm_fountaintribe_yahrzeits_temp" (APIv4 entity `Yahrzeit`). The scheduled job rebuilds it by filling a copy and swapping it in, so it is never empty while the job runs.

# What happens when this extension is disabled?

What is removed:
 - The MySQL table "civicrm_fountaintribe_yahrzeits_temp"
 - The API used by the scheduled job "Call AllHebrewDates.Calculate API"
 - The custom searches described in this README
 - The tokens described in this README
 - The APIs described in this README
 
What is left:
 - The CiviCRM custom field sets, custom fields, and other CiviCRM configurations described in this README. 
  
# What happens when this extension is disabled, then re-enabled? Or uninstalled then re-installed?

During enablement, the extension checks for the existence of the various CiviCRM configurations described in this README. If everything already exists, then nothing is changed. If anything is missing, then it is created.

For example: 
 - The custom relationship type "Yahrzeit observed by" does not exist. Yet all the other configurations exist, such as all the custom fields already exist.
 
 - The relationship type "Yahrzeit observed by" will be created during enablement.   No other configurations are created or changed. 

 - The MySQL temp tables are always recreated. 

########################################
## WARNING
 - DO NOT remove or move any of the custom fields, or this extension will NOT WORK. Changing the labels for the custom fields, contact types, and relationship types has NOT been tested.  
 - Changing labels is NOT RECOMMENDED as your environment will no longer match the user documentation/videos. 
  

# Changes in 4.1

**Merged in:** the `{shabbat.*}` tokens (from the shabbattimes extension).

**New:**
- One settings page for everything.
- Search > Yahrzeits in SearchKit.
- The Yahrzeit APIv4 entity.
- Deceased Hebrew name tokens.
- The Adar yahrzeit setting. The default is now Adar I, so yahrzeits in a leap year for deaths in Adar of a regular year move from Adar II to Adar I; choose Adar II to keep the old dates.

**Fixes:**
- **CiviCRM 6:**
  - The bat mitzvah setting is now declared. Before, it aborted the scheduled job and contact saves.
  - The legacy custom search no longer crashes.
  - The tokens no longer use the deprecated hooks.
- **Dates:**
  - Adar I dates printed without a month name.
  - A bar/bat mitzvah for a birthday on a missing 30 Cheshvan/Kislev came out a day early.
  - The English yahrzeit moved to next year a day early.
  - A blank "before sunset" flag stored an error message as the Hebrew date.
  - Changing only a date of death waited for cron.
- **Tokens:**
  - The month windows ignored the year (upstream #32).
  - Mixing windows in one message blanked all but one (#18).
  - "Today" used the server's time zone.
- **Security:** SQL injection in the APIs and the custom search.
- **Hebcal.com:** calls now have a timeout and a cache.
- **PHP 8:** the job runs without warnings.
- **Database:** the table is utf8mb4.

Also includes the open upstream PRs #38 to #45 by Coleman Watts.
