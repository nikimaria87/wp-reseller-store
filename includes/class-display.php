<?php

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Display {

	/**
	 * Class constructor.
	 *
	 * @since NEXT
	 */
	public function __construct() {

		add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ] );

	}

	/**
	 * Enqueue front-end scripts.
	 *
	 * @action wp_enqueue_scripts
	 * @since  NEXT
	 */
	public function wp_enqueue_scripts() {

		$rtl = is_rtl() ? '-rtl' : '';

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'rstore', Plugin::assets_url( "css/store{$rtl}{$suffix}.css" ), [ 'dashicons' ], rstore()->version );

		wp_enqueue_script( 'js-cookie', Plugin::assets_url( "js/js-cookie{$suffix}.js" ), [], '2.1.3', true );
		wp_enqueue_script( 'rstore', Plugin::assets_url( "js/store{$suffix}.js" ), [ 'jquery', 'js-cookie' ], rstore()->version, true );

		/**
		 * Filter the TTL for cookies (in seconds).
		 *
		 * @since NEXT
		 *
		 * @var int
		 */
		$cookie_ttl = (int) apply_filters( 'rstore_cookie_ttl', DAY_IN_SECONDS * 30 );

		$data = [
			'pl_id'   => (int) rstore_get_option( 'pl_id' ),
			'urls'    => [
				'cart'     => esc_url( rstore()->api->urls['cart'] ),
				'cart_api' => esc_url( rstore()->api->url( 'cart/{pl_id}' ) ),
			],
			'cookies' => [
				'ttl'       => absint( $cookie_ttl ) * 1000, // Convert seconds to ms
				'cartCount' => rstore_prefix( 'cart-count', true ),
			],
			'product' => [
				'id' => ( Post_Type::SLUG === get_post_type() ) ? rstore_get_product_meta( get_the_ID(), 'id', '' ) : '',
			],
			'i18n'    => [
				'view_cart' => esc_html__( 'View cart', 'reseller-store' ),
				'error'     => esc_html__( 'An unknown error has occurred', 'reseller-store' ),
			],
		];

		wp_localize_script( 'rstore', 'rstore', $data );

	}

}
