<?php

namespace HexReport\App\Controllers;

use HexReport\App\Core\Lib\SingleTon;
use Kathamo\Framework\Lib\Controller;
use CodesVault\Howdyqb\DB;

class AjaxApiController extends Controller
{
	use SingleTon;

	/**
	 * @package hexreport
	 * @author WpHex
	 * @since 1.0.0
	 * @method register
	 * @return void
	 * Register all hooks that are needed
	 */
	public function register()
	{
		add_action( 'wp_ajax_total_sales_amount', [ $this, 'total_sales_amount' ] );
		add_action( 'wp_ajax_show_first_top_selling_product_monthly_data', [ $this, 'show_first_top_selling_product_monthly_data' ] );
		add_action( 'wp_ajax_get_top_two_selling_categories_names', [ $this, 'get_top_two_selling_categories_names' ] );
	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @since 1.0.0
	 * @method total_sales_amount
	 * @return void
	 * Get the equals of total sale amount of WooCommerce all products
	 */
	public function total_sales_amount()
	{
		$total_completed_sales = 0;
		$total_cancelled_sales = 0;
		$total_refunded_sales = 0;

		$product_prices = [];

		$category_quantities = [];
		$category_total_amounts = [];

		// Get all completed orders
		$orders = wc_get_orders( [
			'status' => [ 'completed', 'cancelled', 'refunded' ],
			'limit' => -1,
		] );

		foreach ( $orders as $order ) {
			if ( $order->get_status() === 'completed' ) {
				$total_completed_sales += abs( $order->get_total() );
				foreach ( $order->get_items() as $item_id => $item ) {
					$product_id = $item->get_product_id();
					$quantity = $item->get_quantity();
					$product_price = $item->get_product()->get_price();

					// Get product categories
					$categories = wp_get_post_terms( $product_id, 'product_cat', [ 'fields' => 'ids' ] );
					foreach ( $categories as $category_id ) {
						if ( isset( $category_quantities[$category_id] ) ) {
							// Increment the quantity if the category already exists in the array
							$category_quantities[$category_id] += $item->get_quantity();
							$category_total_amounts[$category_id] += $item->get_quantity() * $product_price;
						} else {
							// Add the category to the array if it doesn't exist
							$category_quantities[$category_id] = $item->get_quantity();
							$category_total_amounts[$category_id] = $item->get_quantity() * $product_price;
						}
					}

					if ( isset( $product_quantities[$product_id] ) ) {
						// Increment the quantity and calculate total price if the product already exists in the array
						$product_quantities[$product_id] += $quantity;
						$product_prices[$product_id] += $quantity * $product_price;
					} else {
						// Add the product to the arrays if it doesn't exist
						$product_quantities[$product_id] = $quantity;
						$product_prices[$product_id] = $quantity * $product_price;
					}
				}
			} elseif ( $order->get_status() === 'cancelled' ) {
				$total_cancelled_sales += $order->get_total();
			} elseif ( $order->get_status() === 'refunded' ) {
				$total_refunded_sales += $order->get_total();
			}
		}

		// Find the category with the highest quantity sold
		arsort( $category_quantities ); // Sort the categories by quantity in descending order
		$top_category_id = key( $category_quantities ); // Get the category with the highest quantity
		// Load the top-selling category object
		$top_category = get_term( $top_category_id, 'product_cat' );

		$top_selling_cat_name = ! empty( $top_category->name ) ? $top_category->name : '';
		$top_selling_cat_amount = ! empty( $category_total_amounts[$top_category_id] ) ? $category_total_amounts[$top_category_id] : 0;

		// Find the product with the highest quantity sold
		if ( ! empty( $product_quantities ) ) {
			arsort( $product_quantities ); // Sort the products by quantity in descending order
		}

		$top_product_id = ! empty( $product_quantities ) ? key( $product_quantities ) : 0; // Get the product with the highest quantity

		// Load the product object and get its price
		$top_product = wc_get_product( $top_product_id );
		$top_product_price = ! empty( $top_product_id ) ? $product_prices[$top_product_id] : 0;
		$top_selling_product_name = ! empty( $top_product ) ? $top_product->get_name() : '';


		$total_orders = $total_completed_sales + $total_cancelled_sales;

		$total_completed_sales = $total_completed_sales - $total_refunded_sales; // subtracting the refunded orders from completed orders.

		$monthly_completed_sales = $this->total_sales_amount_for_year();

		$totalVisitorsCount = $this->total_visitors_count_for_year();

		$total_completed_order_in_three_phases = $this->total_completed_order_in_three_phases();

		$total_order_ratio = $this->total_order_ratio();

		$count_payment_method_ratio = $this->count_payment_method_ratio();

		// Check the nonce and action
		if ( $this->verify_nonce() ) {
			// Nonce is valid, proceed with your code
			wp_send_json( [
				// Response data here
				'msg' => __( 'hello','hexreport' ),
				'type' => 'success',
				'totalSales' => sprintf( __( '%s', 'hexreport' ), esc_html( $total_completed_sales ) ),
				'totalCancelledAmount' => sprintf( __( '%s', 'hexreport' ), esc_html( $total_cancelled_sales ) ),
				'totalOrdersAmount' => sprintf( esc_html__( '%s', 'hexreport' ), esc_html( $total_orders ) ),
				'totalRefundedAmount' => sprintf( __( '%s', 'hexreport' ), esc_html( $total_refunded_sales ) ),
				'topSellingProductName' => sprintf( __( '%s', 'hexreport' ), esc_html( $top_selling_product_name ) ),
				'topSellingProductPrice' => sprintf( __( '%s', 'hexreport' ), esc_html( $top_product_price ) ),
				'topSellingCatName' => sprintf( __( '%s', 'hexreport' ), esc_html( $top_selling_cat_name ) ),
				'topSellingCatPrice' => sprintf( __( '%s', 'hexreport' ), esc_html( $top_selling_cat_amount ) ),

				'totalSalesOfYear' => array_map( 'esc_html', $monthly_completed_sales ),

				'totalVisitorsCount' => array_map( 'esc_html', $totalVisitorsCount ),

				'totalCompletedOredersFromJanToApr' => sprintf( esc_html__( '%s', 'hexreport' ), esc_html( ! empty( $total_completed_order_in_three_phases[0] ) ? $total_completed_order_in_three_phases[0] : '' ) ),
				'totalCompletedOredersFromMayToAug' => sprintf( esc_html__( '%s', 'hexreport' ), esc_html( ! empty( $total_completed_order_in_three_phases[1] ) ? $total_completed_order_in_three_phases[1] : '' ) ),
				'totalCompletedOredersFromSepToDec' => sprintf( esc_html__( '%s', 'hexreport' ), esc_html( ! empty( $total_completed_order_in_three_phases[2] ) ? $total_completed_order_in_three_phases[2] : '' ) ),

				'cancelledOrderRation' => sprintf( esc_html__( '%s', 'hexreport' ), esc_html( ! empty( $total_order_ratio[0] ) ? $total_order_ratio[0] : '' ) ),
				'refundedOrderRation' => sprintf( esc_html__( '%s', 'hexreport' ), esc_html( ! empty( $total_order_ratio[1] ) ? $total_order_ratio[1] : '' ) ),
				'failedOrderRation' => sprintf( esc_html__( '%s', 'hexreport' ), esc_html( ! empty( $total_order_ratio[2] ) ? $total_order_ratio[2] : '' ) ),

				'bankTransferRation' => sprintf( esc_html__( '%s', 'hexreport' ), esc_html( ! empty( $count_payment_method_ratio[0] ) ? $count_payment_method_ratio[0] : '' ) ),
				'checkPaymentRatio' => sprintf( esc_html__( '%s', 'hexreport' ), esc_html( ! empty( $count_payment_method_ratio[1] ) ? $count_payment_method_ratio[1] : '' ) ),
				'cashOnDeliveryRatio' => sprintf( esc_html__( '%s', 'hexreport' ), esc_html( ! empty( $count_payment_method_ratio[2] ) ? $count_payment_method_ratio[2] : '' ) ),
				'localPickupRatio' => sprintf( esc_html__( '%s', 'hexreport' ), esc_html( ! empty( $count_payment_method_ratio[3] ) ? $count_payment_method_ratio[3] : '' ) ),
				'flatRateRatio' => sprintf( esc_html__( '%s', 'hexreport' ), esc_html( ! empty( $count_payment_method_ratio[4] ) ? $count_payment_method_ratio[4] : '' ) ),
				'freeShippingRatio' => sprintf( esc_html__( '%s', 'hexreport' ), esc_html( ! empty( $count_payment_method_ratio[5] ) ? $count_payment_method_ratio[5] : '' ) ),
			], 200);
		} else {
			// Nonce verification failed, handle the error
			wp_send_json( [
				'error' => 'Nonce verification failed',
			], 403); // 403 Forbidden status code
		}
	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @since 1.0.0
	 * @method total_sales_amount_for_year
	 * @return array
	 * Get the total sales amount of 12 months from jan-dec
	 */
	public function total_sales_amount_for_year()
	{
		// Get the current year
		$current_year = date( 'Y' );

		// Initialize an array to store monthly sales amounts for completed orders
		$monthly_completed_sales = [];

		// Loop through each month from January to December
		for ( $month = 1; $month <= 12; $month++ ) {
			// Get the first day and last day of the month
			$first_day = "{$current_year}-" . str_pad( $month, 2, '0', STR_PAD_LEFT ) . '-01';
			$last_day = date( 'Y-m-t', strtotime( $first_day ) );

			// Initialize the total completed sales amount for the month
			$total_completed_sales = 0;

			// Get completed orders within the date range for the month
			$completed_orders = wc_get_orders( [
				'status' => 'completed',
				'date_query' => [
					'after' => $first_day,
					'before' => $last_day,
				],
				'limit' => -1, // Retrieve all completed orders
			] );

			// Calculate the total completed sales amount for completed orders
			foreach ( $completed_orders as $order ) {
				$total_completed_sales += $order->get_total();
			}

			// Get refunded orders within the date range for the month
			$refunded_orders = wc_get_orders( [
				'status' => 'refunded',
				'date_query' => [
					'after' => $first_day,
					'before' => $last_day,
				],
				'limit' => -1, // Retrieve all refunded orders
			] );

			// Subtract the refunded amount from the completed sales for the month
			foreach ( $refunded_orders as $order ) {
				$total_completed_sales += $order->get_total();
			}

			// Store the monthly sales amounts in the array
			$monthly_completed_sales[date( 'F', strtotime( $first_day ) )] = $total_completed_sales;
		}

		return $monthly_completed_sales;
	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @since 1.0.0
	 * @method total_visitors_count_for_year
	 * @return array
	 * Get the total number of visitor counts of year starting from jan-dec
	 */
	public function total_visitors_count_for_year()
	{
		$result =
			DB::select('hexreport_visitor_log.January','hexreport_visitor_log.February','hexreport_visitor_log.March','hexreport_visitor_log.April','hexreport_visitor_log.May','hexreport_visitor_log.June','hexreport_visitor_log.July','hexreport_visitor_log.August','hexreport_visitor_log.September','hexreport_visitor_log.October','hexreport_visitor_log.November','hexreport_visitor_log.December')
				->distinct()
				->from('hexreport_visitor_log hexreport_visitor_log')
				->get();

		$totalVisitorsCount = ! empty( $result[0] ) ? $result[0] : [];

		return $totalVisitorsCount;
	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @since 1.0.0
	 * @method total_completed_order_in_three_phases
	 * @return array
	 * Get the total number of completed order in three phases of a year. eg: jan-apr, may-aug, sep-dec
	 */
	public function total_completed_order_in_three_phases()
	{
		// Get the current year
		$current_year = date( 'Y' );

		// Get all completed orders
		$completed_orders = wc_get_orders( [
			'status' => 'completed',
			'limit'  => -1, // Retrieve all orders
		] );

		// Define the date ranges
		$jan_apr_start = new \DateTime("{$current_year}-01-01" );
		$jan_apr_end = new \DateTime( "{$current_year}-04-30" );
		$may_aug_start = new \DateTime( "{$current_year}-05-01" );
		$may_aug_end = new \DateTime( "{$current_year}-08-31" );
		$sep_dec_start = new \DateTime( "{$current_year}-09-01" );
		$sep_dec_end = new \DateTime( "{$current_year}-12-31" );

		// Initialize total amounts for each date range
		$total_amount_jan_apr = 0;
		$total_amount_may_aug = 0;
		$total_amount_sep_dec = 0;

		// Loop through completed orders and calculate totals for each date range
		foreach ( $completed_orders as $order ) {
			$order_date = new \DateTime( $order->get_date_created()->date('Y-m-d') );

			if ( $order_date >= $jan_apr_start && $order_date <= $jan_apr_end ) {
				$total_amount_jan_apr += $order->get_total();
			} elseif ( $order_date >= $may_aug_start && $order_date <= $may_aug_end ) {
				$total_amount_may_aug += $order->get_total();
			} elseif ( $order_date >= $sep_dec_start && $order_date <= $sep_dec_end ) {
				$total_amount_sep_dec += $order->get_total();
			}
		}

		$combined_data = [ $total_amount_jan_apr, $total_amount_may_aug, $total_amount_sep_dec ];

		return $combined_data;
	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @since 1.0.0
	 * @method total_order_ratio
	 * @return array
	 * Get the total order ratio of 'cancelled', 'refunded', 'failed' orders.
	 */
	public function total_order_ratio()
	{
		// Get total orders for customer
		$total_args = [
			'post_type' => 'shop_order',
			'return' => 'ids',
			'limit' => -1, // Retrieve all orders
		];

		$total_orders = count( wc_get_orders( $total_args ) ); // count the array of orders

		// Get CANCELLED orders for customer
		$cancelled_args = [
			'post_status' => ['cancelled'],
			'post_type' => 'shop_order',
			'return' => 'ids',
			'limit' => -1, // Retrieve all orders
		];

		$cancelled_order_numbers = count( wc_get_orders( $cancelled_args ) ); // count the array of orders
		$cancelled_order_ratio = 0 != $total_orders ? $cancelled_order_numbers / $total_orders * 100 : 0;

		// Get refunded orders for customer
		$refunded_args = [
			'post_status' => ['refunded'],
			'post_type' => 'shop_order',
			'return' => 'ids',
			'limit' => -1, // Retrieve all orders
		];
		$refunded_order_numbers = count( wc_get_orders( ( $refunded_args ) ) );
		$refunded_order_ration = 0 != $total_orders ? $refunded_order_numbers / $total_orders * 100 : 0;


		$failed_args = [
			'post_status' => ['failed'],
			'post_type' => 'shop_order',
			'return' => 'ids',
			'limit' => -1, // Retrieve all orders
		];
		$failed_order_numbers = count( wc_get_orders( ( $failed_args ) ) );
		$failed_order_ration = 0 != $total_orders ? $failed_order_numbers / $total_orders * 100 : 0;

		$combined_data = [ $cancelled_order_ratio, $refunded_order_ration, $failed_order_ration ];

		return $combined_data;
	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @since 1.0.0
	 * @method count_payment_method_ratio
	 * @return array
	 * Get the payment method ratio of all orders.
	 */
	public function count_payment_method_ratio()
	{

		$completed_orders = wc_get_orders( [
			'status' => 'completed',
			'limit'  => -1, // Retrieve all orders
		] );

		$total_order_count = 0;
		$payment_method_counts = [];
		$shipping_method_counts = [];

		foreach ( $completed_orders as $order ) {
			if ( $order instanceof \WC_Order ) {
				$payment_method = $order->get_payment_method();
				$shipping_method = $order->get_shipping_method();

				// Increment the count for this payment method
				if ( !empty( $payment_method ) ) {
					if ( !isset( $payment_method_counts[$payment_method] ) ) {
						$payment_method_counts[$payment_method] = 1;
					} else {
						$payment_method_counts[$payment_method]++;
					}
				}

				// Increment the count for shipping method
				if ( !empty( $shipping_method ) ) {
					if ( !isset( $shipping_method_counts[$shipping_method] ) ) {
						$shipping_method_counts[$shipping_method] = 1;
					} else {
						$shipping_method_counts[$shipping_method]++;
					}
				}

				if ( $order->get_status() === 'completed' && !$order->get_parent_id() ) {
					$total_order_count++;
				}
			}
		}

		$direct_bank_transfer_ration =  ! empty( $payment_method_counts['bacs'] ) ? $payment_method_counts['bacs'] / $total_order_count * 100 : 0;

		$check_payment_ration = ! empty( $payment_method_counts['cheque'] ) ? $payment_method_counts['cheque'] / $total_order_count * 100 : 0 ;

		$cash_on_delivery_ration = ! empty( $payment_method_counts['cod'] ) ? $payment_method_counts['cod'] / $total_order_count * 100 : 0;

		$local_pickup_ratio = ! empty( $shipping_method_counts['Local pickup'] ) ? $shipping_method_counts['Local pickup'] / $total_order_count * 100 : 0;
		$flat_rate_ratio = ! empty( $shipping_method_counts['Flat rate'] ) ? $shipping_method_counts['Flat rate'] / $total_order_count * 100 : 0;
		$free_shipping_ratio = ! empty( $shipping_method_counts['Free shipping'] ) ? $shipping_method_counts['Free shipping'] / $total_order_count * 100 : 0;

		$combined_data = [ $direct_bank_transfer_ration, $check_payment_ration, $cash_on_delivery_ration, $local_pickup_ratio, $flat_rate_ratio, $free_shipping_ratio ];

		return $combined_data;
	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @since 1.0.0
	 * @method show_first_top_selling_product_monthly_data
	 * @return void
	 * Get the first top-selling product monthly data.
	 */
	public function show_first_top_selling_product_monthly_data()
	{
		// Get all completed orders
		$completed_orders = wc_get_orders( [
			'status' => 'completed',
		] );

		// Create a multidimensional array to store monthly product sales count
		$monthly_product_sales_count = [];

		// Loop through each completed order
		foreach ( $completed_orders as $order ) {
			// Check if the order is a refund or part of a refunded order, and skip it if true
			if ( !$order->get_parent_id() ) {
				// Get order items
				$order_items = $order->get_items();

				// Get the order date
				$order_date = $order->get_date_created();
				$month_key = $order_date->format( 'n' ); // 'n' format gives the month without leading zeros

				// Initialize array for the month if not already set
				if ( !isset( $monthly_product_sales_count[$month_key] ) ) {
					$monthly_product_sales_count[$month_key] = array();
				}

				// Loop through order items
				foreach ( $order_items as $item ) {
					// Get the product ID and quantity
					$product_id = $item->get_product_id();
					$quantity = $item->get_quantity();

					// Increment the monthly product sales count
					if ( isset( $monthly_product_sales_count[$month_key][$product_id] ) ) {
						$monthly_product_sales_count[$month_key][$product_id] += $quantity;
					} else {
						$monthly_product_sales_count[$month_key][$product_id] = $quantity;
					}
				}
			}
		}

		// Create an array to store the monthly quantities for the top-selling product
		$monthly_quantities_top_selling = [];

		// Loop through each month
		$month_names = [
			1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June',
			7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
		];

		foreach ( $month_names as $month => $month_name ) {
			// Check if the array for the current month exists
			if ( isset( $monthly_product_sales_count[$month] ) ) {
				// Sort the products for the current month in descending order
				arsort( $monthly_product_sales_count[$month] );

				// Get the product ID of the top-selling product for the current month
				$top_selling_product_id = key( $monthly_product_sales_count[$month] );

				// Store the quantity for the top-selling product
				$monthly_quantities_top_selling[$month_name] = $monthly_product_sales_count[$month][$top_selling_product_id];
			} else {
				// If there is no data for the month, store 0
				$monthly_quantities_top_selling[$month_name] = 0;
			}
		}

		$monthly_quantities_second_top_selling = $this->get_second_top_product_monthly_data();

		$get_top_two_selling_product_name = $this->get_top_two_selling_product_name();

		$get_top_selling_product_and_categoreis = $this->get_top_selling_product_and_categoreis();

		$topSellingCategoreisNames = ! empty( $get_top_selling_product_and_categoreis[0] ) ? $get_top_selling_product_and_categoreis[0] : [];
		$topSellingCategoreisCount = ! empty( $get_top_selling_product_and_categoreis[1] ) ? $get_top_selling_product_and_categoreis[1] : [];
		$categoriesSalesRatio = ! empty( $get_top_selling_product_and_categoreis[2] ) ? $get_top_selling_product_and_categoreis[2] : [];
		$topSellingProductsNames = ! empty( $get_top_selling_product_and_categoreis[3] ) ? $get_top_selling_product_and_categoreis[3] : [];
		$topSellingProductsCount = ! empty( $get_top_selling_product_and_categoreis[4] ) ? $get_top_selling_product_and_categoreis[4] : [];
		$productSaleRatio = ! empty( $get_top_selling_product_and_categoreis[5] ) ? $get_top_selling_product_and_categoreis[5] : [];

		// Check the nonce and action
		if ( $this->verify_nonce() ) {
			// Nonce is valid, proceed with your code
			wp_send_json( [
				// Response data here
				'msg' => __( 'hello', 'hexreport' ),
				'type' => 'success',
				'firstTopSellingProductMonthlyData' => array_map( 'esc_html', $monthly_quantities_top_selling ),

				'secondTopSellingProductMonthlyData' => array_map( 'esc_html', $monthly_quantities_second_top_selling ),

				'firstTopSellingProductName' => sprintf( __( '%s', 'hexreport' ), esc_html( ! empty( $get_top_two_selling_product_name[0] ) ? $get_top_two_selling_product_name[0] : '' ) ),
				'secondTopSellingProductName' => sprintf( __( '%s', 'hexreport' ), esc_html( ! empty( $get_top_two_selling_product_name[1] ) ? $get_top_two_selling_product_name[1] : '' ) ),

				'topSellingCategoreisNames' => array_map( 'esc_html', $topSellingCategoreisNames ),
				'topSellingCategoreisCount' => array_map( 'esc_html', $topSellingCategoreisCount ),
				'categoriesSalesRatio' => array_map( 'esc_html', $categoriesSalesRatio ),
				'topSellingProductsNames' => array_map( 'esc_html', $topSellingProductsNames ),
				'topSellingProductsCount' => array_map( 'esc_html', $topSellingProductsCount ),
				'productSaleRatio' => array_map( 'esc_html', $productSaleRatio ),
			], 200);
		} else {
			// Nonce verification failed, handle the error
			wp_send_json( [
				'error' => 'Nonce verification failed',
			], 403); // 403 Forbidden status code
		}

	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @since 1.0.0
	 * @method get_second_top_product_monthly_data
	 * @return array
	 * Get the second top-selling product monthly data.
	 */
	public function get_second_top_product_monthly_data()
	{
		// Get all completed orders
		$completed_orders = wc_get_orders( [
			'status' => 'completed',
		] );

		// Create a multidimensional array to store monthly product sales count
		$monthly_product_sales_count = [];

		// Loop through each completed order
		foreach ( $completed_orders as $order ) {
			// Check if the order is a refund or part of a refunded order, and skip it if true
			if ( !$order->get_parent_id() ) {
				// Get order items
				$order_items = $order->get_items();

				// Get the order date
				$order_date = $order->get_date_created();
				$month_key = $order_date->format('n'); // 'n' format gives the month without leading zeros

				// Initialize array for the month if not already set
				if ( !isset( $monthly_product_sales_count[$month_key] ) ) {
					$monthly_product_sales_count[$month_key] = [];
				}

				// Loop through order items
				foreach ( $order_items as $item ) {
					// Get the product ID and quantity
					$product_id = $item->get_product_id();
					$quantity = $item->get_quantity();

					// Increment the monthly product sales count
					if ( isset( $monthly_product_sales_count[$month_key][$product_id] ) ) {
						$monthly_product_sales_count[$month_key][$product_id] += $quantity;
					} else {
						$monthly_product_sales_count[$month_key][$product_id] = $quantity;
					}
				}
			}
		}

		// Create an array to store the monthly quantities for the second top-selling product
		$monthly_quantities_second_top_selling = [];

		// Loop through each month
		$month_names = [
			1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June',
			7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
		];

		foreach ( $month_names as $month => $month_name ) {
			// Check if the array for the current month exists
			if ( isset( $monthly_product_sales_count[$month] ) ) {
				// Sort the products for the current month in descending order
				arsort( $monthly_product_sales_count[$month] );

				// Skip if there's only one product for the month
				if ( count( $monthly_product_sales_count[$month] ) < 2 ) {
					$monthly_quantities_second_top_selling[$month_name] = 0;
					continue;
				}

				// Skip the top-selling product
				$top_product_id = key( $monthly_product_sales_count[$month] );
				next( $monthly_product_sales_count[$month] );

				// Get the product ID of the second top-selling product for the current month
				$second_top_selling_product_id = key( $monthly_product_sales_count[$month] );

				// Store the quantity for the second top-selling product
				$monthly_quantities_second_top_selling[$month_name] = $monthly_product_sales_count[$month][$second_top_selling_product_id];
			} else {
				// If there is no data for the month, store 0
				$monthly_quantities_second_top_selling[$month_name] = 0;
			}
		}

		return $monthly_quantities_second_top_selling;
	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @since 1.0.0
	 * @method get_top_two_selling_product_name
	 * @return array
	 * Get the top two selling product names;
	 */
	public function get_top_two_selling_product_name()
	{
		// Get orders
		$orders = wc_get_orders( [
			'status' => 'completed', // You can adjust the status based on your requirements
			'limit'  => -1, // To retrieve all orders
		] );

		// Initialize an empty array to store product sales data
		$product_sales = [];

		// Loop through each order
		foreach ( $orders as $order ) {
			// Get order items
			$items = $order->get_items();

			// Loop through each item in the order
			foreach ( $items as $item ) {
				$product_id    = $item->get_product_id();
				$product_qty   = $item->get_quantity();

				// Update product sales data
				if ( isset( $product_sales[ $product_id ] ) ) {
					$product_sales[ $product_id ] += $product_qty;
				} else {
					$product_sales[ $product_id ] = $product_qty;
				}
			}
		}

		// Sort the products based on sales in descending order
		arsort( $product_sales );

		// Get the top two selling product IDs
		$top_two_product_ids = array_slice( array_keys( $product_sales ), 0, 2 );

		// Get the product names based on the IDs
		$top_two_product_names = [];

		foreach ( $top_two_product_ids as $product_id ) {
			$product = wc_get_product( $product_id );
			$top_two_product_names[] = $product->get_name();
		}

		$firstProductName = ! empty( $top_two_product_names[0] ) ? $top_two_product_names[0] : '';
		$secondProductName = ! empty( $top_two_product_names[1] ) ? $top_two_product_names[1] : '';

		$combined_data = [ $firstProductName, $secondProductName ];

		return $combined_data;
	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @since 1.0.0
	 * @method get_top_selling_product_and_categoreis
	 * @return array
	 * Get the first top-selling product monthly data.
	 */
	public function get_top_selling_product_and_categoreis()
	{
		// Initialize arrays to store category names and sales count
		$product_sales = [];
		$category_sales = [];
		$total_order_count = 0; // Initialize the order count

		// Set the number of categories to retrieve (top 10)
		$limit = 10;

		// Get completed orders
		$completed_orders = wc_get_orders( [
			'status' => 'completed',
		] );

		// Iterate through completed orders
		foreach ( $completed_orders as $order ) {
			$total_order_count++; // Increment the order count

			$order_items = $order->get_items();

			foreach ( $order_items as $item ) {
				$product_id = $item->get_product_id();
				$product = wc_get_product( $product_id );
				$product_categories = $product->get_category_ids();
				$product_name = $product->get_name(); // Get the product name

				if ( array_key_exists( $product_name, $product_sales ) ) {
					$product_sales[$product_name] += $item->get_quantity();
				} else {
					$product_sales[$product_name] = $item->get_quantity();
				}

				foreach ( $product_categories as $category_id ) {
					$category_name = get_term( $category_id, 'product_cat' )->name;

					if ( array_key_exists( $category_name, $category_sales ) ) {
						$category_sales[$category_name] += $item->get_quantity();
					} else {
						$category_sales[$category_name] = $item->get_quantity();
					}
				}
			}
		}

		// Sort products by sales count in descending order
		arsort( $product_sales );

		// Limit to the top 10 products
		$product_sales = array_slice( $product_sales, 0, $limit, true );

		// Populate the arrays
		$product_names = array_keys( $product_sales );
		$product_sales = array_values( $product_sales );

		// Sort categories by sales count in descending order
		arsort( $category_sales );

		// Limit to the top 10 categories
		$category_sales = array_slice( $category_sales, 0, $limit, true );

		// Populate the arrays
		$category_names = array_keys( $category_sales );
		$category_sales = array_values( $category_sales );

		$product_sale_ratio = [];

		foreach ( $product_sales as $single_item ) {
			$product_sale_ratio[] = $single_item / $total_order_count * 100;
		}

		$category_sales_ratio = [];

		foreach ( $category_sales as $single_item ) {
			$category_sales_ratio[] = $single_item / $total_order_count * 100;
		}

		$combined_value = [ $category_names, $category_sales, $category_sales_ratio, $product_names, $product_sales, $product_sale_ratio ];

		return $combined_value;
	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @since 1.0.0
	 * @method get_top_two_selling_categories_names
	 * @return void
	 * Get the first top-selling product categories names.
	 */
	public function get_top_two_selling_categories_names()
	{
		// Get the top two selling product categories
		$top_categories = [];

		// Query to get product categories and their total sales
		$args = [
			'post_type' => 'product',
			'posts_per_page' => -1,
		];

		$query = new \WP_Query( $args );

		if ( $query->have_posts() ) {
			$category_sales = [];

			while ( $query->have_posts() ) {
				$query->the_post();
				global $product;

				// Get product categories for the current product
				$product_categories = wp_get_post_terms( get_the_ID(), 'product_cat' );

				foreach ( $product_categories as $category ) {
					$category_name = $category->name;

					// Calculate total sales for the category
					$total_sales = $product->get_total_sales();

					// Update or initialize the total sales for the category
					if ( isset( $category_sales[$category_name] ) ) {
						$category_sales[$category_name] += $total_sales;
					} else {
						$category_sales[$category_name] = $total_sales;
					}
				}
			}

			// Sort the categories by total sales in descending order
			arsort( $category_sales );

			// Get the top two categories
			$top_categories = array_slice( array_keys( $category_sales ), 0, 2 );
		}

		// Restore the original post data
		wp_reset_postdata();

		$firstCatName = ! empty( $top_categories[0] ) ? $top_categories[0] : '';
		$secondCatName = ! empty( $top_categories[1] ) ? $top_categories[1] : '';

		$get_top_two_categories_monthly_data = $this->get_top_two_categories_monthly_data();

		$firstCatMonthData = ! empty( $get_top_two_categories_monthly_data[0] ) ? $get_top_two_categories_monthly_data[0] : [];

		$secondCatMonthData = ! empty( $get_top_two_categories_monthly_data[1] ) ? $get_top_two_categories_monthly_data[1] : [];

		// Check the nonce and action
		if ( $this->verify_nonce() ) {
			// Nonce is valid, proceed with your code
			wp_send_json( [
				// Response data here
				'msg' => __( 'hello', 'hexreport' ),
				'type' => 'success',
				'firstCatName' => sprintf( __( '%s', 'hexreport' ), esc_html( $firstCatName ) ),
				'secondCatName' => sprintf( __( '%s', 'hexreport' ), esc_html( $secondCatName ) ),

				'firstCatMonthData' => array_map( 'esc_html', $firstCatMonthData ),
				'secondCatMonthData' => array_map( 'esc_html', $secondCatMonthData ),
			], 200);
		} else {
			// Nonce verification failed, handle the error
			wp_send_json( [
				'error' => 'Nonce verification failed',
			], 403); // 403 Forbidden status code
		}
	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @since 1.0.0
	 * @method get_top_two_selling_categories_names
	 * @return array
	 * Get the first top-selling product categories names.
	 */
	public function get_top_two_categories_monthly_data()
	{
		$completed_orders = wc_get_orders( [
			'status' => 'completed',
		] );

		// Initialize arrays to store data for the top two categories
		$top_selling_category_data = [];
		$second_top_selling_category_data = [];

		// Initialize an array to count month-wise sales for categories
		$monthly_category_sales = [];

		foreach ( $completed_orders as $order ) {
			foreach ( $order->get_items() as $item ) {
				$product = $item->get_product();
				$categories = wp_get_post_terms( $product->get_id(), 'product_cat', [ 'fields' => 'names' ] );

				foreach ( $categories as $category ) {
					$month = date( 'F', strtotime( $order->get_date_completed()->format( 'Y-m-d H:i:s' ) ) );

					if ( !isset( $monthly_category_sales[$category][$month] ) ) {
						$monthly_category_sales[$category][$month] = 0;
					}
					$monthly_category_sales[$category][$month] += $item->get_quantity();
				}
			}
		}

		// Sort categories by total sales
		arsort( $monthly_category_sales );

		// Get the top two selling categories
		$top_categories = array_keys( $monthly_category_sales );

		if ( isset( $top_categories[0] ) ) {
			$top_category_data = $monthly_category_sales[$top_categories[0]];
		} else {
			$top_category_data = [];
		}

		if ( isset( $top_categories[1] ) ) {
			$second_top_category_data = $monthly_category_sales[$top_categories[1]];
		} else {
			$second_top_category_data = [];
		}

		// Create an array of month names
		$months = [
			'January', 'February', 'March', 'April', 'May', 'June',
			'July', 'August', 'September', 'October', 'November', 'December'
		];

		// Create arrays for the top two selling categories
		foreach ( $top_categories as $category ) {
			if ( $category === $top_categories[0] ) {
				$top_selling_category_data[$category] = [];
				foreach ( $months as $month ) {
					if ( isset( $top_category_data[$month] ) ) {
						$top_selling_category_data[$category][$month] = $top_category_data[$month];
					} else {
						$top_selling_category_data[$category][$month] = 0;
					}
				}
			} elseif ( $category === $top_categories[1] ) {
				$second_top_selling_category_data[$category] = [];
				foreach ( $months as $month ) {
					if ( isset( $second_top_category_data[$month] ) ) {
						$second_top_selling_category_data[$category][$month] = $second_top_category_data[$month];
					} else {
						$second_top_selling_category_data[$category][$month] = 0;
					}
				}
			}
		}

		$final_data_1 = [];
		$final_data_2 = [];

		foreach ( $top_selling_category_data as $key ) {
			foreach( $key as $sinlge_value ) {
				$final_data_1[] = $sinlge_value;
			}
		}

		foreach ( $second_top_selling_category_data as $key ) {
			foreach( $key as $sinlge_value ) {
				$final_data_2[] = $sinlge_value;
			}
		}

		$combined_data = [ $final_data_1, $final_data_2 ];

		return $combined_data;
	}

	/**
	 * @package hexreport
	 * @author WpHex
	 * @since 1.0.0
	 * @method verify_nonce
	 * @return mixed
	 * Verify the nonce
	 */
	private function verify_nonce()
	{
		return isset( $_GET['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_GET['nonce'] ) ) , 'hexReportData-react_nonce' ) == 1 ;
	}
}
