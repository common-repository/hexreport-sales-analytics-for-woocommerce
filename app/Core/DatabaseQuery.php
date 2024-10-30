<?php
namespace HexReport\App\Core;

use HexReport\App\Core\Lib\SingleTon;

use CodesVault\Howdyqb\DB;

class DatabaseQuery
{
	use SingleTon;

	/**
	 * @package hexreport
	 * @author WpHex
	 * @method register
	 * @return mixed
	 * @since 1.0.0
	 * Add all the necessary hooks that are needed.
	 */
	public function register()
	{
		add_action( 'init', [ $this, 'update_year_column_value' ] );
		add_action( 'wp', [ $this, 'log_visitor_arrival_data' ] );
	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @since 1.0.0
	 * @method update_year_column_value
	 * @return void
	 * Update 'Year' column database value if it does not match with current year
	 */
	public function update_year_column_value()
	{
		$current_year = date( 'Y' );

		$result2 =
			DB::select( 'hexreport_visitor_log.' . 'Year' )
				->distinct()
				->from( 'hexreport_visitor_log hexreport_visitor_log' )
				->get();

		if (  ! empty( $result2[0]['Year'] ) && $current_year != $result2[0]['Year'] ) {
			DB::update('hexreport_visitor_log', [
				'Year' => $current_year,
				'January' => 0,
				'February' => 0,
				'March' => 0,
				'April' => 0,
				'May' => 0,
				'June' => 0,
				'July' => 0,
				'August' => 0,
				'September' => 0,
				'October' => 0,
				'November' => 0,
				'December' => 0,
			] )
				->where('Year', '!=', $current_year)
				->execute();
		}
	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @since 1.0.0
	 * @method log_visitor_arrival_data
	 * @return void
	 * Get the total number of counts of user visits of the website
	 */
	public function log_visitor_arrival_data()
	{
		$current_year = date( 'Y' );
		$current_month = date( 'F' );

		$result =
			DB::select( 'hexreport_visitor_log.' . $current_month )
				->distinct()
				->from( 'hexreport_visitor_log hexreport_visitor_log' )
				->get();

		$current_count = ! empty( $result[0][$current_month] ) ? $result[0][$current_month] : 0;

		if ( is_user_logged_in() ) {
			return;
		} else {
			if ( 0 === $current_count ) {
				// Initialize the count if it doesn't exist
				$current_count = 1;
				DB::insert('hexreport_visitor_log', [
					[
						$current_month => $current_count,
					]
				] );
			} else {
				// Increment the count
				$current_count++;

				DB::update('hexreport_visitor_log', [
					$current_month => $current_count,
				] )
					->where('Year', '=', $current_year)
					->execute();
			}
		}
	}
}
