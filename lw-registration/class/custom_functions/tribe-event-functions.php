<?php
add_action('wp_enqueue_scripts',function(){
	if ( ! isset( $_COOKIE['tribe_browser_time_zone'] ) ) { ?>
		<script type="text/javascript">
			if ( navigator.cookieEnabled ) {
				document.cookie = "tribe_browser_time_zone=" + Intl.DateTimeFormat().resolvedOptions().timeZone + "; path=/";
			}
		</script>
	<?php }
});

if ( ! function_exists( 'tribe_get_start_date' ) ) {
	/**
	 * Start Date
	 *
	 * Returns the event start date and time
	 *
	 * @category Events
	 *
	 * @since 4.7.6 Deprecated the $timezone parameter.
	 *
	 * @param int    $event        (optional)
	 * @param bool   $display_time If true shows date and time, if false only shows date
	 * @param string $date_format  Allows date and time formating using standard php syntax (http://php.net/manual/en/function.date.php)
	 * @param string $timezone     Deprecated. Timezone in which to present the date/time (or default behaviour if not set)
	 *
	 * @return string|null Date
	 */
	function tribe_get_start_date( $event = null, $display_time = true, $date_format = '', $timezone = null ) {
		static $cache_var_name = __FUNCTION__;

		if(empty($timezone)){
			$timezone = getUserLocalTimeZone();
		}

		if ( is_null( $event ) ) {
			global $post;
			$event = $post;
		}

		if ( is_numeric( $event ) ) {
			$event = get_post( $event );
		}

		if ( ! is_object( $event ) ) {
			return '';
		}

		$start_dates = tribe_get_var( $cache_var_name, [] );
		$cache_key = "{$event->ID}:{$display_time}:{$date_format}:{$timezone}";

		if ( ! isset( $start_dates[ $cache_key ] ) ) {
			if ( Tribe__Date_Utils::is_all_day( get_post_meta( $event->ID, '_EventAllDay', true ) ) ) {
				$display_time = false;
			}

			// @todo [BTRIA-584]: Move timezones to Common.
			if ( class_exists( 'Tribe__Events__Timezones' ) ) {
				$start_date = Tribe__Events__Timezones::event_start_timestamp( $event->ID, $timezone );
			} else {
				return null;
			}

			$start_dates[ $cache_key ] = tribe_format_date( $start_date, $display_time, $date_format );
			tribe_set_var( $cache_var_name, $start_dates );
		}

		/**
		 * Filters the returned event start date and time
		 *
		 * @param string  $start_date
		 * @param WP_Post $event
		 */
		return apply_filters( 'tribe_get_start_date', $start_dates[ $cache_key ], $event );
	}
}

if ( ! function_exists( 'tribe_get_end_date' ) ) {
	/**
	 * End Date
	 *
	 * Returns the event end date
	 *
	 * @category Events
	 *
	 * @since 4.7.6 Deprecated the $timezone parameter.
	 *
	 * @param int    $event        (optional)
	 * @param bool   $display_time If true shows date and time, if false only shows date
	 * @param string $date_format  Allows date and time formating using standard php syntax (http://php.net/manual/en/function.date.php)
	 * @param string $timezone     Deprecated. Timezone in which to present the date/time (or default behaviour if not set)
	 *
	 * @return string|null Date
	 */
	function tribe_get_end_date( $event = null, $display_time = true, $date_format = '', $timezone = null ) {
		static $cache_var_name = __FUNCTION__;

		if(empty($timezone)){
			$timezone = getUserLocalTimeZone();
		}		
		
		if ( is_null( $event ) ) {
			global $post;
			$event = $post;
		}

		if ( is_numeric( $event ) ) {
			$event = get_post( $event );
		}

		if ( ! is_object( $event ) ) {
			return '';
		}

		$end_dates = tribe_get_var( $cache_var_name, [] );
		$cache_key = "{$event->ID}:{$display_time}:{$date_format}:{$timezone}";

		if ( ! isset( $end_dates[ $cache_key ] ) ) {
			if ( Tribe__Date_Utils::is_all_day( get_post_meta( $event->ID, '_EventAllDay', true ) ) ) {
				$display_time = false;
			}

			// @todo [BTRIA-584]: Move timezones to Common.
			if ( class_exists( 'Tribe__Events__Timezones' ) ) {
				$end_date = Tribe__Events__Timezones::event_end_timestamp( $event->ID , $timezone );
			} else {
				return null;
			}

			$end_dates[ $cache_key ] = tribe_format_date( $end_date, $display_time, $date_format );
			tribe_set_var( $cache_var_name, $end_dates );
		}

		/**
		 * Filters the returned event end date and time
		 *
		 * @param string  $end_date
		 * @param WP_Post $event
		 */
		return apply_filters( 'tribe_get_end_date', $end_dates[ $cache_key ], $event );
	}
}

function getUserLocalTimeZone()
{
	$timeZone = null;
	if (!empty($_COOKIE['tribe_browser_time_zone'])) {
		$timeZone = $_COOKIE['tribe_browser_time_zone'];
	}	
	return $timeZone;
}

function tribe_get_event_timezone_abbr($event_id)
{
	$browser_time_zone_string = getUserLocalTimeZone();
	if(!empty($browser_time_zone_string)){
		 // Grab the event time zone string.
		 $event_time_zone_string = Tribe__Events__Timezones::get_event_timezone_string( $event_id );
		 
		 // Grab the event start date in UTC time from the database.
	  	 $event_start_utc = tribe_get_event_meta( $event_id, '_EventStartDateUTC', true );
		 
		 // Set up the DateTime object.
		 $event_start_date_in_utc_timezone = new DateTime( $event_start_utc, new DateTimeZone( 'UTC' ) );
		 
		 // Convert the UTC DateTime object into the browser time zone.
		 $event_start_date_in_browser_timezone = $event_start_date_in_utc_timezone->setTimezone( new DateTimeZone( $browser_time_zone_string ) )->format( get_option( 'time_format' ) );

		 // Grab the time zone abbreviation based on the browser time zone string.
		 $time_zone_abbreviation = Tribe__Timezones::abbr( 'now', $browser_time_zone_string );
	}
	else{
		$time_zone_abbreviation = get_post_meta(get_the_ID(),'_EventTimezoneAbbr',true);
	}
	return $time_zone_abbreviation;	
}
?>