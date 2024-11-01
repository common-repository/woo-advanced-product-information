<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_ADVANCED_PRODUCT_INFORMATION_Data {
	private $params;

	/**
	 * WC_ADVANCED_PRODUCT_INFORMATION_Data constructor.
	 * Init setting
	 */
	public function __construct() {
		global $wapi_settings;
		if ( ! $wapi_settings ) {
			$wapi_settings = get_option( '_wapi_settings', array() );
		}
		$this->params = $wapi_settings;
	}

	public function get_params( $name = '' ) {
		$params = array();
		if ( ! $name ) {
			$params = $this->params;
		} elseif ( isset( $this->params[ $name ] ) ) {
			$params = $this->params[ $name ];
		}

		return $params;
	}

}

new WC_ADVANCED_PRODUCT_INFORMATION_Data();