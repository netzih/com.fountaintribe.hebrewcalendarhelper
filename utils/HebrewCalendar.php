<?php

class HebrewCalendar{


	
	static private $allRemoteHebCalData = [];  // data from the http://hebcal.com API.
	
	// Should always be set to false in a production environment.
	const ALWAYS_CLEAR_TEMP_TABLE = false;
	
	const YAHRZEIT_TEMP_TABLE_NAME = "civicrm_fountaintribe_yahrzeits_temp";  //  old table name: "pogstone_temp_yahrzeits";

	// After how many minutes should yahrzeit cache data be recalculated?
	// In a production environment this is typically 12 hours, ie 720 minutes.
	const YAHRZEIT_CACHE_TIMEOUT = "15";


	const HEBREW_MONTH_TISHREI = "1";
	const HEBREW_MONTH_HESHVAN = "2";
	const HEBREW_MONTH_KISLEV = "3";
	const HEBREW_MONTH_TEVET = "4";
	const HEBREW_MONTH_SHEVAT = "5";
	const HEBREW_MONTH_ADAR = "6";
	const HEBREW_MONTH_ADAR_2 = "7";
	const HEBREW_MONTH_NISAN = "8";
	const HEBREW_MONTH_IYYAR = "9";
	const HEBREW_MONTH_SIVAN = "10";
	const HEBREW_MONTH_TAMUZ = "11";
	const HEBREW_MONTH_AV    = "12";
	const HEBREW_MONTH_ELUL  = "13";
	
	
	const EXTENDED_DATE_CUSTOM_FIELD_GROUP_TITLE = "Extended Date Information";
	const EXTENDED_DATE_CUSTOM_FIELD_GROUP_NAME = "Extended_Date_Information";
	
	const EXTENDED_DATE_CUSTOM_FIELD_BIRTH_TITLE = "Birth Date Before Sunset";
	const EXTENDED_DATE_CUSTOM_FIELD_BIRTH_NAME = "Birth_Date_Before_Sunset";
	
	const EXTENDED_DATE_CUSTOM_FIELD_DEATH_TITLE = "Death Date Before Sunset";
	const EXTENDED_DATE_CUSTOM_FIELD_DEATH_NAME = "Death_Date_Before_Sunset";
	
	
	// Original title: Yahrzeit Details   
	// Original name: Yahrzeit_Details
	const YAH_RELATIONSHIPTYPE_CUSTOM_FIELD_GROUP_TITLE = "Yahrzeit Preferences";
	const YAH_RELATIONSHIPTYPE_CUSTOM_FIELD_GROUP_NAME = "Yahrzeit_Preferences";
	
	
	const YAH_RELATIONSHIPTYPE_CUSTOM_FIELD_TITLE = "Does mourner observe the English date?";
	const YAH_RELATIONSHIPTYPE_CUSTOM_FIELD_NAME = "Mourner_observes_the_English_date";
	
	const YAH_DECEASED_CONTACT_TYPE_NAME = "Deceased";
	const YAH_DECEASED_CONTACT_TYPE_TITLE = "Deceased";
	
	const YAH_DECEASED_CUSTOM_FIELD_GROUP_TITLE = "Yahrzeit Dates (Calculated Automatically)";
	// Original name: Hebrew_Calendar_Demographics
	const YAH_DECEASED_CUSTOM_FIELD_GROUP_NAME = "Yahrzeit_Dates";
	
	// custom fields for above custom group
	const YAH_NEXT_HEB_YAHRZEIT_NAME = "Next_Hebrew_Yahrzeit";
	const YAH_NEXT_HEB_YAHRZEIT_TITLE = "Next Hebrew Yahrzeit (Starts at sunset on this date)";
	
	const YAH_NEXT_ENGLISH_YAHRZEIT_NAME = "Next_English_Yahrzeit";
	const YAH_NEXT_ENGLISH_YAHRZEIT_TITLE = "Next English Yahrzeit";
	
	const YAH_HEB_DEATH_DATE_NAME = "Hebrew_Date_of_Death";
	const YAH_HEB_DEATH_DATE_TITLE = "Hebrew Date of Death";
	
	
	const HEB_BIRTH_CUSTOM_FIELD_GROUP_TITLE = "Hebrew Birth Dates (Calculated Automatically)";
	const HEB_BIRTH_CUSTOM_FIELD_GROUP_NAME = "Hebrew_Birth_Dates";
	
	const HEB_BIRTH_DATE_NAME = "Hebrew_Date_of_Birth";
	const HEB_BIRTH_DATE_TITLE = "Hebrew Date of Birth";


	const HEB_NEXT_BIRTHDAY_NAME = "Next_Hebrew_Birthday";
	const HEB_NEXT_BIRTHDAY_TITLE = "Next Hebrew Birthday";
	
	const HEB_EARLIEST_BARBAT_MITZVAH_NAME = "Hebrew_Earliest_BarBat_Date";
	const HEB_EARLIEST_BARBAT_MITZVAH_TITLE = "Earliest Possible Bar/Bat Mitzvah Date (Starts at sunset on this date)";
	
	const RELIGIOUS_CUSTOM_FIELD_GROUP_NAME = "Religious";
	const RELIGIOUS_CUSTOM_FIELD_GROUP_TITLE = "Religious";
	
	const HEB_NAME_FIELD_TITLE = "Hebrew Name";
	const HEB_NAME_FIELD_NAME = "Hebrew_Name";
	
	const HEB_NAME_PARENTS_FIELD_TITLE = "Hebrew Names of Parents";
	const HEB_NAME_PARENTS_FIELD_NAME = "Hebrew_Names_of_Parents";
	
	const IS_JEWISH_FIELD_TITLE = "Is Jewish";
	const IS_JEWISH_FIELD_NAME = "Is_Jewish";
	

	// Tribe (select list) // options: 'Israelite' , 'Levi' , 'Cohen' 
	
	// Tribe field type: String, Select
	// Tribe choices:  Cohen, Israelite, Levi
	const RELIGIOUS_TRIBE_FIELD_NAME = "Tribe";
	const RELIGIOUS_TRIBE_FIELD_TITLE = "Tribe";
	
	const RELIGIOUS_TRIBE_OPTIONS_NAME = "tribe_options";
	
	
	
	
	// Israelite Kohen and Levi aliyot
	
	// Other fields to add to "Religious" field set:
	// Bar_Bat_Mitzvah_Date , Bar/Bat Mitzvah Date  (date)  --> ie when is it scheduled to occur in the shul
	// Bar_Bat_Mitzvah_Parasha , Bar_Bat_Mitzvah_Parasha (text)
	// Confirmation_Date , Confirmation Date (date)
	
	const PLAQUE_CUSTOM_FIELD_GROUP_TITLE = "Memorial Plaque Info";
	const PLAQUE_CUSTOM_FIELD_GROUP_NAME = "Plaque_Info";
	
	const HAS_PLAQUE_FIELD_TITLE = "Has Plaque"; // boolean
	const HAS_PLAQUE_FIELD_NAME = "Has_Plaque";
	
	const PLAQUE_LOCATION_TITLE = "Plaque Location";
	const PLAQUE_LOCATION_NAME = "Plaque_Location";
	
	
// Yahrzeit - correct spelling, per hebcal.com

	// TODO: Fix original names, titles,  in older CiviCRM databases. 
// Yarzheit - incorrect spelling, fix via SQL as needed.  also spaces used to be used in the 'name'

	// original relationship title: 'Yarzheit observed by'  
	// original relationship name: 'Yarzheit observed by'
	
	const YAHRZEIT_RELATIONSHIP_TYPE_A_B_TITLE = "Yahrzeit observed by";
	const YAHRZEIT_RELATIONSHIP_TYPE_A_B_NAME = "Yahrzeit_observed_by";
	
	const YAHRZEIT_RELATIONSHIP_TYPE_B_A_TITLE =  "Yahrzeit observed in memory of";
	const YAHRZEIT_RELATIONSHIP_TYPE_B_A_NAME = "Yahrzeit_observed_in_memory_of";
	
	
	
	private static function getParashatByDateHebrew(  &$date_parm){
		
		$tmp_hebcal_data_all_years = HebrewCalendar::getAllRemoteHebCalData();
		
		$year_parm = substr($date_parm, 0, 4 );
		
		if( isset( $tmp_hebcal_data_all_years[$year_parm] ) ){
			$tmp_hebcal_data = $tmp_hebcal_data_all_years[$year_parm];
			//CRM_Core_Error::debug($year_parm." Heb data: ", $tmp_hebcal_data );
			foreach( $tmp_hebcal_data as $cur ){
				if( $cur->category == "parashat" &&  $cur->date == $date_parm ){
			
					$parashat_title_hebrew = $cur->hebrew;
					$parashat_date = $cur->date;
			
					return $parashat_title_hebrew ;
			
				}else{
					// keep looking.
				}
					
					
			}
		}
		
		
	}
	
	
	private static function getParashatByDate( &$date_parm){
		
		$tmp_hebcal_data_all_years = HebrewCalendar::getAllRemoteHebCalData(); 
		
		$year_parm = substr($date_parm, 0, 4 );
		
		if( isset( $tmp_hebcal_data_all_years[$year_parm] ) ){
			$tmp_hebcal_data = $tmp_hebcal_data_all_years[$year_parm];
			//CRM_Core_Error::debug($year_parm." Heb data: ", $tmp_hebcal_data );
			foreach( $tmp_hebcal_data as $cur ){
				if( $cur->category == "parashat" &&  $cur->date == $date_parm ){
					
					$parashat_title = $cur->title;
					$parashat_date = $cur->date;
					
					return $parashat_title ; 
					
				}else{
					// keep looking. 
				}
				
				
			}
		}
		
		
	}
	
	private static function getHolidayByDateHebrew( &$date_parm){
	
		$tmp_hebcal_data_all_years = HebrewCalendar::getAllRemoteHebCalData();
	
		$year_parm = substr($date_parm, 0, 4 );
	
		if( isset( $tmp_hebcal_data_all_years[$year_parm] ) ){
			$tmp_hebcal_data = $tmp_hebcal_data_all_years[$year_parm];
			//CRM_Core_Error::debug($year_parm." Heb data: ", $tmp_hebcal_data );
			foreach( $tmp_hebcal_data as $cur ){
				if( $cur->category == "holiday" &&  $cur->date == $date_parm ){
	
					$holiday_title_hebrew = $cur->hebrew;
					$holiday_date = $cur->date;
	
					return $holiday_title_hebrew;
	
				}else{
					// keep looking.
				}
	
	
			}
		}
	
	
	}
	
	
	private static function getHolidayByDate( &$date_parm){
	
		$tmp_hebcal_data_all_years = HebrewCalendar::getAllRemoteHebCalData();
	
		$year_parm = substr($date_parm, 0, 4 );
	
		if( isset( $tmp_hebcal_data_all_years[$year_parm] ) ){
			$tmp_hebcal_data = $tmp_hebcal_data_all_years[$year_parm];
			//CRM_Core_Error::debug($year_parm." Heb data: ", $tmp_hebcal_data );
			foreach( $tmp_hebcal_data as $cur ){
				if( $cur->category == "holiday" &&  $cur->date == $date_parm ){
						
					$holiday_title = $cur->title;
					$holiday_date = $cur->date;
						
					return $holiday_title;
						
				}else{
					// keep looking.
				}
	
	
			}
		}
	
	
	}
	
	
	private static function isLocatedInIsrael(){
		// Get the domain's country for this CiviCRM environment's current domain.
		// This assumes this country is the same for all domains in a multi-site CiviCRM.
		// TODO: get more information about how to handle multi-site environments.
			
		$default_country_name_tmp = "";
		$result = civicrm_api3('Domain', 'get', [
				'sequential' => 1,
				'current_domain' => ['IS NOT NULL' => 1],
		]);
			
			
		if( $result['is_error'] == 0 && $result['count'] == 1){
			$tmp_domain_address = $result['values'][0]['domain_address'] ?? NULL;
				
			if( is_array($tmp_domain_address)){
				$tmp_country_id = $tmp_domain_address['country_id'] ?? '';
					
				//CRM_Core_Error::debug("Country ID: ", $tmp_country_id);
				if( strlen((string) $tmp_country_id) > 0 ){
					$result_country = civicrm_api3('Country', 'get', [
							'sequential' => 1,
							'id' => $tmp_country_id,
					]);
		
					if( $result_country['is_error'] == 0 && $result_country['count'] == 1){
							
						$default_country_name_tmp = $result_country['values'][0]['name'] ;
					}
				}
			}
				
				
				
				
		}else{
				
		}
		
		if( strtolower( $default_country_name_tmp) == "israel"){
			$tmp_in_israel = "true";
			// CRM_Core_Error::debug("Will use API setting for Israel", $default_country_name_tmp);
		}else{
			$tmp_in_israel = "false";
			// CRM_Core_Error::debug("Will use API setting for diaspora. ", $default_country_name_tmp);
		}
			
		return $tmp_in_israel; 
		
	}
	
	
	
	private static function getRemoteHebCalDataByYear(&$year_parm ){
		
		// Documentation for HebCal.com REST API: https://www.hebcal.com/home/195/jewish-calendar-rest-api
		$in_israel_crm_domain_setting = HebrewCalendar::isLocatedInIsrael();
		
		if(strlen((string) $year_parm) == 4 && strlen((string) $in_israel_crm_domain_setting) > 0 ){
			
			/*
			 * Mutually exclusive language parameter for HebCal API:

			    lg=s – Sephardic transliterations (default if unspecified)
			    lg=sh – Sephardic translit. + Hebrew
			    lg=a – Ashkenazis transliterations
			    lg=ah – Ashkenazis translit. + Hebrew
			    lg=h – Hebrew only

			 */
			
			$language_api_parm = "s"; // Uses the modern Hebrew transliteration, ie 'parashat'.   'a' and 'ah' uses the older, Yiddish-style ie 'parashas' which is rarely used. 
			
			// Since API parm 'geo' is set to 'none', no candle-lighting times will get returned from the API. 
			$geo_api_query_string = "&geo=none";
			
			
			if( strtolower($in_israel_crm_domain_setting) == "true"){
				$tmp_in_israel = "on";
				// HebCal.com API:	'on' = in Israel
				// CRM_Core_Error::debug("Will use HebCal.com API setting for Israel", $tmp_in_israel);
			}else{
				$tmp_in_israel = "off";
				// HebCal.com API:	'off' = diaspora
				// CRM_Core_Error::debug("Will use  HebCal.com API setting for diaspora. ", $tmp_in_israel);
			}
		
			// Holidays and portions for a year never change: cache them for 30 days.
			$data = \Civi\Hebrewcalendarhelper\Hebcal::get('hebcal', [
				'v' => 1, 'cfg' => 'json', 'maj' => 'on', 'min' => 'on', 'lg' => $language_api_parm,
				'i' => $tmp_in_israel, 'mod' => 'on', 'nx' => 'on', 'year' => $year_parm, 'month' => 'x',
				'ss' => 'on', 'mf' => 'on', 'geo' => 'none', 's' => 'on',
			], 30 * 86400);
			// The lookups below read the items as objects.
			HebrewCalendar::$allRemoteHebCalData[$year_parm] = array_map(fn($item) => (object) $item, $data['items'] ?? []);

		//var_export($decoded->response);
		}else{
			\Civi::log()->warning("Hebrew Calendar Helper: no year to look up holidays and portions for.");
		}
		
		
		
	}
	
	
	private static function getAllRemoteHebCalData(){
		
		$tmp_rtn = null; 
		if(count( HebrewCalendar::$allRemoteHebCalData) == 0){
			
			$years_array = [];
			
			// Use remote hebcal.com API to get needed data for last year, current year, and next year. 
			$years_offsets_arr = [-4, -3, -2, -1, 0, 1, 2, 3, 4] ;
			foreach($years_offsets_arr as $year_offset){
				
				$tmp_year = date("Y") + $year_offset;
				$years_array[] = $tmp_year;
			}
			
			
		//	$last_year = date("Y") - 1;
		//	$current_year = date("Y");
		//	$next_year = date("Y") + 1;
			
		//	$years_array = array($last_year, $current_year, $next_year );
			
			foreach($years_array as $tmp_year){
				
				HebrewCalendar::getRemoteHebCalDataByYear( $tmp_year ); 
			}		
			
		}
		
		$tmp_rtn = HebrewCalendar::$allRemoteHebCalData;
		return $tmp_rtn;
		
	}
	
	function determine_relationship_name($mourner_contact_id, $deceased_contact_id  ){
	
		if(strlen((string) $mourner_contact_id) == 0){
			return '';
		}
	
	
		$formated_rel_name = '';
		$sql = "(SELECT label_b_a as label , name_b_a as rel_api_name,  civicrm_option_value.name as gender_label
		FROM `civicrm_relationship` rel, civicrm_relationship_type reltype, civicrm_contact con
		LEFT JOIN civicrm_option_value ON civicrm_option_value.option_group_id = (SELECT id FROM civicrm_option_group WHERE name = 'gender') AND con.gender_id = civicrm_option_value.value
		where
		rel.relationship_type_id = reltype.id
		and contact_id_a = $mourner_contact_id and contact_id_b = $deceased_contact_id
		and contact_id_b = con.id
		and rel.is_active = 1  )
		UNION
		(SELECT label_a_b as label , name_a_b as rel_api_name,  civicrm_option_value.name as gender_label
		FROM `civicrm_relationship` rel, civicrm_relationship_type reltype, civicrm_contact con
		LEFT JOIN civicrm_option_value ON civicrm_option_value.option_group_id = (SELECT id FROM civicrm_option_group WHERE name = 'gender') AND con.gender_id = civicrm_option_value.value
		where
		rel.relationship_type_id = reltype.id
		and contact_id_a = $deceased_contact_id and contact_id_b = $mourner_contact_id
		and contact_id_a = con.id
		and rel.is_active = 1)		";
	
		$dao = CRM_Core_DAO::executeQuery( $sql ) ;
		$first_time = true;
		while ( $dao->fetch( ) ) {
	
			$tmp_label = $dao->label;
			$tmp_rel_api_name = $dao->rel_api_name;
			$deceased_gender = $dao->gender_label;
	
			$seperator = ', ';
			 
			// print "<hr>Current relationship label from db: ".$tmp_label;
	
			if($tmp_rel_api_name <> HebrewCalendar::YAHRZEIT_RELATIONSHIP_TYPE_A_B_NAME) {
	
				// print "<br>current label is not yahzeit, so need to clean up for return value. ";
				if(strlen((string) $formated_rel_name) > 0){
					//  print "<br>  a) formatted rel name: ".$formated_rel_name;
	
	
	
					$formated_rel_name = $formated_rel_name.$seperator;
					//   print "<br>  b) formatted rel name: ".$formated_rel_name ;
				}
				//  print "<br>Dirty current label: ".$tmp_label;
	
				$tmp_label = strtolower ( $tmp_label);
				$pos = strripos($tmp_label , ' of' );
				if($pos){
					$tmp_label = substr( $tmp_label , 0,  $pos);
				}
	
				$pos = strripos($tmp_label , ' by' );
				if($pos){
					$tmp_label = substr( $tmp_label , 0,  $pos);
				}
	
				// Cleaned current label. (lower cased, and of,by removed) : $tmp_label;
	
				if($deceased_gender == "Female"){
					if($tmp_label == "parent"){ $tmp_label = "mother"; }
					else if($tmp_label == "spouse"){ $tmp_label = "wife"; }
					else if($tmp_label == "sibling"){ $tmp_label = "sister"; }
					else if($tmp_label == "grandparent"){ $tmp_label = "grandmother"; }
					else if($tmp_label == "step-parent"){ $tmp_label = "step-mother"; }
					else if($tmp_label == "child"){ $tmp_label = "daughter"; }
					else if($tmp_label == "step-child"){ $tmp_label = "step-daughter"; }
					else if($tmp_label == "grandchild"){ $tmp_label = "granddaughter"; }
					else if($tmp_label == "aunt is"){ $tmp_label = "niece";}
					else if($tmp_label == "uncle is"){ $tmp_label = "niece";}
					else if($tmp_label == "parent-in-law"){ $tmp_label = "mother-in-law" ; }
					else if($tmp_label == "child-in-law"){ $tmp_label = "daughter-in-law" ; }
					else if($tmp_label == "sibling-in-law"){ $tmp_label = "sister-in-law" ; }
	
	
				}else if($deceased_gender == "Male"){
					if($tmp_label == "parent"){ $tmp_label = "father"; }
					else if($tmp_label == "spouse"){ $tmp_label = "husband"; }
					else if($tmp_label == "sibling"){ $tmp_label = "brother"; }
					else if($tmp_label == "grandparent"){ $tmp_label = "grandfather"; }
					else if($tmp_label == "step-parent"){ $tmp_label = "step-father"; }
					else if($tmp_label == "child"){ $tmp_label = "son"; }
					else if($tmp_label == "step-child"){ $tmp_label = "step-son"; }
					else if($tmp_label == "grandchild"){ $tmp_label = "grandson"; }
					else if($tmp_label == "aunt is"){ $tmp_label = "nephew";}
					else if($tmp_label == "uncle is"){ $tmp_label = "nephew";}
					else if($tmp_label == "parent-in-law"){ $tmp_label = "father-in-law" ; }
					else if($tmp_label == "child-in-law"){ $tmp_label = "son-in-law" ; }
					else if($tmp_label == "sibling-in-law"){ $tmp_label = "brother-in-law" ; }
	
				}else{
					if($tmp_label == "aunt is"){ $tmp_label = "niece/nephew";}
					else if($tmp_label == "uncle is"){ $tmp_label = "niece/nephew";}
				}
	
				$formated_rel_name = $formated_rel_name.$tmp_label;
				$first_time = false;
			}
	
	
		}
		$dao->free( );
	
		return $formated_rel_name;
	}
	
	
	
	



	



	



	
	
	// This fills in parashat and holiday info
	function fillYahrzeitParashat(&$contact_ids, $table = HebrewCalendar::YAHRZEIT_TEMP_TABLE_NAME){
		// The row IDs below are collected with GROUP_CONCAT, which cuts off at 1024 bytes by default.
		CRM_Core_DAO::executeQuery("SET SESSION group_concat_max_len = 1000000");
		
		
		
		if(strlen((string) $contact_ids) > 0){
			$sql_conids_filter = " AND deceased_contact_id  IN ( $contact_ids ) ";
		}else{
			$sql_conids_filter = "";
		}
		
		$sql = "SELECT group_concat( id) as ids , date(yahrzeit_shabbat_morning_before) as shabbat_date , 'before' as shabbat_type
				 FROM ".$table.
				" WHERE yahrzeit_shabbat_morning_before IS NOT NULL ".$sql_conids_filter.
				"  GROUP BY yahrzeit_shabbat_morning_before
				order by yahrzeit_shabbat_morning_before  ";
		
		$dao = CRM_Core_DAO::executeQuery( $sql ) ;
		
		while ( $dao->fetch( ) ) {
			
			$shabbat_date = $dao->shabbat_date;
			$shabbat_type = $dao->shabbat_type;
			$row_ids_raw  = $dao->ids;
		
			
			
			
			
			// get Holiday for Shabbat before the yahrzeit.
			$tmpHoliday = HebrewCalendar::getHolidayByDate($shabbat_date);
			$tmpHolidayHebrew = HebrewCalendar::getHolidayByDateHebrew($shabbat_date);
			
			if(  strlen((string) $tmpHoliday ) > 0 ){		
				//	$tmpParashat_cleaned = str_replace("'", "''", $tmpParashat);
				$update_sql = "UPDATE ".$table."
						set shabbat_before_holiday = %1,
						 shabbat_before_holiday_hebrew = %2
						       WHERE id IN ( ".$row_ids_raw." ) ";
			
				$params_a = [];
				$params_a[1] =  [ $tmpHoliday, 'String' ];
				
				$params_a[2] =  [ $tmpHolidayHebrew, 'String' ];
			
				$update_dao = CRM_Core_DAO::executeQuery( $update_sql,   $params_a ) ;
				$update_dao->free();
			
			}
			
			// getHolidayByDateHebrew
			
			$tmpParashat = HebrewCalendar::getParashatByDate($shabbat_date);
			if( strlen((string) $tmpParashat ) > 0  ){
				
			//	$tmpParashat_cleaned = str_replace("'", "''", $tmpParashat);
				$update_sql = "UPDATE ".$table." 
						set shabbat_before_parashat = %1 
						       WHERE id IN ( ".$row_ids_raw." ) ";			
				
				$params_a = [];
				$params_a[1] =  [ $tmpParashat, 'String' ];
				
				
				$update_dao = CRM_Core_DAO::executeQuery( $update_sql,   $params_a ) ;
				$update_dao->free();
				
			}
			
			// Now get the Hebrew name of the parashat.
			$tmpParashatHebrew = HebrewCalendar::getParashatByDateHebrew($shabbat_date);
			if( strlen((string) $tmpParashatHebrew ) > 0 ){
			
				//	$tmpParashat_cleaned = str_replace("'", "''", $tmpParashat);
				$update_sql = "UPDATE ".$table." set shabbat_before_parashat_hebrew = %1
						       WHERE id IN ( ".$row_ids_raw." ) ";
			
				$params_a = [];
				$params_a[1] =  [ $tmpParashatHebrew, 'String' ];
			
			
				$update_dao = CRM_Core_DAO::executeQuery( $update_sql,   $params_a ) ;
				$update_dao->free();
			
			}
			
			
			//CRM_Core_Error::debug("Shabbat Date: ".$shabbat_date, $tmpParashat );
			
		}
		
		$dao->free();
		
		
		$sql = "SELECT group_concat( id) as ids , date(yahrzeit_shabbat_morning_after) as shabbat_date , 'after' as shabbat_type
				FROM ".$table.
				" WHERE yahrzeit_shabbat_morning_after IS NOT NULL ".$sql_conids_filter.
				" GROUP BY yahrzeit_shabbat_morning_after
				 order by yahrzeit_shabbat_morning_after";
		
		
		$dao = CRM_Core_DAO::executeQuery( $sql ) ;
		
		while ( $dao->fetch( ) ) {
				
			$shabbat_date = $dao->shabbat_date;
			$shabbat_type = $dao->shabbat_type;
			$row_ids_raw  = $dao->ids;
		
				
			
			
			// get Holiday for Shabbat after the yahrzeit.
			$tmpHoliday = HebrewCalendar::getHolidayByDate(  $shabbat_date);
			$tmpHolidayHebrew = HebrewCalendar::getHolidayByDateHebrew($shabbat_date);
			if(  strlen((string) $tmpHoliday ) > 0 ){
				//	$tmpParashat_cleaned = str_replace("'", "''", $tmpParashat);
				$update_sql = "UPDATE ".$table."
						set shabbat_after_holiday = %1 , 
						shabbat_after_holiday_hebrew = %2
						       WHERE id IN ( ".$row_ids_raw." ) ";
					
				$params_a = [];
				$params_a[1] =  [ $tmpHoliday, 'String' ];
				
				
				$params_a[2] =  [ $tmpHolidayHebrew, 'String' ];
					
				$update_dao = CRM_Core_DAO::executeQuery( $update_sql,   $params_a ) ;
				$update_dao->free();
					
			}
			
			
			$tmpParashat = HebrewCalendar::getParashatByDate($shabbat_date);
			if( strlen((string) $tmpParashat ) > 0 ){
		
				$tmpParashat_cleaned = str_replace("'", "''", $tmpParashat);
				$update_sql = "UPDATE ".$table." set shabbat_after_parashat = '$tmpParashat_cleaned'
				WHERE id IN ( ".$row_ids_raw." ) ";
				$update_dao = CRM_Core_DAO::executeQuery( $update_sql ) ;
				$update_dao->free();
		
			}
			
			// Now get the Hebrew name of the parashat.
			$tmpParashatHebrew = HebrewCalendar::getParashatByDateHebrew($shabbat_date);
			if( strlen((string) $tmpParashatHebrew ) > 0 ){
					
				//	$tmpParashat_cleaned = str_replace("'", "''", $tmpParashat);
				$update_sql = "UPDATE ".$table." set shabbat_after_parashat_hebrew = %1
						       WHERE id IN ( ".$row_ids_raw." ) ";
					
				$params_a = [];
				$params_a[1] =  [ $tmpParashatHebrew, 'String' ];
					
					
				$update_dao = CRM_Core_DAO::executeQuery( $update_sql,   $params_a ) ;
				$update_dao->free();
					
			}
				
				
			//CRM_Core_Error::debug("Shabbat Date: ".$shabbat_date, $tmpParashat );
				
		}
		
		$dao->free();
		
		
		
		
	}
	
	function scrubBirthCalculatedFields( $contact_ids  ){
		$rtn_data = [];
		
		if( strlen((string) $contact_ids) > 0){
			$contactids_sql = " AND bcg.entity_id IN ( $contact_ids )";
		
		}else{
			$contactids_sql = "";
		}
		
		
		$params = [
				'version' => 3,
				'sequential' => 1,
				'name' => HebrewCalendar::HEB_BIRTH_CUSTOM_FIELD_GROUP_NAME,
		];
		$result = civicrm_api('CustomGroup', 'getsingle', $params);
		
		if(isset($result['table_name'])){
			$heb_cal_table_name = $result['table_name'];
			$heb_cal_set_id = $result['id'];
		}else{
			$heb_cal_table_name = "";
		}
		
		
		if( strlen((string) $heb_cal_table_name) > 0){
		
		
			$params = [
					'version' => 3,
					'sequential' => 1,
					'custom_group_id' =>  $heb_cal_set_id,
					'name' => HebrewCalendar::HEB_BIRTH_DATE_NAME,
			];
			$result = civicrm_api('CustomField', 'getsingle', $params);
		
			if( isset ( $result['column_name'])){
				$col_name_heb_birth_date = $result['column_name'];
			}else{
				$col_name_heb_birth_date = "";
		
			}
		
		
			$params = [
					'version' => 3,
					'sequential' => 1,
					'custom_group_id' =>  $heb_cal_set_id,
					'name' => HebrewCalendar::HEB_EARLIEST_BARBAT_MITZVAH_NAME,
			];
			$result = civicrm_api('CustomField', 'getsingle', $params);
		
			if( isset ( $result['column_name'])){
				$col_name_earliest_barbat = $result['column_name'];
			}else{
				$col_name_earliest_barbat = "";
			}
		
			$params = [
					'version' => 3,
					'sequential' => 1,
					'custom_group_id' =>  $heb_cal_set_id,
					'name' => HebrewCalendar::HEB_NEXT_BIRTHDAY_NAME,
			];
			$result = civicrm_api('CustomField', 'getsingle', $params);
		
			if( isset ( $result['column_name'])){
				$col_name_hebrew_next_birthday = $result['column_name'];
			}else{
				$col_name_hebrew_next_birthday = "";
			}
				
			$sql = "update  ".$heb_cal_table_name." bcg SET ".
					"bcg.".$col_name_heb_birth_date." = null,  ".
					"bcg.".$col_name_earliest_barbat." = null, ".
					"bcg.".$col_name_hebrew_next_birthday." = null ".
					" WHERE 1=1 ".$contactids_sql;
						
					//$rtn_data['error_message'] = "SQL - ".$sql;
					//return $rtn_data;
					$dao_update = CRM_Core_DAO::executeQuery( $sql ) ;
					$dao_update->free();
						
		}else{
			// something is wrong, could not find the table.
				
		}
		
		//$rtn_data['error_message'] = "Debug, birthday scrub is done";
		//return $rtn_data;
	}
	
	// set as null: all CiviCRM custom fields that are read-only fields that get calculated by this extension. 
	function scrubDeceasedCalculatedFields( $contact_ids  ){
		$rtn_data = [];
		
		if( strlen((string) $contact_ids) > 0){
			$contactids_sql = " AND dcg.entity_id IN ( $contact_ids )";
				
		}else{
			$contactids_sql = "";
		}
		
		
		$params = [
				'version' => 3,
				'sequential' => 1,
				'name' => HebrewCalendar::YAH_DECEASED_CUSTOM_FIELD_GROUP_NAME,
		];
		$result = civicrm_api('CustomGroup', 'getsingle', $params);
		
		if(isset($result['table_name'])){
			$heb_cal_table_name = $result['table_name'];
			$heb_cal_set_id = $result['id'];
		}else{
			$heb_cal_table_name = "";
		}
		
		
		if( strlen((string) $heb_cal_table_name) > 0){
		
		
			$params = [
					'version' => 3,
					'sequential' => 1,
					'custom_group_id' =>  $heb_cal_set_id,
					'name' => HebrewCalendar::YAH_NEXT_HEB_YAHRZEIT_NAME,
			];
			$result = civicrm_api('CustomField', 'getsingle', $params);
				
			if( isset ( $result['column_name'])){
				$col_name_next_heb_yahrzeit = $result['column_name'];
			}else{
				$col_name_next_heb_yahrzeit = "";
		
			}
		
		
			$params = [
					'version' => 3,
					'sequential' => 1,
					'custom_group_id' =>  $heb_cal_set_id,
					'name' => HebrewCalendar::YAH_NEXT_ENGLISH_YAHRZEIT_NAME,
			];
			$result = civicrm_api('CustomField', 'getsingle', $params);
				
			if( isset ( $result['column_name'])){
				$col_name_next_english_yahrzeit = $result['column_name'];
			}else{
				$col_name_next_english_yahrzeit = "";
			}
		
			$params = [
					'version' => 3,
					'sequential' => 1,
					'custom_group_id' =>  $heb_cal_set_id,
					'name' => HebrewCalendar::YAH_HEB_DEATH_DATE_NAME,
			];
			$result = civicrm_api('CustomField', 'getsingle', $params);
		
			if( isset ( $result['column_name'])){
				$col_name_hebrew_date_of_death = $result['column_name'];
			}else{
				$col_name_hebrew_date_of_death = "";
			}
			
			$sql = "update  ".$heb_cal_table_name." dcg SET ".
					"dcg.".$col_name_hebrew_date_of_death." = null,  ".
					"dcg.".$col_name_next_english_yahrzeit." = null, ".
					"dcg.".$col_name_next_heb_yahrzeit." = null ".
			   " WHERE 1=1 ".$contactids_sql;
			
			//$rtn_data['error_message'] = "SQL - ".$sql;
			//return $rtn_data;
			$dao_update = CRM_Core_DAO::executeQuery( $sql ) ;
			$dao_update->free();
			
		}else{
			// something is wrong, could not find the table. 
			
		}
		
		
		
		
	}

	// This is normally called whenever this extension is enabled. 
	function createExtensionConfigs(){
		$this->createRelationshipType();
		$rtn = $this->createCustomCRMConfigs();
		
		
		if(isset( $rtn['error_message']) && strlen((string) $rtn['error_message']) > 0){
			
			throw new Exception('Error creating custom CRM configs: '.$rtn['error_message']); 
		}
		
		$this->createYahrzeitTempTable();
		
		
		// calculate all Hebrew dates. ( this is helpful on re-installs and re-enablement.
		// This has no impact on fresh installs. 
		$params = [
				'version' => 3,
				'sequential' => 1,
		];
		$result = civicrm_api('AllHebrewDates', 'calculate', $params);
	 
		
	}
	
	// This is normally called whenever this extension is disabled.
	function removeExtensionConfigs(){
		$this->removeYahrzeitTempTable();
		
	}
	
	private function removeYahrzeitTempTable(){
		
		$yahrzeit_table_name =  HebrewCalendar::YAHRZEIT_TEMP_TABLE_NAME;
		
		$sql_drop = "DROP TABLE IF EXISTS ".$yahrzeit_table_name;
		
		$dao = CRM_Core_DAO::executeQuery( $sql_drop ) ;
		$dao->free();
		
	}
	
	
	private function createYahrzeitTempTable(){
		
		
		$this->removeYahrzeitTempTable();
		
		$yahrzeit_table_name =  HebrewCalendar::YAHRZEIT_TEMP_TABLE_NAME;
		
                		/*
		 *
		 */
		$sql_create = "CREATE TABLE $yahrzeit_table_name (
		id int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
		mourner_contact_id int NOT NULL,
		mourner_name varchar(500) NOT NULL,
		deceased_contact_id int NOT NULL,
		deceased_name varchar(500) NOT NULL,
		deceased_date datetime NOT NULL,
		d_before_sunset varchar(5),
		hebrew_deceased_date varchar(256),
		yahrzeit_date datetime,
		yahrzeit_hebrew_date_format_hebrew varchar(256),
		yahrzeit_hebrew_date_format_english varchar(256),
		yahrzeit_hebrew_year int(10), 
	    yahrzeit_hebrew_month int(10),
	    yahrzeit_hebrew_day int(10),
		relationship_name_formatted varchar(256),
		yahrzeit_type varchar(256),
		mourner_observance_preference varchar(256),
		plaque_location varchar(500),
		shabbat_before_parashat varchar(250),
		shabbat_before_parashat_hebrew varchar(250),
		shabbat_before_holiday varchar(250), 
		shabbat_before_holiday_hebrew varchar(250), 
		shabbat_after_parashat varchar(250),
		shabbat_after_parashat_hebrew varchar(250),
		shabbat_after_holiday varchar(250), 
		shabbat_after_holiday_hebrew varchar(250), 
		 shabbat_before_hebrew_date_format_english varchar(256),
		 shabbat_before_hebrew_year_num int(10),
	  shabbat_before_hebrew_month_num int(10),
	  shabbat_before_hebrew_day_num int(10),
		yahrzeit_erev_shabbat_before datetime,
		yahrzeit_shabbat_morning_before datetime,
		yahrzeit_erev_shabbat_after datetime,
		yahrzeit_shabbat_morning_after datetime,
		shabbat_after_hebrew_date_format_english varchar(256),
		 shabbat_after_hebrew_year_num int(10),
	  shabbat_after_hebrew_month_num int(10),
	  shabbat_after_hebrew_day_num int(10),
		yahrzeit_date_morning datetime,
		yahrzeit_relationship_id varchar(25),
		created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (`id`))
                ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ";
		


		$dao = CRM_Core_DAO::executeQuery( $sql_create ) ;
		$dao->free();
	
                $sql = " ALTER TABLE $yahrzeit_table_name
                               ADD UNIQUE KEY mourner_to_yah_date
                               (mourner_contact_id , deceased_contact_id , yahrzeit_date, yahrzeit_relationship_id, yahrzeit_type)  " ;

		$dao = CRM_Core_DAO::executeQuery( $sql ) ;
		$dao->free();

//                $sql = "ALTER TABLE $yahrzeit_table_name CHANGE `created_date` `created_date` 
//                        TIMESTAMP on update CURRENT_TIMESTAMP NULL DEFAULT NULL";

//		$dao = CRM_Core_DAO::executeQuery( $sql ) ;
//		$dao->free();


	}
	
	
	private function createRelationshipType(){
		
		// It does not make sense to have a household observe a yahrzeit, as
		// then its impossible to track how the mourner is related to the dececased person.
		// Such as "Spouse of", "Child of", etc which are commonly used in yahrzeit letters/emails.
		
		$result = civicrm_api3('RelationshipType', 'get', [
				'sequential' => 1,
				'name_a_b' => HebrewCalendar::YAHRZEIT_RELATIONSHIP_TYPE_A_B_NAME,
		]);
		
		
		if($result['is_error'] <> 0 || $result['count'] == 0  ){
			
		
				$result = civicrm_api3('RelationshipType', 'create', [
						'sequential' => 1,
						'name_a_b' => HebrewCalendar::YAHRZEIT_RELATIONSHIP_TYPE_A_B_NAME,
						'label_a_b' => HebrewCalendar::YAHRZEIT_RELATIONSHIP_TYPE_A_B_TITLE,
						'name_b_a' => HebrewCalendar::YAHRZEIT_RELATIONSHIP_TYPE_B_A_NAME,
						'label_b_a' => HebrewCalendar::YAHRZEIT_RELATIONSHIP_TYPE_B_A_TITLE,
						'contact_type_a' => "Individual",
						'contact_type_b' => "Individual",
				]);
		}else{
			// nothing to do, relationship type already exists.
			
		}
		
	}

	private function formatTableNameForCreateAPI( $input_parm ){
		
		$parm_cleaned = strtolower( str_replace( " ", "_", $input_parm) );
		
		$crmstandardprefix = "civicrm_value_"; 
		
		$api_table_name = $crmstandardprefix.$parm_cleaned;
		
		return $api_table_name; 
	}
	
   // If custom data fields, custom contact types do not exist, create them. Otherwise do nothing.
	private function createCustomCRMConfigs(){
		
		$tmp = [];
		
		$individual_type_id = "";
		// Get id of contact_type 'Individual' , needed for API call.
		$result = civicrm_api3('ContactType', 'get', [
				'sequential' => 1,
				'name' => "Individual",
		]);
		
		if($result['is_error'] == 0 && $result['count'] == 1  ){
			$individual_type_id = $result['id'];
			
			
		}else{
			$tmp['error_message'] = "Error: Could not find core contact_type 'Individual' ";
			return;
		}
		
		
		// Make sure custom contact type 'Deceased' exists. 
		$result = civicrm_api3('ContactType', 'get', [
				'sequential' => 1,
				'name' => "Deceased",
		]);
		
		if($result['is_error'] <> 0 || $result['count'] == 0  ){
			$result = civicrm_api3('ContactType', 'create', [
					'sequential' => 1,
					'name' => HebrewCalendar::YAH_DECEASED_CONTACT_TYPE_NAME,
					'label' => HebrewCalendar::YAH_DECEASED_CONTACT_TYPE_TITLE,
					'parent_id' => $individual_type_id,
			]);
				
			
			
		}
		
		
		
		
		$result = civicrm_api3('CustomGroup', 'get', [
				'sequential' => 1,
				'name' => HebrewCalendar::EXTENDED_DATE_CUSTOM_FIELD_GROUP_NAME,
		]);
		
		// TODO: Check that it extends individual. 
		
		$tmp_api_table_name = $this->formatTableNameForCreateAPI( HebrewCalendar::EXTENDED_DATE_CUSTOM_FIELD_GROUP_NAME ); 
		
		
		if($result['is_error'] <> 0 || $result['count'] == 0  ){
			// create it custom data set.
			$create_result = civicrm_api3('CustomGroup', 'create', [
					'sequential' => 1,
					'title' => HebrewCalendar::EXTENDED_DATE_CUSTOM_FIELD_GROUP_TITLE,
					'extends' => "Individual",
					'name' => HebrewCalendar::EXTENDED_DATE_CUSTOM_FIELD_GROUP_NAME,
					'table_name' => $tmp_api_table_name, 
					'help_pre' => "",
					'weight' => 1, 
			]);
				
		}else{
				
			// Nothing to do. 	
				
		}
		
		
		// Check if birth custom field exists, create it if needed.
		$result = civicrm_api3('CustomField', 'get', [
				'sequential' => 1,
				'custom_group_id' => HebrewCalendar::EXTENDED_DATE_CUSTOM_FIELD_GROUP_NAME,
				'name' => HebrewCalendar::EXTENDED_DATE_CUSTOM_FIELD_BIRTH_NAME,
		]);
		
		if($result['is_error'] <> 0 || $result['count'] == 0  ){
			$result = civicrm_api3('CustomField', 'create', [
					'sequential' => 1,
					'custom_group_id' => HebrewCalendar::EXTENDED_DATE_CUSTOM_FIELD_GROUP_NAME,
					'label' => HebrewCalendar::EXTENDED_DATE_CUSTOM_FIELD_BIRTH_TITLE,
					'name' => HebrewCalendar::EXTENDED_DATE_CUSTOM_FIELD_BIRTH_NAME,
					'data_type' => "Boolean",
					'html_type' => "Radio",
					'is_searchable' => "1",
			]);
		
		}
		
		
		// Check if death custom field exists, create it if needed.
		$result = civicrm_api3('CustomField', 'get', [
				'sequential' => 1,
				'custom_group_id' => HebrewCalendar::EXTENDED_DATE_CUSTOM_FIELD_GROUP_NAME,
				'name' => HebrewCalendar::EXTENDED_DATE_CUSTOM_FIELD_DEATH_NAME,
		]);
		
		if($result['is_error'] <> 0 || $result['count'] == 0  ){
			$result = civicrm_api3('CustomField', 'create', [
					'sequential' => 1,
					'custom_group_id' => HebrewCalendar::EXTENDED_DATE_CUSTOM_FIELD_GROUP_NAME,
					'label' => HebrewCalendar::EXTENDED_DATE_CUSTOM_FIELD_DEATH_TITLE,
					'name' => HebrewCalendar::EXTENDED_DATE_CUSTOM_FIELD_DEATH_NAME,
					'data_type' => "Boolean",
					'html_type' => "Radio",
					'is_searchable' => "1",
			]);
		
		}
		
		
		// if needed, Create custom data set used for "relationship type" = "yahrzeit observed in memory of"
		
		$result = civicrm_api3('CustomGroup', 'get', [
				'sequential' => 1,
				'name' => HebrewCalendar::YAH_RELATIONSHIPTYPE_CUSTOM_FIELD_GROUP_NAME,
		]);
		
		// TODO: Check that it extends correct relationship type.		
		
		if($result['is_error'] <> 0 || $result['count'] == 0  ){
			// we need to create it. But first we need to get the relationship type id.
			$reltype_result = civicrm_api3('RelationshipType', 'get', [
					'sequential' => 1,
					'name_a_b' => HebrewCalendar::YAHRZEIT_RELATIONSHIP_TYPE_A_B_NAME,
			]);
			
			$yah_relationtype_id = "";
			if($reltype_result['is_error'] <> 0 || $reltype_result['count'] > 0  ){
				$tmp_reltype = $reltype_result['values'][0];
				$yah_relationtype_id = $tmp_reltype['id'];
			
				$tmp_api_table_name = $this->formatTableNameForCreateAPI( HebrewCalendar::YAH_RELATIONSHIPTYPE_CUSTOM_FIELD_GROUP_NAME);
			
				// finally, we can create the custom data set.
				$create_result = civicrm_api3('CustomGroup', 'create', [
						'sequential' => 1,
						'title' => HebrewCalendar::YAH_RELATIONSHIPTYPE_CUSTOM_FIELD_GROUP_TITLE,
						'extends' => "Relationship",
						'extends_entity_column_value' => $yah_relationtype_id, 
						'name' => HebrewCalendar::YAH_RELATIONSHIPTYPE_CUSTOM_FIELD_GROUP_NAME,
						'table_name' => $tmp_api_table_name, 
						'help_pre' => "",
						'weight' => 2, 
				]);
				
			}else{
				// custom relationship type does not exist. So we cannot create the custom field group.
				
			}
			
		}else{
		
			// Nothing to do.
		
		}
		
		// see if the custom field "mourner observes the English date" exists. If not, create it.
		// Check if death custom field exists, create it if needed.
		$result = civicrm_api3('CustomField', 'get', [
				'sequential' => 1,
				'custom_group_id' => HebrewCalendar::YAH_RELATIONSHIPTYPE_CUSTOM_FIELD_GROUP_NAME,
				'name' => HebrewCalendar::YAH_RELATIONSHIPTYPE_CUSTOM_FIELD_NAME,
		]);
		
		if($result['is_error'] <> 0 || $result['count'] == 0  ){
			$result = civicrm_api3('CustomField', 'create', [
					'sequential' => 1,
					'custom_group_id' => HebrewCalendar::YAH_RELATIONSHIPTYPE_CUSTOM_FIELD_GROUP_NAME,
					'label' => HebrewCalendar::YAH_RELATIONSHIPTYPE_CUSTOM_FIELD_TITLE,
					'name' => HebrewCalendar::YAH_RELATIONSHIPTYPE_CUSTOM_FIELD_NAME,
					'data_type' => "Boolean",
					'html_type' => "Radio",
					'is_searchable' => "1",
			]);
		
		}
		
		
		// if needed, Create custom data set for contacts of type 'Deceased'
		$result = civicrm_api3('CustomGroup', 'get', [
				'sequential' => 1,
				'name' => HebrewCalendar::YAH_DECEASED_CUSTOM_FIELD_GROUP_NAME,
		]);
		
		// TODO: Check that it extends correct contact type.
		
		if($result['is_error'] <> 0 || $result['count'] == 0  ){
			// we need to create it. But first we need to verify the contact type we are using exists.
			$con_type_result = civicrm_api3('ContactType', 'get', [
					'sequential' => 1,
					'name' => HebrewCalendar::YAH_DECEASED_CONTACT_TYPE_NAME,
			]);
				
			//$yah_contact_type_id = "";
			if($con_type_result['is_error'] <> 0 || $con_type_result['count'] > 0  ){
					
				$tmp_api_table_name = $this->formatTableNameForCreateAPI( HebrewCalendar::YAH_DECEASED_CUSTOM_FIELD_GROUP_NAME);
				
				// finally, we can create the custom data set.
				$create_result = civicrm_api3('CustomGroup', 'create', [
						'sequential' => 1,
						'title' => HebrewCalendar::YAH_DECEASED_CUSTOM_FIELD_GROUP_TITLE,
						'extends' => "Individual",  
						'extends_entity_column_value' => HebrewCalendar::YAH_DECEASED_CONTACT_TYPE_NAME,
						'name' => HebrewCalendar::YAH_DECEASED_CUSTOM_FIELD_GROUP_NAME,
						'help_pre' => "All fields in this area are calculated automatically.",
						'table_name' => $tmp_api_table_name, 
						'weight' => 2,
				]);
				
		
			}else{
				// custom contact type for 'Deceased' does not exist,
				// So we cannot create the custom field group.
		
			}
			
				
		}else{
		
			// Nothing to do.
		
		}
		
		// if needed,  create custom fields for above group
		$result = civicrm_api3('CustomField', 'get', [
				'sequential' => 1,
				'custom_group_id' => HebrewCalendar::YAH_DECEASED_CUSTOM_FIELD_GROUP_NAME,
				'name' => HebrewCalendar::YAH_NEXT_HEB_YAHRZEIT_NAME,
		]);
		
		if($result['is_error'] <> 0 || $result['count'] == 0  ){
			$result = civicrm_api3('CustomField', 'create', [
					'sequential' => 1,
					'custom_group_id' => HebrewCalendar::YAH_DECEASED_CUSTOM_FIELD_GROUP_NAME,
					'label' => HebrewCalendar::YAH_NEXT_HEB_YAHRZEIT_TITLE,
					'name' => HebrewCalendar::YAH_NEXT_HEB_YAHRZEIT_NAME,
					'data_type' => "Date",
					'html_type' => "Select Date",
					'is_required' => "0",
					'is_searchable' => "1",
					'is_search_range' =>  "1",
					'is_view' => 1,
					'mask' => "MM dd, yyyy",
					'date_format' => "MM d, yy",
					'help_pre' => "",
					'help_post' => "",
					
			]);
		
		}
		
		//
		$result = civicrm_api3('CustomField', 'get', [
				'sequential' => 1,
				'custom_group_id' => HebrewCalendar::YAH_DECEASED_CUSTOM_FIELD_GROUP_NAME,
				'name' => HebrewCalendar::YAH_NEXT_ENGLISH_YAHRZEIT_NAME,
		]);
		
		if($result['is_error'] <> 0 || $result['count'] == 0  ){
			$result = civicrm_api3('CustomField', 'create', [
					'sequential' => 1,
					'custom_group_id' => HebrewCalendar::YAH_DECEASED_CUSTOM_FIELD_GROUP_NAME,
					'label' => HebrewCalendar::YAH_NEXT_ENGLISH_YAHRZEIT_TITLE,
					'name' => HebrewCalendar::YAH_NEXT_ENGLISH_YAHRZEIT_NAME,
					'data_type' => "Date",
					'html_type' => "Select Date",
					'is_required' => "0",
					'is_searchable' => "1",
					'is_search_range' =>  "1",
					'is_view' => 1,
					'mask' => "MM dd, yyyy",
					'date_format' => "MM d, yy",
					'help_pre' => "",
					'help_post' => "",
						
			]);
		
		}
		
		//
		$result = civicrm_api3('CustomField', 'get', [
				'sequential' => 1,
				'custom_group_id' => HebrewCalendar::YAH_DECEASED_CUSTOM_FIELD_GROUP_NAME,
				'name' => HebrewCalendar::YAH_HEB_DEATH_DATE_NAME,
		]);
		
		if($result['is_error'] <> 0 || $result['count'] == 0  ){
			$result = civicrm_api3('CustomField', 'create', [
					'sequential' => 1,
					'custom_group_id' => HebrewCalendar::YAH_DECEASED_CUSTOM_FIELD_GROUP_NAME,
					'label' => HebrewCalendar::YAH_HEB_DEATH_DATE_TITLE,
					'name' => HebrewCalendar::YAH_HEB_DEATH_DATE_NAME,
					'data_type' => "String",
					'html_type' => "Text",
					'is_required' => "0",
					'is_searchable' => "1",
					'is_view' => 1,
					'help_pre' => "",
					'help_post' => "",
		
			]);
		
		}
		
		
		// if needed, Create custom data set for tracking Hebrew birthdays
		$result = civicrm_api3('CustomGroup', 'get', [
				'sequential' => 1,
				'name' => HebrewCalendar::HEB_BIRTH_CUSTOM_FIELD_GROUP_NAME,
		]);
		
		// TODO: Check that it extends correct contact type.
		
		if($result['is_error'] <> 0 || $result['count'] == 0  ){
			
			$tmp_api_table_name = $this->formatTableNameForCreateAPI( HebrewCalendar::HEB_BIRTH_CUSTOM_FIELD_GROUP_NAME);
			
			
			$create_result = civicrm_api3('CustomGroup', 'create', [
					'sequential' => 1,
					'title' => HebrewCalendar::HEB_BIRTH_CUSTOM_FIELD_GROUP_TITLE,
					'extends' => "Individual",
					'name' => HebrewCalendar::HEB_BIRTH_CUSTOM_FIELD_GROUP_NAME,
					'help_pre' => "All fields in this area are calculated automatically.",
					'table_name' => $tmp_api_table_name, 
					'weight' => 3,
			]);
			
			
			
		}else{
			// nothing to do
		}
		
		
		//
		$result = civicrm_api3('CustomField', 'get', [
				'sequential' => 1,
				'custom_group_id' => HebrewCalendar::HEB_BIRTH_CUSTOM_FIELD_GROUP_NAME,
				'name' => HebrewCalendar::HEB_BIRTH_DATE_NAME,
		]);
		
		if($result['is_error'] <> 0 || $result['count'] == 0  ){
			$result = civicrm_api3('CustomField', 'create', [
					'sequential' => 1,
					'custom_group_id' => HebrewCalendar::HEB_BIRTH_CUSTOM_FIELD_GROUP_NAME,
					'label' =>  HebrewCalendar::HEB_BIRTH_DATE_TITLE,
					'name' => HebrewCalendar::HEB_BIRTH_DATE_NAME,
					'data_type' => "String",
					'html_type' => "Text",
					'is_required' => "0",
					'is_searchable' => "1",
					'is_view' => 1,
					'help_pre' => "",
					'help_post' => "",
		
			]);
		
		}
		
		
		$result = civicrm_api3('CustomField', 'get', [
				'sequential' => 1,
				'custom_group_id' => HebrewCalendar::HEB_BIRTH_CUSTOM_FIELD_GROUP_NAME,
				'name' => HebrewCalendar::HEB_NEXT_BIRTHDAY_NAME,
		]);
		
		if($result['is_error'] <> 0 || $result['count'] == 0  ){
			$result = civicrm_api3('CustomField', 'create', [
					'sequential' => 1,
					'custom_group_id' => HebrewCalendar::HEB_BIRTH_CUSTOM_FIELD_GROUP_NAME,
					'label' =>  HebrewCalendar::HEB_NEXT_BIRTHDAY_TITLE,
					'name' => HebrewCalendar::HEB_NEXT_BIRTHDAY_NAME,
					'data_type' => "Date",
					'html_type' => "Select Date",
					'is_required' => "0",
					'is_searchable' => "1",
					'is_search_range' =>  "1",
					'is_view' => 1,
					'mask' => "MM dd, yyyy",
					'date_format' => "MM d, yy",
					'help_pre' => "",
					'help_post' => "",
		
			]);
		
		}
		
		$result = civicrm_api3('CustomField', 'get', [
				'sequential' => 1,
				'custom_group_id' => HebrewCalendar::HEB_BIRTH_CUSTOM_FIELD_GROUP_NAME,
				'name' => HebrewCalendar::HEB_EARLIEST_BARBAT_MITZVAH_NAME,
		]);
		
		if($result['is_error'] <> 0 || $result['count'] == 0  ){
			$result = civicrm_api3('CustomField', 'create', [
					'sequential' => 1,
					'custom_group_id' => HebrewCalendar::HEB_BIRTH_CUSTOM_FIELD_GROUP_NAME,
					'label' =>  HebrewCalendar::HEB_EARLIEST_BARBAT_MITZVAH_TITLE,
					'name' => HebrewCalendar::HEB_EARLIEST_BARBAT_MITZVAH_NAME,
					'data_type' => "Date",
					'html_type' => "Select Date",
					'is_required' => "0",
					'is_searchable' => "1",
					'is_search_range' =>  "1",
					'is_view' => 1,
					'mask' => "MM dd, yyyy",
					'date_format' => "MM d, yy",
					'help_pre' => "",
					'help_post' => "",
		
			]);
		
		}
		
		// if needed, create custom field group for tracking Religious information. 
		$result = civicrm_api3('CustomGroup', 'get', [
				'sequential' => 1,
				'name' => HebrewCalendar::RELIGIOUS_CUSTOM_FIELD_GROUP_NAME,
		]);
		
		// TODO: Check that it extends correct contact type.
		
		if($result['is_error'] <> 0 || $result['count'] == 0  ){
				
			$tmp_api_table_name = $this->formatTableNameForCreateAPI( HebrewCalendar::RELIGIOUS_CUSTOM_FIELD_GROUP_NAME);
				
				
			$create_result = civicrm_api3('CustomGroup', 'create', [
					'sequential' => 1,
					'title' => HebrewCalendar::RELIGIOUS_CUSTOM_FIELD_GROUP_TITLE,
					'extends' => "Individual",
					'name' => HebrewCalendar::RELIGIOUS_CUSTOM_FIELD_GROUP_NAME,
					'help_pre' => "",
					'table_name' => $tmp_api_table_name,
					'weight' => 3,
			]);
				
				
				
		}else{
			// nothing to do
		}
		
		// add fields to Religious field set
		//
		$result = civicrm_api3('CustomField', 'get', [
				'sequential' => 1,
				'custom_group_id' => HebrewCalendar::RELIGIOUS_CUSTOM_FIELD_GROUP_NAME,
				'name' => HebrewCalendar::HEB_NAME_FIELD_NAME,
		]);
		
		if($result['is_error'] <> 0 || $result['count'] == 0  ){
			$result = civicrm_api3('CustomField', 'create', [
					'sequential' => 1,
					'custom_group_id' => HebrewCalendar::RELIGIOUS_CUSTOM_FIELD_GROUP_NAME,
					'label' =>  HebrewCalendar::HEB_NAME_FIELD_TITLE,
					'name' => HebrewCalendar::HEB_NAME_FIELD_NAME,
					'data_type' => "String",
					'html_type' => "Text",
					'is_required' => "0",
					'is_searchable' => "1",
					'help_pre' => "", 
					'help_post' => "Example:  David or דוד",
		
			]);
		
		}
		
		//
		$result = civicrm_api3('CustomField', 'get', [
				'sequential' => 1,
				'custom_group_id' => HebrewCalendar::RELIGIOUS_CUSTOM_FIELD_GROUP_NAME,
				'name' => HebrewCalendar::HEB_NAME_PARENTS_FIELD_NAME,
		]);
		
		if($result['is_error'] <> 0 || $result['count'] == 0  ){
			$result = civicrm_api3('CustomField', 'create', [
					'sequential' => 1,
					'custom_group_id' => HebrewCalendar::RELIGIOUS_CUSTOM_FIELD_GROUP_NAME,
					'label' =>  HebrewCalendar::HEB_NAME_PARENTS_FIELD_TITLE,
					'name' => HebrewCalendar::HEB_NAME_PARENTS_FIELD_NAME,
					'data_type' => "String",
					'html_type' => "Text",
					'is_required' => "0",
					'is_searchable' => "1",
					'help_pre' => "", 
					'help_post' => "Example: Ben Moshe v'Sarah or בן משה ושרה",
		
			]);
		
		}
		
		//
		$result = civicrm_api3('CustomField', 'get', [
				'sequential' => 1,
				'custom_group_id' => HebrewCalendar::RELIGIOUS_CUSTOM_FIELD_GROUP_NAME,
				'name' => HebrewCalendar::IS_JEWISH_FIELD_NAME,
		]);
		
		if($result['is_error'] <> 0 || $result['count'] == 0  ){
			$result = civicrm_api3('CustomField', 'create', [
					'sequential' => 1,
					'custom_group_id' => HebrewCalendar::RELIGIOUS_CUSTOM_FIELD_GROUP_NAME,
					'label' =>  HebrewCalendar::IS_JEWISH_FIELD_TITLE,
					'name' => HebrewCalendar::IS_JEWISH_FIELD_NAME,
					'data_type' => "Boolean",
					'html_type' => "Radio",
					'is_required' => "0",
					'is_searchable' => "1",
					'help_pre' => "",
					'help_post' => "", 
		
			]);
		
		}
		
		/*
		 * // Tribe (select list) // options: 'Israelite' , 'Levi' , 'Cohen' 
	
	// Tribe field type: String, Select
	// Tribe choices:  Cohen, Israelite, Levi
	const RELIGIOUS_TRIBE_FIELD_NAME = "Tribe";
	const RELIGIOUS_TRIBE_FIELD_TITLE = "Tribe";
	
	const RELIGIOUS_TRIBE_OPTIONS_NAME = "tribe_options";
		 */
		$result = civicrm_api3('CustomField', 'get', [
				'sequential' => 1,
				'custom_group_id' => HebrewCalendar::RELIGIOUS_CUSTOM_FIELD_GROUP_NAME,
				'name' => HebrewCalendar::RELIGIOUS_TRIBE_FIELD_NAME,
		]);
		
		if($result['is_error'] <> 0 || $result['count'] == 0  ){
			// 'Tribe' does not exist yet, create the options, then create the field 'Tribe' 
			
			$tribe_options_array = [];
			
			$tribe_options_array['Israelite'] =  "Israelite";
			$tribe_options_array['Levi'] =  "Levi";
			$tribe_options_array['Cohen'] =  "Cohen";
			
			require_once( "utils/ConfigHelper.php");
			$tmpConfigHelper = new ConfigHelper();
			$tribe_grp_option_id = $tmpConfigHelper->create_option_group( HebrewCalendar::RELIGIOUS_TRIBE_OPTIONS_NAME , 
					HebrewCalendar::RELIGIOUS_TRIBE_OPTIONS_NAME, 
					$tribe_options_array );
			
			if(strlen((string) $tribe_grp_option_id) > 0  ){
				$result = civicrm_api3('CustomField', 'create', [
						'sequential' => 1,
						'custom_group_id' => HebrewCalendar::RELIGIOUS_CUSTOM_FIELD_GROUP_NAME,
						'label' =>  HebrewCalendar::RELIGIOUS_TRIBE_FIELD_TITLE,
						'name' => HebrewCalendar::RELIGIOUS_TRIBE_FIELD_NAME,
						'data_type' => "String",
						'html_type' => "Select",
						'option_group_id' => $tribe_grp_option_id ,
						'is_required' => "0",
						'is_searchable' => "1",
						'help_pre' => "",
						'help_post' => "",
						'weight' => 4,
				
				]);
				
			}else{
				$tmp['error_message'] = "Error: Could NOT find id for the option_group named  '".
				HebrewCalendar::RELIGIOUS_TRIBE_OPTIONS_NAME."' as a result, could not create the custom field for Tribe.";
				return;
				
			}
			
		}
		
	
		// if needed, create plaque custom field set
		$result = civicrm_api3('CustomGroup', 'get', [
				'sequential' => 1,
				'name' => HebrewCalendar::PLAQUE_CUSTOM_FIELD_GROUP_NAME,
		]);
		
		// TODO: Check that it extends correct contact type.
		//CRM_Core_Error::debug("Debug: Just checked if '".HebrewCalendar::PLAQUE_CUSTOM_FIELD_GROUP_NAME."' set exists. : ",  $result );
		//CRM_Core_Error::log("Debug: Just checked if '".HebrewCalendar::PLAQUE_CUSTOM_FIELD_GROUP_NAME."' set exists. : ",  $result );
		if($result['is_error'] <> 0 || $result['count'] == 0  ){
		
			$tmp_api_table_name = $this->formatTableNameForCreateAPI( HebrewCalendar::PLAQUE_CUSTOM_FIELD_GROUP_NAME);
		
		
			$create_result = civicrm_api3('CustomGroup', 'create', [
					'sequential' => 1,
					'title' => HebrewCalendar::PLAQUE_CUSTOM_FIELD_GROUP_TITLE,
					'extends' => "Individual",
					'extends_entity_column_value' => HebrewCalendar::YAH_DECEASED_CONTACT_TYPE_NAME,
					'name' => HebrewCalendar::PLAQUE_CUSTOM_FIELD_GROUP_NAME,
					'help_pre' => "",
					'table_name' => $tmp_api_table_name,
					'weight' => 3,
			]);
		
		
		
		}else{
			// nothing to do
		}
		
		// add custom fields to Plaque Info set
		
		
		// // HAS_PLAQUE_FIELD_NAME
		$result = civicrm_api3('CustomField', 'get', [
				'sequential' => 1,
				'custom_group_id' => HebrewCalendar::PLAQUE_CUSTOM_FIELD_GROUP_NAME,
				'name' => HebrewCalendar::HAS_PLAQUE_FIELD_NAME,
		]);
		
		if($result['is_error'] <> 0 || $result['count'] == 0  ){
			$result = civicrm_api3('CustomField', 'create', [
					'sequential' => 1,
					'custom_group_id' => HebrewCalendar::PLAQUE_CUSTOM_FIELD_GROUP_NAME,
					'label' =>  HebrewCalendar::HAS_PLAQUE_FIELD_TITLE,
					'name' => HebrewCalendar::HAS_PLAQUE_FIELD_NAME,
					'data_type' => "Boolean",
					'html_type' => "Radio",
					'is_required' => "0",
					'is_searchable' => "1",
					'help_pre' => "",
					'help_post' => "",
		
			]);
		
		}
		
		
		
		
		//
		$result = civicrm_api3('CustomField', 'get', [
				'sequential' => 1,
				'custom_group_id' => HebrewCalendar::PLAQUE_CUSTOM_FIELD_GROUP_NAME,
				'name' => HebrewCalendar::PLAQUE_LOCATION_NAME,
		]);
		
		if($result['is_error'] <> 0 || $result['count'] == 0  ){
			$result = civicrm_api3('CustomField', 'create', [
					'sequential' => 1,
					'custom_group_id' => HebrewCalendar::PLAQUE_CUSTOM_FIELD_GROUP_NAME,
					'label' =>  HebrewCalendar::PLAQUE_LOCATION_TITLE,
					'name' => HebrewCalendar::PLAQUE_LOCATION_NAME,
					'data_type' => "String",
					'html_type' => "Text",
					'is_required' => "0",
					'is_searchable' => "1",
					'help_pre' => "",
					'help_post' => "",
		
			]);
		
		}
		
		
		
		
	}
	
	
	
	
	function calculateBirthDates( $extended_dates_table_name,    $contact_ids){
	
		$eb_result = civicrm_api3('CustomField', 'get', [
				'sequential' => 1,
				'custom_group_id' => HebrewCalendar::EXTENDED_DATE_CUSTOM_FIELD_GROUP_NAME,
				'name' => HebrewCalendar::EXTENDED_DATE_CUSTOM_FIELD_BIRTH_NAME ,
		]);
		
		if($eb_result['is_error'] <> 0 || $eb_result['count'] == 0  ){
			$rtn_data['error_message'] = "Could not find custom field: '".HebrewCalendar::EXTENDED_DATE_CUSTOM_FIELD_BIRTH_NAME."' ";
			return $rtn_data;
		
		}else{
				
			$tmp_values = $eb_result['values'][0];
			 $extended_birth_date_col_name = $tmp_values['column_name'];
			$extended_birth_custom_field_id = $tmp_values['id'];
		
		
		}
		
		if(  strlen((string) $extended_birth_date_col_name) == 0 ){
			$rtn_data['error_message'] = "Could not get SQL column name for '".HebrewCalendar::EXTENDED_DATE_CUSTOM_FIELD_BIRTH_NAME."'";
			return $rtn_data;
		}
		
		// done with validation steps
		
		
		
		if( strlen((string) $contact_ids) > 0){
			$contactids_sql = " AND c.id IN ( $contact_ids )";
	
		}else{
			$contactids_sql = "";
		}
	
		$result = civicrm_api3('CustomField', 'get', [
				'sequential' => 1,
				'custom_group_id' => HebrewCalendar::HEB_BIRTH_CUSTOM_FIELD_GROUP_NAME,
				'name' => HebrewCalendar::HEB_BIRTH_DATE_NAME,
		]);
	
		if($result['is_error'] == 0 && $result['count'] == 1  ){
				
				
			$tmp_id = $result['values'][0]['id'];    //$result['id'];
			$api_name_heb_birth_date =   'custom_'.$tmp_id;
	
		}else{
			$rtn_data['error_message'] = "Error: Missing custom field: '".HebrewCalendar::HEB_BIRTH_DATE_NAME."'";
			return $rtn_data;
		}
	
	// $api_name_earliest_barbat
		$result = civicrm_api3('CustomField', 'get', [
				'sequential' => 1,
				'custom_group_id' => HebrewCalendar::HEB_BIRTH_CUSTOM_FIELD_GROUP_NAME,
				'name' => HebrewCalendar::HEB_EARLIEST_BARBAT_MITZVAH_NAME,
		]);
		
		if($result['is_error'] == 0 && $result['count'] == 1  ){
		
		
			$tmp_id = $result['values'][0]['id'];    //$result['id'];
			$api_name_earliest_barbat =   'custom_'.$tmp_id;
		
		}else{
			$rtn_data['error_message'] = "Error: Missing custom field: '".HebrewCalendar::HEB_EARLIEST_BARBAT_MITZVAH_NAME."'";
			return $rtn_data;
		}
		
		// $api_name_next_hebrew_birthday
		$result = civicrm_api3('CustomField', 'get', [
				'sequential' => 1,
				'custom_group_id' => HebrewCalendar::HEB_BIRTH_CUSTOM_FIELD_GROUP_NAME,
				'name' => HebrewCalendar::HEB_NEXT_BIRTHDAY_NAME,
		]);
		
		if($result['is_error'] == 0 && $result['count'] == 1  ){
		
		
			$tmp_id = $result['values'][0]['id'];    //$result['id'];
			$api_name_next_hebrew_birthday =   'custom_'.$tmp_id;
		
		}else{
			$rtn_data['error_message'] = "Error: Missing custom field: '".HebrewCalendar::HEB_NEXT_BIRTHDAY_NAME."'";
			return $rtn_data;
		}
		
		
		
		
		
		$birth_year = null;
		$birth_month = null;
		$birth_day = null;
		$birth_date_before_sunset = null;
	
		$sql = "select c.id as contact_id ,
	  		year(c.birth_date) as birth_year,
		month(c.birth_date) as birth_month,
		day(c.birth_date) as birth_day,
	  		civicrm_option_value.name as gender_label,
	  		 edt.".$extended_birth_date_col_name." as birth_date_before_sunset
	  		  FROM civicrm_contact c
	  		  LEFT JOIN ".$extended_dates_table_name." edt ON c.id = edt.entity_id
	  		  LEFT JOIN civicrm_option_value ON civicrm_option_value.option_group_id = (SELECT id FROM civicrm_option_group WHERE name = 'gender') AND c.gender_id = civicrm_option_value.value
	          where c.is_deleted <> 1 AND
	  		  		c.contact_type = 'Individual' AND
	  		  		c.birth_date is not null ".$contactids_sql;
		 
		 
		 
		$contacts_updated = 0;
		$dao = CRM_Core_DAO::executeQuery( $sql ) ;
		while ( $dao->fetch( ) ) {
			$contact_id = $dao->contact_id ;
			$birth_year = $dao->birth_year;
			$birth_month = $dao->birth_month;
			$birth_day = $dao->birth_day;
			$birth_date_before_sunset = $dao->birth_date_before_sunset;
			$gender = $dao->gender_label;
	
						
			$hebrew_date_format =  'dd MM yy';
			$hebrew_birth_date_formated = self::util_convert2hebrew_date($birth_year, $birth_month, $birth_day, $birth_date_before_sunset, $hebrew_date_format);
			
			// $rtn_data["hebrew_date_of_birth"] = $hebrew_birth_date_formated;
				
			$heb_date_format = 'hebrew';
			$hebrew_birth_date_formated_as_hebrew = self::util_convert2hebrew_date($birth_year, $birth_month, $birth_day, $birth_date_before_sunset, $heb_date_format);
			
			// $rtn_data["hebrew_date_of_birth_hebrew"] = $hebrew_birth_date_formated_as_hebrew;
				
				/*
			$config = CRM_Core_Config::singleton( );
				
			$tmp_system_date_format = 	$config->dateInputFormat;
			if($tmp_system_date_format == 'dd/mm/yy'){
				$gregorian_date_format = "dd MM yyyy" ;
				 
			}else if($tmp_system_date_format == 'mm/dd/yy' ){
				$gregorian_date_format = "MM dd, yyyy";
				 
			}else{
	
				$rtn_data['error_message'] = "Configuration Issue: Unrecognized System date format: ".$tmp_system_date_format;
				return $rtn_data ;
	
			}
			*/
			
	
			$erev_start_flag = '1';
			$gregorian_date_format = "MM dd, yyyy";
			if($gender == 'Male'){
				$bar_bat_mitzvah_flag = "bar";
				// $bar_bat_label = "Bar Mitzvah";  // Son of the commandments.
			}else if( $gender == 'Female' ){
				$bar_bat_mitzvah_flag = "bat";
				// $bar_bat_label = "Bat Mitzvah";  // Daughter of the commandments.
			}else{
				// unrecognized gender option, treat as 'bar' which means person must be 13 or older.
				$bar_bat_mitzvah_flag = "bar";
				// $bar_bat_label = "B'nai Mitzvah";  // Technically this is the plural, ie "Sons and daughters of the commandments" . Need input from community on this.
			}
			
			
			$tmp_date_gregorian_format = 'crm_api';
			
			$bat_mitzvah_date_formated = self::util_get_bar_bat_mizvah_date($birth_year, $birth_month, $birth_day, $birth_date_before_sunset, $erev_start_flag, $bar_bat_mitzvah_flag, $tmp_date_gregorian_format);
			
			$next_hebrew_birthday_formated = self::util_get_next_hebrew_birthday_date($birth_year, $birth_month, $birth_day, $birth_date_before_sunset, $erev_start_flag,  $tmp_date_gregorian_format);
			
			/*
			if( $contact_id == 4){
				$rtn_data['error_message'] = "Debug:  $contact_id next heb birthday: ".$next_hebrew_birthday_formated;
				return $rtn_data;
			}
			*/
			//$rtn_data["bar_bat_mitzvah_label"] =  $bar_bat_label ;
			//$rtn_data["earliest_bar_bat_mitzvah_date"]  = $bat_mitzvah_date_formated ;
			
			
			// Now update the contact with the newly calculated values.
			/*
			$api_parms = array(
					'sequential' => 1,
					'id' =>  $contact_id,
			);
			
			
			$api_parms[$api_name_heb_birth_date] = $hebrew_birth_date_formated;		
		
			
			if( is_numeric(substr( $bat_mitzvah_date_formated, 0 , 4)) ){
				
				$api_parms[$api_name_earliest_barbat] = $bat_mitzvah_date_formated;
			}
			
			if( is_numeric(substr( $next_hebrew_birthday_formated, 0 , 4)) ){
			
				$api_parms[$api_name_next_hebrew_birthday] = $next_hebrew_birthday_formated;
			}
			
			*/
			/*
			 * $api_name_heb_birth_date => $hebrew_birth_date_formated,
					$api_name_earliest_barbat => $bat_mitzvah_date_formated,
			 */
			
			
			// Do NOT use CiviCRM API here, use SQL. This is because using API creates a infinite loop because this is called from a hook
			$rtn_code  = $this->updateCiviCRMCalcBirthdayFields(  $contact_id, $hebrew_birth_date_formated, $bat_mitzvah_date_formated, $next_hebrew_birthday_formated ) ;
			
			if( $rtn_code > 0 ){
				$contacts_updated = $contacts_updated + 1;
			}
			//$result = civicrm_api3('Contact', 'create', $api_parms);
			
			//if( $result['is_error'] == 0 && $result['count'] == 1){
			//	$contacts_updated = $contacts_updated + 1;
	
			//}
				
		}
		$dao->free();
		 
		$rtn_data['contacts_updated_birthdays'] = $contacts_updated;
		return $rtn_data;
		 
	}
	
	function updateCiviCRMCalcYahrzeitFields( &$deceased_contact_id, &$yahrzeit_date_tmp_next, &$tmp_yahrzeit_date_observe_english_next, &$hebrew_deceased_date  ){
		
		$params = [
				'version' => 3,
				'sequential' => 1,
				'name' => HebrewCalendar::YAH_DECEASED_CUSTOM_FIELD_GROUP_NAME,
		];
		$result = civicrm_api('CustomGroup', 'getsingle', $params);
		
		if(isset($result['table_name'])){
			$heb_cal_table_name = $result['table_name'];
			$heb_cal_set_id = $result['id'];
		}else{
			$heb_cal_table_name = "";
		}
		
		if( strlen((string) $heb_cal_table_name) > 0){
		
		
			$params = [
					'version' => 3,
					'sequential' => 1,
					'custom_group_id' =>  $heb_cal_set_id,
					'name' => HebrewCalendar::YAH_NEXT_HEB_YAHRZEIT_NAME,
			];
			$result = civicrm_api('CustomField', 'getsingle', $params);
				
			if( isset ( $result['column_name'])){
				$col_name_next_heb_yahrzeit = $result['column_name'];
			}else{
				$col_name_next_heb_yahrzeit = "";
		
			}
		
		
			$params = [
					'version' => 3,
					'sequential' => 1,
					'custom_group_id' =>  $heb_cal_set_id,
					'name' => HebrewCalendar::YAH_NEXT_ENGLISH_YAHRZEIT_NAME,
			];
			$result = civicrm_api('CustomField', 'getsingle', $params);
				
			if( isset ( $result['column_name'])){
				$col_name_next_english_yahrzeit = $result['column_name'];
			}else{
				$col_name_next_english_yahrzeit = "";
			}
		
			$params = [
					'version' => 3,
					'sequential' => 1,
					'custom_group_id' =>  $heb_cal_set_id,
					'name' => HebrewCalendar::YAH_HEB_DEATH_DATE_NAME,
			];
			$result = civicrm_api('CustomField', 'getsingle', $params);
		
			if( isset ( $result['column_name'])){
				$col_name_hebrew_date_of_death = $result['column_name'];
			}else{
				$col_name_hebrew_date_of_death = "";
			}
				
				
			if( strlen((string) $col_name_next_heb_yahrzeit) > 0 && strlen((string) $col_name_next_english_yahrzeit) > 0  ){
				$dao_exists = CRM_Core_DAO::executeQuery(
						"select count(*) as count from $heb_cal_table_name where entity_id =  $deceased_contact_id ") ;
		
				$rec_exists = false;
				if($dao_exists->fetch()){
					if( $dao_exists->count == "1" ){
						$rec_exists = true;
					}else{
						$rec_exists = false;
					}
		
				}
		
					
					
				$yah_date_cleaned_for_sql  = "null";
				
				if(  is_numeric (substr($yahrzeit_date_tmp_next, 0 , 4)) ){
					$yah_date_cleaned_for_sql = "'".$yahrzeit_date_tmp_next."'" ;
					
					$yah_english_next_for_sql = " date_add( '$tmp_yahrzeit_date_observe_english_next' , INTERVAL 1 day) ";
		
				}else{
					// all years must be numeric
					// this means  'Cannot determine yahrzeit date'
					$yah_date_cleaned_for_sql  = "null";
					$yah_english_next_for_sql  = "null"; 
		
				}
				
				$hebrew_date_of_death_for_sql = str_replace("'", "''",$hebrew_deceased_date );   // escape ' for sql
					
				//   $hebrew_data = $tmpHebCal::retrieve_hebrew_demographic_dates( $deceased_contact_id);
				if( $rec_exists){
					$sql	= "UPDATE $heb_cal_table_name SET
					$col_name_next_heb_yahrzeit  = $yah_date_cleaned_for_sql,
					$col_name_next_english_yahrzeit =  $yah_english_next_for_sql ,
					$col_name_hebrew_date_of_death = '".$hebrew_date_of_death_for_sql."'
					WHERE entity_id =  $deceased_contact_id ";
		
				}else{
					$sql = "INSERT INTO $heb_cal_table_name (entity_id , $col_name_next_heb_yahrzeit, $col_name_next_english_yahrzeit, $col_name_hebrew_date_of_death   )
					VALUES( $deceased_contact_id , $yah_date_cleaned_for_sql , $yah_english_next_for_sql ,  '".$hebrew_date_of_death_for_sql."'	 )  ";
				}
		
				// CRM_Core_Error::debug("Debug: Yahrzeit sql for custom fields : ", $sql );
				$dao_update_dececased = CRM_Core_DAO::executeQuery( $sql ) ;
				$dao_update_dececased->free();
		
		
			}
		}
		
		
	}
	function updateCiviCRMCalcBirthdayFields(  &$contact_id, &$hebrew_birth_date_formated, &$bat_mitzvah_date_formated, &$next_hebrew_birthday_formated){
		
		$rtn_did_update = 0 ; 
		
		$params = [
				'version' => 3,
				'sequential' => 1,
				'name' => HebrewCalendar::HEB_BIRTH_CUSTOM_FIELD_GROUP_NAME,
		];
		$result = civicrm_api('CustomGroup', 'getsingle', $params);
		
		if(isset($result['table_name'])){
			$heb_cal_table_name = $result['table_name'];
			$heb_cal_set_id = $result['id'];
		}else{
			$heb_cal_table_name = "";
		}
		
		if( strlen((string) $heb_cal_table_name) > 0){
		
		
			$params = [
					'version' => 3,
					'sequential' => 1,
					'custom_group_id' =>  $heb_cal_set_id,
					'name' => HebrewCalendar::HEB_NEXT_BIRTHDAY_NAME,
			];
			$result = civicrm_api('CustomField', 'getsingle', $params);
				
			if( isset ( $result['column_name'])){
				$col_name_next_heb_birthday = $result['column_name'];
			}else{
				$col_name_next_heb_birthday = "";
		
			}
		
		
			$params = [
					'version' => 3,
					'sequential' => 1,
					'custom_group_id' =>  $heb_cal_set_id,
					'name' => HebrewCalendar::HEB_EARLIEST_BARBAT_MITZVAH_NAME,
			];
			$result = civicrm_api('CustomField', 'getsingle', $params);
				
			if( isset ( $result['column_name'])){
				$col_name_earliest_barbat = $result['column_name'];
			}else{
				$col_name_earliest_barbat = "";
			}
		
			
			$params = [
					'version' => 3,
					'sequential' => 1,
					'custom_group_id' =>  $heb_cal_set_id,
					'name' => HebrewCalendar::HEB_BIRTH_DATE_NAME,
			];
			$result = civicrm_api('CustomField', 'getsingle', $params);
		
			if( isset ( $result['column_name'])){
				$col_name_hebrew_date_of_birth = $result['column_name'];
			}else{
				$col_name_hebrew_date_of_birth = "";
			}
				
			// if all three columns exist, do sql. 	
			if( strlen((string) $col_name_next_heb_birthday) > 0 && strlen((string) $col_name_earliest_barbat) > 0 && strlen((string) $col_name_hebrew_date_of_birth ) > 0  ){
				$dao_exists = CRM_Core_DAO::executeQuery(
						"select count(*) as count from $heb_cal_table_name where entity_id =  $contact_id ") ;
				$rec_exists = false;
				if($dao_exists->fetch()){
					if( $dao_exists->count == "1" ){
						$rec_exists = true;
					}else{
						$rec_exists = false;
					}
				}
		
				// 3 dates to put in database: $hebrew_birth_date_formated, $bat_mitzvah_date_formated, $next_hebrew_birthday_formated		
				$sql_earliest_barbat_date = $this->cleanDateForSQL( $bat_mitzvah_date_formated ); 
				$sql_next_heb_birthday = $this->cleanDateForSQL( $next_hebrew_birthday_formated ); 
				
				$sql_heb_date_of_birth = str_replace("'", "''", $hebrew_birth_date_formated);
					
				if( $rec_exists){
					$sql	= "UPDATE $heb_cal_table_name SET
					$col_name_next_heb_birthday  = $sql_next_heb_birthday,
					$col_name_earliest_barbat = $sql_earliest_barbat_date,
					$col_name_hebrew_date_of_birth = '".$sql_heb_date_of_birth."'
					WHERE entity_id =  $contact_id ";
		
				}else{
					$sql = "INSERT INTO $heb_cal_table_name 
					(entity_id , $col_name_next_heb_birthday , $col_name_earliest_barbat, $col_name_hebrew_date_of_birth   )
					VALUES( $contact_id , $sql_next_heb_birthday ,$sql_earliest_barbat_date, '".$sql_heb_date_of_birth."' ) ";
				}
		
				
				//CRM_Core_Error::debug("Debug: Date of birth sql for custom fields : ", $sql );
				$dao = CRM_Core_DAO::executeQuery( $sql ) ;
				$dao->free();
		
			     $rtn_did_update = 1; 
			}
		}
		
		
		
		
		return $rtn_did_update; 
		
	}
	
	
	private function cleanDateForSQL(&$date_parm){
		
	$date_cleaned  = "null";
	// 'Cannot determine date'
	if(  is_numeric (substr($date_parm, 0 , 4)) ){
		$date_cleaned = "'".$date_parm."'" ;
	
	}else{
		// all years must be numeric
		$date_cleaned  = "null";
	
	}
	
	
	return $date_cleaned; 
	
	}
	
	  

	/******************************************************************
	 *
	 *
	 *
	 *********************************************************************/
	function util_get_hebrew_month_name( &$julian_date, &$hebrew_date){

		/* Use month spellings from HebCal.com for all months.
		PHP spellings (hebcal.com spellings):
		[1] => Tishri  (Tishrei)
		[2] => Heshvan (Cheshvan)
		[3] => Kislev ()
		[4] => Tevet ()
		[5] => Shevat (Sh'vat)
            [6] => AdarI (Adar I)
            [7] => AdarII (Adar II)
            [8] => Nisan ()
            [9] => Iyyar ()
            [10] => Sivan ()
            [11] => Tammuz (Tamuz)
            [12] => Av ()
            [13] => Elul
            */
		
		list($hebrewMonth, $hebrewDay, $hebrewYear) = explode("/",$hebrew_date);

		if($hebrewMonth == HebrewCalendar::HEBREW_MONTH_TISHREI){
			return ts("Tishrei");	
		}else if($hebrewMonth == HebrewCalendar::HEBREW_MONTH_HESHVAN){
			return ts("Cheshvan");
		}else if($hebrewMonth == HebrewCalendar::HEBREW_MONTH_KISLEV){
			return ts("Kislev");
		}else if($hebrewMonth == HebrewCalendar::HEBREW_MONTH_TEVET){
			return ts("Tevet");
		}else if($hebrewMonth == HebrewCalendar::HEBREW_MONTH_SHEVAT){
			return ts("Sh'vat");
		}else if($hebrewMonth == HebrewCalendar::HEBREW_MONTH_ADAR){
			// PHP only uses month 6 in leap years, where it is Adar I.
			return $this->is_hebrew_year_leap_year($hebrewYear) ? ts("Adar I") : ts("Adar");
		}else if($hebrewMonth == HebrewCalendar::HEBREW_MONTH_ADAR_2){
			// Month 7 is Adar II in a leap year and plain Adar otherwise.
			return $this->is_hebrew_year_leap_year($hebrewYear) ? ts("Adar II") : ts("Adar");
		}else if($hebrewMonth == HebrewCalendar::HEBREW_MONTH_NISAN){
			return ts("Nisan");
		}else if($hebrewMonth == HebrewCalendar::HEBREW_MONTH_IYYAR){
			return ts("Iyyar");
		}else if($hebrewMonth == HebrewCalendar::HEBREW_MONTH_SIVAN){
			return ts("Sivan");
		}else if($hebrewMonth == HebrewCalendar::HEBREW_MONTH_TAMUZ){
			return ts("Tamuz");
		}else if($hebrewMonth == HebrewCalendar::HEBREW_MONTH_AV){
			return ts("Av");
		}else if($hebrewMonth == HebrewCalendar::HEBREW_MONTH_ELUL){
			return ts("Elul");
		}else{
			 
			 //Old logic: just use the PHP function to get the month name. */
			//return jdmonthname($julian_date,4);
			
			// something went wrong,
			return "Unknown Month";
		}

	}


	/******************************************************************
	 *   Return true if the Hebrew year is a leap year. Otherwise
	 * return false.
	 *
	 *
	 *********************************************************************/
	function is_hebrew_year_leap_year( $hebrewYear){
		
		
  // https://en.wikipedia.org/wiki/Hebrew_calendar
  //  To determine whether year n of the calendar is a leap year, 
  // find the remainder on dividing [(7 × n) + 1] by 19. 
  // If the remainder  is 6 or less it is a leap year; if it is 7 or more it is not.
		
		$tmp = (7 * $hebrewYear) + 1;
		$remainder = $tmp % 19; 
		
		if( $remainder <= 6 ){
			return true;
		}else{
			return false;
		}
		
		
		

	}
	
	
	
	/***************************************************************
	 *  Get the current date from the server and return the
	 * formatted Hebrew Date.
	 *
	 ****************************************************************/
	function util_convert_today2hebrew_date(&$hebrew_format ){


		// In the site's time zone: a cron job may run in UTC.
		$today = \Civi\Hebrewcalendarhelper\YahrzeitTokens::today();

		$gregorianMonth = $today->format('n');
		$gregorianDay = $today->format('j');
		$gregorianYear = $today->format('Y');

		//date_default_timezone_set('America/Chicago');
		$ibeforesunset = '1';
		$today_hebrew_formated = self::util_convert2hebrew_date($gregorianYear, $gregorianMonth, $gregorianDay, $ibeforesunset, $hebrew_format);
		return $today_hebrew_formated;


	}


	/******************************************************************
	 *
	 *
	 *
	 *********************************************************************/
	function util_get_next_hebrew_birthday_date(&$iyear, &$imonth, &$iday, &$ibeforesunset, &$erev_start_flag,  &$gregorian_date_format){
		//date_default_timezone_set('America/Chicago');
		$heb_format_tmp = 'mm/dd/yy';
		$birthdate_hebrew = self::util_convert2hebrew_date($iyear, $imonth, $iday, $ibeforesunset, $heb_format_tmp );
		
		//  birthdate_hebrew ( will be used for bar bat Mitzvah calculation: $birthdate_hebrew ;
		
		$dob_heb_array = explode( '/',$birthdate_hebrew);   //
		
		
		if(count( $dob_heb_array ) == 3){
			$hebrewbirthMonth = $dob_heb_array[0];
			$hebrewbirthDay = $dob_heb_array[1];
			$hebrewbirthYear = $dob_heb_array[2];
		}else{
			$hebrewbirthMonth = "";
			$hebrewbirthDay = "";
			$hebrewbirthYear = "";
				
		}
		
		
		$heb_year_format = "yy";
		$current_hebrew_year = self::util_convert_today2hebrew_date($heb_year_format);
		
		$yesterday =  date( mktime(0, 0, 0, date("m") , date("d") - 1, date("Y")));
		$yesterday_formatted = date('M j Y',  $yesterday);
		
		$purpose = 'birthday';
		
		
		if(strlen((string) $gregorian_date_format) > 0 ){
			$tmpformat = $gregorian_date_format;
		}else{
			$tmpformat = 'yyyy-mm-dd';
		}
		
		
		//$current_year_heb_birthday  = self::util_convert_hebrew2gregorian_date($current_hebrew_year , $hebrewbirthMonth, $hebrewbirthDay, $erev_start_flag, $tmpformat);
		
		
		//if($current_year_heb_birthday ==  "Date requested does not exist."  ){
			
		$current_year_heb_birthday = self::util_adjust_hebrew_date($hebrewbirthYear,  $current_hebrew_year , $hebrewbirthMonth, $hebrewbirthDay, $erev_start_flag, $tmpformat, $purpose);
			//$heb_date_is_adjusted = " (adjusted)";
		//}
		
		
		if( strtotime($current_year_heb_birthday)  >= $yesterday  ){
			// Current year heb birthday: $current_year_heb_birthday  is equal to or later than yesterday: $yesterday.
			$correct_heb_birthday = $current_year_heb_birthday;
		
		}else{
			// Current year heb birthday has already past the date: $yesterday. Advance hebrew year by 1.
			
			$next_hebrew_year = $current_hebrew_year + 1;
			
			//CRM_Core_Error::debug("Current Hebrew year birthday is in the past, check next Hebrew year ", $next_hebrew_year  );
			
			$full_format = 'MM dd, yy sunset';
			$heb_date_is_adjusted = "";
			// $next_year_heb_birthday  = self::util_convert_hebrew2gregorian_date($next_hebrew_year, $hebrewbirthMonth, $hebrewbirthDay, $erev_start_flag, $tmpformat);
		
			//CRM_Core_Error::debug("next year heb birthday: ", $next_year_heb_birthday );
			
			//if($next_year_heb_birthday ==  "Date requested does not exist."  ){
		
			$next_year_heb_birthday = self::util_adjust_hebrew_date($hebrewbirthYear , $next_hebrew_year, $hebrewbirthMonth, $hebrewbirthDay, $erev_start_flag, $tmpformat, $purpose);
				//$heb_date_is_adjusted = " (adjusted)";
				//CRM_Core_Error::debug("(Adjusted) next year heb birthday: ", $next_year_heb_birthday );
			//}
		
		
			$correct_heb_birthday = $next_year_heb_birthday;
		}
		
		
		return $correct_heb_birthday;
		
	}
	
	
	
	/******************************************************************
	 *
	 *
	 *
	 *********************************************************************/
	function  util_get_bar_bat_mizvah_date(&$iyear, &$imonth, &$iday, &$ibeforesunset, &$erev_start_flag,  &$bar_bat_mitzvah_flag, &$gregorian_date_format  ){

		//date_default_timezone_set('America/Chicago');
		$heb_format_tmp = 'mm/dd/yy';
		$birthdate_hebrew = self::util_convert2hebrew_date($iyear, $imonth, $iday, $ibeforesunset, $heb_format_tmp );

		//  birthdate_hebrew ( will be used for bar bat Mitzvah calculation: $birthdate_hebrew ;
		$dob_heb_array = explode( '/',$birthdate_hebrew);   // 
		
		if(count( $dob_heb_array ) == 3){
			$hebrewbirthMonth = $dob_heb_array[0];
			$hebrewbirthDay = $dob_heb_array[1];
			$hebrewbirthYear = $dob_heb_array[2];
		}else{
			$hebrewbirthMonth = "";
			$hebrewbirthDay = "";
			$hebrewbirthYear = "";
			
		}
				
		$bar_bat_mitzvah_year = '';
		if ( intval($hebrewbirthYear)  > 1){
		if(  $bar_bat_mitzvah_flag == 'bat'){
			// Technically a girl can be done as early as 12, but many congregations wait until 13.
			$tmp_bat_age = (int) \Civi::settings()->get('hebrewcalendarhelper_bmitzvah_age_female');
			if ($tmp_bat_age !== 12 && $tmp_bat_age !== 13) {
				$tmp_bat_age = 13;
			}
                        $bar_bat_mitzvah_year = $hebrewbirthYear + intval($tmp_bat_age);
		}else if( $bar_bat_mitzvah_flag == 'bar'){
			$bar_bat_mitzvah_year = $hebrewbirthYear + 13;
		}else{
			return "bar_bat_mitzvah_flag must be either bar or bat. " ;
		}
		}else{
			return "";
		}	

		//$tmpformat = 'MM dd, yy sunset';
		if(strlen((string) $gregorian_date_format) > 0 ){
			$tmpformat = $gregorian_date_format;
		}else{
			$tmpformat = 'yyyy-mm-dd';
		}
		//$bar_bat_gregorian_date  = self::util_convert_hebrew2gregorian_date($bar_bat_mitzvah_year, $hebrewbirthMonth, $hebrewbirthDay, $erev_start_flag, $tmpformat);


		//if($bar_bat_gregorian_date ==  "Date requested does not exist."  ){
		$purpose = 'barbat';
		$bar_bat_gregorian_date = self::util_adjust_hebrew_date($hebrewbirthYear, $bar_bat_mitzvah_year, $hebrewbirthMonth, $hebrewbirthDay, $erev_start_flag, $tmpformat, $purpose);
		//	$heb_date_is_adjusted = " (adjusted)";
		//}

		return $bar_bat_gregorian_date;


	}

	/**********************************************************************************
	 *  Adjusts an invalid Hebrew date to a valid one. 
	 *  
     * There are only 2 times this can happen in a non-leap year:
     * 
     * Chesvan can have either 29 or 30 days. 
     * Kislev can have either 29 or 30 days.
     * 
     * In a leap year: AdarI always has 30 days. (Adar does not exist) and AdarII always has 29 days.
     * In a non-leap year: Adar always has 29 days.
     * 
	 *  Then converts the adjusted date to a Gregorian date. The Gregorian date is returned.
	 * 
	 * PHP numbers for impacted months:
	 *  2 => Cheshvan
     *  3 => Kislev
	 *  6 => AdarI
     *  7 => Adar or AdarII
	 ***********************************************************************************/
	function util_adjust_hebrew_date(&$original_hyear,  &$ihyear, &$original_hmonth, &$original_hday, &$erev_start_flag, &$tmpformat, &$purpose){

		
		// $original_hyear is the original Hebrew year that the death or birth occured in.
		// $ihyear is the Hebrew year that we want to observe the yahrzeit or birthday in.
		
		// purpose can be: 'yahrzeit', 'barbat' or 'birthday' (barbat and birthday are treated the same) 
		if( $purpose == "yahrzeit" ||  $purpose == "barbat" ||  $purpose == "birthday"){
			// nothing to do. 
		}else{
			return "Cannot adjust date: Unrecognized conversion purpose: ".$purpose;
			
		}
		
		if( $original_hyear  == "0000" ||  $original_hyear  == "0" || strlen((string) $original_hyear) == 0 ){
			return "Cannot adjust date: Unrecognized original Hebrew year: ".$original_hyear;
		}
		
		$tmp_hday = $original_hday;
		$tmp_hmonth = $original_hmonth;
				
	//	CRM_Core_Error::debug("Inside util_adjust_hebrew_date function for Hebrew date: original year: ".$original_hyear , $ihyear."-".$original_hmonth."-".$original_hday);
		
		// is it Adar, AdarI or AdarII ( ie is month = '6' or '7'? )
		if( $original_hmonth == HebrewCalendar::HEBREW_MONTH_ADAR || $original_hmonth == HebrewCalendar::HEBREW_MONTH_ADAR_2 ){
				$is_original_leap_year_tmp = $this->is_hebrew_year_leap_year($original_hyear);
				$is_leap_year_tmp = $this->is_hebrew_year_leap_year($ihyear);
				
				// deal with leap year stuff.   4 possibilies:  
				// A) The original year and the observance year are both LEAP years.
				// B) The original year and the observance year are both non-leap years. 
				// C) The original year is a non-leap year, yet the observance year is a LEAP year
				// D) The original year is a LEAP year, yet the observance year is a non-leap year
				if( $is_original_leap_year_tmp == true &&  $is_leap_year_tmp == true){
					// A) birth/death occured in a LEAP year, and current observance is also a LEAP year. 
					// No need to touch AdarI or AdarII dates.	
					
				}else if( $is_original_leap_year_tmp <> true &&  $is_leap_year_tmp <> true ){
					// B) birth/death occured in a non-leap year AND current observance is also a non-leap year.
					// No need to touch AdarI or AdarII dates.
					
				}else if(  $is_original_leap_year_tmp <> true &&  $is_leap_year_tmp == true){
					// C) birth/death occured in a non-leap year YET observance is a LEAP year.
					
					if( $purpose == "barbat" || $purpose == "birthday"){
						// Adar (non-leap) birthdays are observed in AdarII. 
						$tmp_hmonth = HebrewCalendar::HEBREW_MONTH_ADAR_2;
					}else if( $purpose == "yahrzeit" ){
						// Customs differ: the setting chooses Adar I (the default) or Adar II.
						$tmp_hmonth = \Civi::settings()->get('hebrewcalendarhelper_yahrzeit_adar') === 'adar2'
							? HebrewCalendar::HEBREW_MONTH_ADAR_2
							: HebrewCalendar::HEBREW_MONTH_ADAR;
					}
					
	
				}else if($is_original_leap_year_tmp == true &&  $is_leap_year_tmp <> true){
					// D) birth/death occured in a LEAP year YET observance is a non-leap year. 
					if( $original_hmonth == HebrewCalendar::HEBREW_MONTH_ADAR && $original_hday == '30' ) {
						// Original date was Adar I 30 , ie Rosh Hodesh Adar II during a leap year. 
						// This means move date back one day, to Shevat 30, which is also Rosh Hodesh Adar
						// This logic holds for borth birthdays and yahrzeits. 
						$tmp_hmonth = HebrewCalendar::HEBREW_MONTH_SHEVAT;	
						$tmp_hday  = '30';
					}
					
					// Since its non-leap, current observance is Adar.  
					if( $original_hmonth == HebrewCalendar::HEBREW_MONTH_ADAR_2){
						$tmp_hmonth = HebrewCalendar::HEBREW_MONTH_ADAR;
					}
					
					
				}else{	
					return "An impossible situation occured during comparison of leap year vs non-leap year. ";	
				}
		}  // done with Adar, AdarI and AdarII stuff.
		
		// Is original Hebrew month Chesvan or Kislev? (has nothing to do with leap year or non-leap year. )
		if( $original_hmonth == HebrewCalendar::HEBREW_MONTH_HESHVAN || $original_hmonth == HebrewCalendar::HEBREW_MONTH_KISLEV  ){
			// ONLY do this when 30th does not occur in observance year. 
			if($original_hday == '30'){
				// Does the 30th of the input month occur in this observance year?
				$valid_hebrew_date  = self::verify_hebrew_date($ihyear , $original_hmonth, $original_hday);
				
				if( $valid_hebrew_date == 0 && ($purpose == "barbat" || $purpose == "birthday")){
					// Someone born on the 30th has not reached the anniversary until the 30th is over,
					// so in a year without one it is the 1st of the next month.
					$tmp_hmonth = $original_hmonth + 1;
					$tmp_hday  = '1';
				}else if( $valid_hebrew_date == 0){
				   // the 30 does not occur in this observance year, need to decide to move it up or move it back.
				   $first_observance_heb_year = $original_hyear + 1;
					$is_first_observance_valid_hebrew_date = self::verify_hebrew_date( $first_observance_heb_year, $original_hmonth, $original_hday);
					
					if( $is_first_observance_valid_hebrew_date == 0 ){
						// since there is NO 30th for the first observance year, 
						// so move observance forward by one day (eg no Chesvan 30 on first observance
						// so observe yahrzeit/birthay on Kiselv 1. (which is also 1st day of Rosh Hodesh)
						// Meaning on Cheshvan 30 when it exists
						// and on Kislev 1 when Cheshvan 30 doesn’t exist.
						// for example: for a person that died/born on Chesvan 30, this means observe the yarzeit/birthday on Kislev 1.
						// for example: for a person that died/born on Kiselv 30, this means observe the yarzeit/birthday on Tevet 1.
						$tmp_hmonth = $original_hmonth + 1; 
						$tmp_hday  = '1';
						
					}else{
						// There IS a 30th for the first observance, so move move observance back
						// for example: for a person that died/born on Chesvan 30, this means observe the yarzeit/birthday on Cheshvan 29.
						// for example:for a person that died/born on Kiselv 30, this means observe the yarzeit/birthday on Kiselv 29.
						$tmp_hmonth = $original_hmonth;
						$tmp_hday  = '29';
						
						
					}
					// 30th does not occur, move back 1 day. OR move ahead one day, depending on 
					// what happens on 1st yahrzeit/birthday
					/*
					A) there IS NO Cheshvan 30 for the first yahrzeit, then the Yahrtzeit falls on 
					Kislev 1 and all the following Yahrtzeits will be on the 
					1st day of Rosh Hodesh. Meaning on Cheshvan 30 when it exists 
					and on Kislev 1 when Cheshvan 30 doesn’t exist.
					OR
					B) There IS Cheshvan 30, then the Yahrtzeit falls on
					 Cheshvan 30 and all the following Yahrtzeits will 
					 fall on the last day of Cheshvan. Meaning on Cheshvan 
					 30 (1st day Rosh Hodesh) when it exists and 
					 on Cheshvan 29 when Cheshvan 30 doesn’t exist.
						*/
					
				}
			}
		}
		
		
		// the rest is general code. 
		$tmp_new_date = self::util_convert_hebrew2gregorian_date($ihyear, $tmp_hmonth, $tmp_hday, $erev_start_flag, $tmpformat);
		//CRM_Core_Error::debug("Almost done with adjust function", $ihyear."-".$tmp_hmonth."-".$tmp_hday);
		// CRM_Core_Error::debug("Inside util_adjust_hebrew_date function: About to return Gregorian date: ", $tmp_new_date);
		return $tmp_new_date;
	}


	/**********************************************************************************
	 * Input is Gregorian date of death, plus if the death
	 *  occured before sunset or not.
	 * it returns the formatted Gregorian date of the yarhzeit.
	 ***********************************************************************************/



	function util_get_yahrzeit_date($year_offset , $iyear, $imonth, $iday, $gregorian_ibeforesunset, $erev_start_flag, $gregorian_format){

		//print "<br>Inside util_get_yahrzeit_date";
		// print " PARMS: flag: ".$previous_next_flag." iyear: ".$iyear." imonth: ".$imonth." iday: ".$iday;
        if(is_numeric( $year_offset)){
        	
        }else{
        	return "The year_offset parm ( '".$year_offset."' ) must be numeric. ";
        }
		
		
		$defaultmsg = "Cannot determine yahrzeit date";
		if($iyear == ''  ){
			return $defaultmsg;
		}
		 
		if($imonth == ''  ){
			return $defaultmsg;
		}

		if($iday == ''  ){
			return $defaultmsg;
		}

		if ((string) $gregorian_ibeforesunset === '') {
			// Unknown: take the date as entered, as if before sunset.
			$gregorian_ibeforesunset = '1';
		}


		// date_default_timezone_set('America/Chicago');

		$heb_date_is_adjusted = "";
		$heb_format_tmp = 'mm/dd/yy';
		$deathdate_hebrew = self::util_convert2hebrew_date($iyear, $imonth, $iday, $gregorian_ibeforesunset, $heb_format_tmp );

		//print "<br>Hebrew death date: ".$deathdate_hebrew;

		list($hebrewdeathMonth, $hebrewdeathDay, $hebrewdeathYear) = explode("/",$deathdate_hebrew);

		$heb_year_format = "yy";
		$current_hebrew_year = self::util_convert_today2hebrew_date($heb_year_format);

		// print "<br>cur Hebrew year: ".$current_hebrew_year;

		# Get yahrzeit date for the current Hebrew year.
		$tmpformat = 'yyyy-mm-dd';
		$purpose = "yahrzeit";
		
		$current_year_yahrzetit = self::util_adjust_hebrew_date($hebrewdeathYear, $current_hebrew_year, $hebrewdeathMonth, $hebrewdeathDay, $erev_start_flag, $tmpformat, $purpose);
		
			
		if ($current_year_yahrzetit == "Cannot determine Hebrew date" ){
			return "Cannot determine Hebrew date for current year yahrzeit";
		}

		$correct_yarzheit = '';
		$yesterday =  date( mktime(0, 0, 0, date("m") , date("d") - 1, date("Y")));
		$yesterday_formatted = date('M j Y',  $yesterday);

		$full_format = 'MM dd, yy sunset';
		$heb_date_is_adjusted = "";
		$wanted_hebrew_year = "";
		
		// 1 = next yahrzeit, 2 = yahr after the next one, etc. 
		if($year_offset > 0 ){
			
			//$tmp_hebrew_year_of_next_yahr = ""; 
			
			if( strtotime($current_year_yahrzetit)  >= $yesterday  ){
				// Current year yarhzeit: $current_year_yahrzetit  is equal to or later than yesterday: $yesterday.
				//$correct_yarzheit = $current_year_yahrzetit;
				//$tmp_hebrew_year_of_next_yahr = $current_hebrew_year; 
				
				$wanted_hebrew_year = ($current_hebrew_year - 1  ) +  $year_offset;

			}else{
				// Current year yarhzeit has already past the date: $yesterday. Advance hebrew year by 1.
				//$next_hebrew_year = $current_hebrew_year + 1;
				//$tmp_hebrew_year_of_next_yahr = $next_hebrew_year ; 
				
				$wanted_hebrew_year = $current_hebrew_year  +  $year_offset;
						
				//$next_year_yahrzetit = self::util_adjust_hebrew_date($hebrewdeathYear , $next_hebrew_year, $hebrewdeathMonth, $hebrewdeathDay, $erev_start_flag, $tmpformat, $purpose);
				//$correct_yarzheit = $next_year_yahrzetit;
			}
		}else{
			
	  		// get previous yahrzeit. -1 = previous yahr, -2 = the yahrzeit before the previous one. 
			if( strtotime($current_year_yahrzetit)  < $yesterday  ){
				// Current year yarhzeit: $current_year_yahrzetit  is before yesterday: $yesterday.
				//$correct_yarzheit = $current_year_yahrzetit;
				$wanted_hebrew_year = $current_hebrew_year + 1  +  $year_offset;

			}else{
				// Current hebrew year yarhzeit in the future:  Subtract 1 from hebrew year.
				//$prev_hebrew_year = $current_hebrew_year - 1;
						
				//$prev_year_yahrzetit = self::util_adjust_hebrew_date($hebrewdeathYear , $prev_hebrew_year, $hebrewdeathMonth, $hebrewdeathDay, $erev_start_flag, $tmpformat, $purpose);
				
				$wanted_hebrew_year = $current_hebrew_year  +  $year_offset;
				
				//$correct_yarzheit = $prev_year_yahrzetit;
			}


		}
		
		//
		// $wanted_hebrew_year
		if(strlen((string) $wanted_hebrew_year) > 0 ){
			//print "<br>wanted heb year: ".$wanted_hebrew_year."  heb month: ".$hebrewdeathMonth;
			$correct_yarzheit = self::util_adjust_hebrew_date($hebrewdeathYear , $wanted_hebrew_year, $hebrewdeathMonth, $hebrewdeathDay, $erev_start_flag, $tmpformat, $purpose);
			//print "<br>Correct yahrzeit:  $correct_yarzheit ";
			
		}else{
			return "Cannot determine yahrzeit because wanted_hebrew_year is blank.";
			
		}

		// print "<br><br><b>correct yahrzeit to return : </b> ".$correct_yarzheit;

		$tmp_date = date_create($correct_yarzheit);

		if($tmp_date == ""){
			return "Cannot determine yahrzeit";

		}

		$gregorianMonthName = $tmp_date->format('F');
		$gregorianDay = $tmp_date->format('j');
		$gregorianYear =$tmp_date->format('Y');


		//'yyyy-mm-dd';
		
		//print "<br> gregorian format: ".$gregorian_format;
		
		if( $gregorian_format == "MM dd, yyyy"){
			$gregorianMonthName = $tmp_date->format('F');
			$gregorianDay = $tmp_date->format('j');
			$gregorianYear =$tmp_date->format('Y');
			$yahrzeit_date_formatted  = "$gregorianMonthName $gregorianDay, $gregorianYear starting at sunset $heb_date_is_adjusted";

			return $yahrzeit_date_formatted ;
		}else if( $gregorian_format == "dd MM yyyy" ){

			$gregorianMonthName = $tmp_date->format('F');
			$gregorianDay = $tmp_date->format('j');
			$gregorianYear =$tmp_date->format('Y');
			$yahrzeit_date_formatted  = "$gregorianDay $gregorianMonthName $gregorianYear starting at sunset $heb_date_is_adjusted";

			return $yahrzeit_date_formatted ;


		} else if($gregorian_format == "yyyy-mm-dd"){
			$gregorianMonth = $tmp_date->format('m');
			$gregorianDay = $tmp_date->format('d');
			$gregorianYear =$tmp_date->format('Y');
			$yahrzeit_date_formatted  ="$gregorianYear-$gregorianMonth-$gregorianDay";
			return $yahrzeit_date_formatted ;

		}else{
			return "unrecognized gregorian format: $gregorian_format";
		}


	}

	/******************************************************************
	 *
	 *
	 *
	 *********************************************************************/
	function verify_hebrew_date($hebyear , $hebmonth, $hebday){
		/* This function verifies if the Hebrew date exists in reality. For example, 3 Hebrew months  */
		/* are variable length months. Adar, Heshvan, and Kieslev have either 29 or 30 days depending on */
		/* the year. The conversion from Julian to Hebrew ALWAYS produces a legit Hebrew date. The conversion from Hebrew to */
		/* Julian is not always accurate. By going both ways and verifying the results match, we can be certain */
		/* the Hebrew date is valid.
		 /* If valid date, return 1. Else return 0  */


		if($hebyear == '' || $hebmonth == '' || $hebday == ''){
			//  error:  year, month and day are all required.
			return 0;
		}
		$julian_datetmp =  cal_to_jd ( CAL_JEWISH  ,  $hebmonth , $hebday , $hebyear  );

		$tmp_gregorian = jdtogregorian( $julian_datetmp );
		
		
		$tmp_greg_arr = explode("/", $tmp_gregorian  );
		$tmp_greg_month = $tmp_greg_arr[0];
		$tmp_greg_day = $tmp_greg_arr[1];
		$tmp_greg_year = $tmp_greg_arr[2];
		
		
	//	CRM_Core_Error::debug("Inside verify part 1: Gregorian date: ", $tmp_gregorian." year: ".$tmp_greg_year." month: ".$tmp_greg_month." day: ".$tmp_greg_day ); 
		
		$jd_day_count_from_greg = cal_to_jd ( CAL_GREGORIAN , $tmp_greg_month , $tmp_greg_day , $tmp_greg_year );
		
	//	CRM_Core_Error::debug("Inside Verify for Greg. date $tmp_gregorian:", "Julian day count from Hebrew: ".$julian_datetmp." ANd Julian Day Count from Gregorian: ".$jd_day_count_from_greg);
		$hebrewDate_tmp = jdtojewish($julian_datetmp);
		

		
		list($hebrewMonth_tmp, $hebrewDay_tmp, $hebrewYear_tmp) = explode("/",$hebrewDate_tmp);
		
		$tmp_heb_test = cal_from_jd($julian_datetmp, CAL_JEWISH  );
 	//CRM_Core_Error::debug($hebyear."-".$hebmonth."-".$hebday.": inside verify function : ", $hebrewDate_tmp." Test heb: ".$tmp_heb_test['date']);
		
		
		// Hebrew date before: $hebmonth-$hebday-$hebyear / after round trip (mm-dd-yyyy): $hebrewMonth_tmp-$hebrewDay_tmp-$hebrewYear_tmp

		if( $hebrewMonth_tmp == $hebmonth && $hebrewDay_tmp == $hebday && $hebrewYear_tmp == $hebyear){
			return 1;

		}else{
			return 0;
		}


	}

	/******************************************************************
	 *
	 *
	 *
	 *********************************************************************/
	function util_convert2hebrew_date(&$iyear, &$imonth, &$iday, &$ibeforesunset, &$hebrewformat){
		 
		$defaultmsg = "Cannot determine Hebrew date";
		if($iyear == ''  ){
			return $defaultmsg." because year is blank";
		}
		 
		if($imonth == ''  ){
			return $defaultmsg." because month is blank";
		}

		if($iday == ''  ){
			return $defaultmsg." because day is blank";
		}


		if ((string) $ibeforesunset === '') {
			// Unknown: take the date as entered, as if before sunset.
			$ibeforesunset = '1';
		}




		# date_default_timezone_set('Europe/London');
		$idate_tmp = new DateTime("$iyear-$imonth-$iday");

		$idate_str = $idate_tmp->format('F j, Y');
		// Date provided: $idate_str

		$sunset_info_formated = '';
		if($ibeforesunset == "0"){
			$tmpdate_unix = mktime(0, 0, 0,  $idate_tmp->format('m')  ,  $idate_tmp->format('d')+1,  $idate_tmp->format('Y'));
			$tmpdate_array = getdate($tmpdate_unix );


			$gregorianMonth = $tmpdate_array['mon'];
			$gregorianDay = $tmpdate_array['mday'];
			$gregorianYear = $tmpdate_array['year'];
			// After sunset, so added 1 day to Gregorian date.

			$sunset_info_formated = '';

		}else if($ibeforesunset == "1"){
			$gregorianMonth = $idate_tmp->format('n');
			$gregorianDay = $idate_tmp->format('j');
			$gregorianYear = $idate_tmp->format('Y');

			$sunset_info_formated = ' until sunset';
			// Before sunset, so no change to Gregorian date.

		}else{
			return "Cannot determine Hebrew date because ibeforesunset is not 1 or 0.";

		}

		// Date to convert to Hebrew date( mm-dd-yyyy) :  $gregorianMonth - $gregorianDay - $gregorianYear

		$jdDate = gregoriantojd($gregorianMonth,$gregorianDay,$gregorianYear);


		if($hebrewformat == 'mm/dd/yy'){
			$hebrewDate = jdtojewish($jdDate);
			list($hebrewMonth, $hebrewDay, $hebrewYear) = explode("/",$hebrewDate);
			$hebrew_date_formated = "$hebrewMonth/$hebrewDay/$hebrewYear";
		}else if($hebrewformat == 'dd MM yy sunset'){
			$hebrewDate = jdtojewish($jdDate);
			list($hebrewMonth, $hebrewDay, $hebrewYear) = explode("/",$hebrewDate);
			$hebrewMonthName = self::util_get_hebrew_month_name($jdDate, $hebrewDate);
			$hebrew_date_formated = "$hebrewDay  $hebrewMonthName  $hebrewYear $sunset_info_formated";
		}else if($hebrewformat == 'dd MM yy' || $hebrewformat == 'dd_MM_yy'){
			$hebrewDate = jdtojewish($jdDate);
			list($hebrewMonth, $hebrewDay, $hebrewYear) = explode("/",$hebrewDate);
			$hebrewMonthName = self::util_get_hebrew_month_name($jdDate, $hebrewDate);
			$hebrew_date_formated = "$hebrewDay $hebrewMonthName $hebrewYear";
		}else if($hebrewformat == 'dd MM' || $hebrewformat == 'dd_MM'  ){
			$hebrewDate = jdtojewish($jdDate);
			list($hebrewMonth, $hebrewDay, $hebrewYear) = explode("/",$hebrewDate);
			$hebrewMonthName = self::util_get_hebrew_month_name($jdDate, $hebrewDate);
			$hebrew_date_formated = "$hebrewDay $hebrewMonthName";

		}else if($hebrewformat == 'yy'){
			$hebrewDate = jdtojewish($jdDate);
			list($hebrewMonth, $hebrewDay, $hebrewYear) = explode("/",$hebrewDate);
			$hebrew_date_formated  = "$hebrewYear";
		}else if($hebrewformat == 'hebrew'){
			$hebrew_date_formated =  mb_convert_encoding( jdtojewish( $jdDate, true ), "UTF-8", "ISO-8859-8");
		}else{
			$hebrew_date_formated = "Unrecognized Hebrew date format: $hebrewformat";
		}



		// Hebrew Date formatted: $hebrew_date_formated
		return $hebrew_date_formated;

	}

	/******************************************************************
	 * Input parm is the Hebrew date and desired format. The Gregorian
	 * date is returned as a formated string.
	 *
	 *
	 *******************************************************************/
	function util_convert_hebrew2gregorian_date($iyear, $imonth, $iday, $erev_start_flag, $date_format){

		if($imonth==''){

			return "Month is required";
		}else if($iday=='' ){
			return "Day is required";

		}else if($iyear==''){

			return "year is required";
		}else{

			//CRM_Core_Error::debug("Debug: Inside convert_hebrew2gregorian: Hebrew date provided: ",  $iyear."-".$imonth."-".$iday);
			
			if( $imonth == HebrewCalendar::HEBREW_MONTH_HESHVAN  ||  $imonth == HebrewCalendar::HEBREW_MONTH_KISLEV){ 
				// if Chesvan or Kislev, these are the only 2 months where we do not know if the length is 29 or 30. 
				if( $iday == "30"){
					$valid_hebrew_date  = self::verify_hebrew_date($iyear , $imonth, $iday);
		
					if( $valid_hebrew_date == 0){
						//CRM_Core_Error::debug("----Error: Inside convert_hebrew2gregorian: Invalid date: Hebrew date provided: ".$iyear."-".$imonth."-".$iday, "<hr>");
						return "Date requested does not exist.";
					}
				}
			}
			
			
			

			$julian_date =  cal_to_jd ( CAL_JEWISH  ,  $imonth , $iday ,$iyear  );
			$gregorian_date = cal_from_jd( $julian_date, CAL_GREGORIAN);


			$oDay = $gregorian_date['day'];
			$oYear = $gregorian_date['year'];
			$oMonthName = $gregorian_date['monthname'];
			$oMonth = $gregorian_date['month'];

			if( $erev_start_flag == '0'){
				$sunset_str = "until sunset";
				// wanted ending date, no change needed to Gregorian date.
			}else if($erev_start_flag == '1' ){

				$sunset_str = "starting at sunset";
				$tmpdate_unix = mktime(0, 0, 0,  $oMonth  ,  $oDay - 1,  $oYear);
				$tmpdate_array = getdate($tmpdate_unix );


				$oMonthName = $tmpdate_array['month'];
				$oMonth = $tmpdate_array['mon'];
				$oDay = $tmpdate_array['mday'];
				$oYear = $tmpdate_array['year'];
				// wanted Erev ( ie starting date), so subtracted 1 day from Gregorian date.

				
				
			}else{
				return "Unknown erev_start_flag, must be either '1' or '0' ";
			}



			$formatted_date_str = '';
			if($date_format == 'yyyy-mm-dd'){
				$dash = "-";
				$formatted_date_str= $oYear.$dash.$oMonth.$dash.$oDay;
				// Is Hebrew date valid?  $valid_str
			}else if($date_format == 'dd-mm-yyyy'){
				$slash = '-';
				$formatted_date_str= $oDay.$slash.$oMonth.$slash.$oYear;

			}else if($date_format == 'MM dd, yy sunset'){
				// numeric month: $oMonth

				$formatted_date_str = "$oMonthName $oDay, $oYear $sunset_str"  ;
			}else if($date_format == 'crm_api'){
				
				 $formatted_date_str = date( 'Y-m-d', $tmpdate_unix);
				 
			}else{
				$formatted_date_str = "Unknown date_format.";
			}
			// function util_convert_hebrew2gregorian_date about to return: $formatted_date_str
			// print "<br>".$formatted_date_str."<br>";
			return $formatted_date_str;
		}

	}


	public static function getSQLschema(){

		$tmp_schema_name = '';
		$sql = "SELECT SCHEMA() as tmp_sname from civicrm_contact limit 0, 1" ;
		$dao = CRM_Core_DAO::executeQuery( $sql  ) ;
		 
		if($dao->fetch( )){
			$tmp_schema_name = $dao->tmp_sname;
		}else{
			print "<br><br>HebrewCalendar rror message: Cound NOT find CiviCRM db schema using query: ".$sql;
		}
		 
		$dao->free();
		// print "<br>About to return schema name: ".$tmp_schema_name;
		return $tmp_schema_name;
	}


	function getYahrzeitDateEnglishObservance(&$deceased_year, &$deceased_month, &$deceased_day, &$year_offset ){

		$tmp_return = '';
		$cur_year = date('Y');
        
		if(is_numeric($year_offset)){
			
		}else{
			$tmp_return = "Year offset parm must be numeric: ".$year_offset;
			return $tmp_return;
		}

		if(strlen((string) $deceased_year) > 0 && strlen((string) $deceased_month) > 0 && strlen((string) $deceased_day) > 0){

            $wanted_year = ""; 
			$tmp_yahrzeit_date_observe_english = new DateTime($cur_year.'-'.$deceased_month.'-'.$deceased_day);
			// Since this function is expected to return the evening of the yahrzeit, need to subtract 1 day.
			$tmp_yahrzeit_date_observe_english = $tmp_yahrzeit_date_observe_english->sub(new DateInterval('P1D')) ;

			// Like the Hebrew yahrzeit, this year's counts as next until the day after it: the
			// evening it starts is yesterday at the latest.
			$yesterday = new DateTime('yesterday');

			// 1= next yahrzeit, 2 = yahrzeit after next, etc.
			if( $year_offset > 0 ){
				if( $tmp_yahrzeit_date_observe_english < $yesterday){
			 
					$wanted_year = $cur_year + $year_offset; 
					
					//$tmp_yahrzeit_date_observe_english->add(new DateInterval('P1Y')) ;
				}else{
					$wanted_year = ($cur_year -1 ) + $year_offset; 
				}
			}else if($year_offset < 0){
				// print "<br>Need prev. English yahrzeit date. ";
				if( $tmp_yahrzeit_date_observe_english >= $yesterday){
			  // subtract a year.
					$wanted_year = $cur_year + $year_offset ;
					//$tmp_yahrzeit_date_observe_english->sub(new DateInterval('P1Y')) ;
				}else{
					$wanted_year = $cur_year  + 1  + $year_offset; 
				}


			}else{
				$tmp_return = "Unknown year_offset parm: ".$year_offset;

			}

			$tmp_yahrzeit_date_observe_english = new DateTime($wanted_year.'-'.$deceased_month.'-'.$deceased_day);
			// Since this function is expected to return the evening of the yahrzeit, need to subtract 1 day.
			$tmp_yahrzeit_date_observe_english = $tmp_yahrzeit_date_observe_english->sub(new DateInterval('P1D')) ;
			
			
			$tmp_return = $tmp_yahrzeit_date_observe_english->format('Y-m-d');
		}else{
			$tmp_return = "Unknown date";
		}

		// print "<h2>English observed yahrzeit: ".$tmp_yahrzeit_date_observe_english_sql."</h2>";


		return $tmp_return;


	}


}



?>
