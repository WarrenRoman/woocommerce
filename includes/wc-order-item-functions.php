<?php
/**
 * WooCommerce Order Item Functions
 *
 * Functions for order specific things.
 *
 * @author      WooThemes
 * @category    Core
 * @package     WooCommerce/Functions
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add a item to an order (for example a line item).
 *
 * @access public
 * @param int $order_id
 * @return mixed
 */
function wc_add_order_item( $order_id, $item ) {
	$order_id = absint( $order_id );

	if ( ! $order_id )
		return false;

	$defaults = array(
		'order_item_name'       => '',
		'order_item_type'       => 'line_item',
	);

	$item = wp_parse_args( $item, $defaults );

	$data_store = WC_Data_Store::load( 'order-item' );
	$item_id    = $data_store->add_order_item( $order_id, $item );

	do_action( 'woocommerce_new_order_item', $item_id, $item, $order_id );

	return $item_id;
}

/**
 * Update an item for an order.
 *
 * @since 2.2
 * @param int $item_id
 * @param array $args either `order_item_type` or `order_item_name`
 * @return bool true if successfully updated, false otherwise
 */
function wc_update_order_item( $item_id, $args ) {
	$data_store = WC_Data_Store::load( 'order-item' );
	$update     = $data_store->update_order_item( $item_id, $args );

	if ( false === $update ) {
		return false;
	}

	do_action( 'woocommerce_update_order_item', $item_id, $args );

	return true;
}

/**
 * Delete an item from the order it belongs to based on item id.
 *
 * @access public
 * @param int $item_id
 * @return bool
 */
function wc_delete_order_item( $item_id ) {
	$item_id    = absint( $item_id );
	$data_store = WC_Data_Store::load( 'order-item' );

	if ( ! $item_id ) {
		return false;
	}

	do_action( 'woocommerce_before_delete_order_item', $item_id );

	$data_store->delete_order_item( $item_id );

	do_action( 'woocommerce_delete_order_item', $item_id );

	return true;
}

/**
 * WooCommerce Order Item Meta API - Update term meta.
 *
 * @access public
 * @param mixed $item_id
 * @param mixed $meta_key
 * @param mixed $meta_value
 * @param string $prev_value (default: '')
 * @return bool
 */
function wc_update_order_item_meta( $item_id, $meta_key, $meta_value, $prev_value = '' ) {
	$data_store = WC_Data_Store::load( 'order-item' );
	if ( $data_store->update_metadata( $item_id, $meta_key, $meta_value, $prev_value ) ) {
		$cache_key = WC_Cache_Helper::get_cache_prefix( 'orders' ) . 'item_meta_array_' . $item_id;
		wp_cache_delete( $cache_key, 'orders' );
		return true;
	}
	return false;
}

/**
 * WooCommerce Order Item Meta API - Add term meta.
 *
 * @access public
 * @param mixed $item_id
 * @param mixed $meta_key
 * @param mixed $meta_value
 * @param bool $unique (default: false)
 * @return int New row ID or 0
 */
function wc_add_order_item_meta( $item_id, $meta_key, $meta_value, $unique = false ) {
	$data_store = WC_Data_Store::load( 'order-item' );
	if ( $meta_id = $data_store->add_metadata( $item_id, $meta_key, $meta_value, $unique ) ) {
		$cache_key = WC_Cache_Helper::get_cache_prefix( 'orders' ) . 'item_meta_array_' . $item_id;
		wp_cache_delete( $cache_key, 'orders' );
		return $meta_id;
	}
	return 0;
}

/**
 * WooCommerce Order Item Meta API - Delete term meta.
 *
 * @access public
 * @param mixed $item_id
 * @param mixed $meta_key
 * @param string $meta_value (default: '')
 * @param bool $delete_all (default: false)
 * @return bool
 */
function wc_delete_order_item_meta( $item_id, $meta_key, $meta_value = '', $delete_all = false ) {
	$data_store = WC_Data_Store::load( 'order-item' );
	if ( $data_store->delete_metadata( $item_id, $meta_key, $meta_value, $delete_all ) ) {
		$cache_key = WC_Cache_Helper::get_cache_prefix( 'orders' ) . 'item_meta_array_' . $item_id;
		wp_cache_delete( $cache_key, 'orders' );
		return true;
	}
	return false;
}

/**
 * WooCommerce Order Item Meta API - Get term meta.
 *
 * @access public
 * @param mixed $item_id
 * @param mixed $key
 * @param bool $single (default: true)
 * @return mixed
 */
function wc_get_order_item_meta( $item_id, $key, $single = true ) {
	$data_store = WC_Data_Store::load( 'order-item' );
	return $data_store->get_metadata( $item_id, $key, $single );
}

/**
 * Get order ID by order item ID.
 *
 * @param  int $item_id
 * @return int
 */
function wc_get_order_id_by_order_item_id( $item_id ) {
	$data_store = WC_Data_Store::load( 'order-item' );
	return $data_store->get_order_id_by_order_item_id( $item_id );
}
