<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 * @version $Id: Interface.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Generic interface for the helper form item.
 *
 * @package MShop
 * @subpackage Common
 */
interface MShop_Common_Item_Helper_Form_Interface
{
	/**
	 * Returns the url.
	 *
	 * @return string Url
	 */
	public function getUrl();


	/**
	 * Sets the url.
	 *
	 * @param string $url Url
	 */
	public function setUrl( $url );


	/**
	 * Returns the method.
	 *
	 * @return string Method
	 */
	public function getMethod();


	/**
	 * Sets the method.
	 *
	 * @param string $method Method
	 */
	public function setMethod( $method );


	/**
	 * Returns the value for the given key.
	 *
	 * @param string $key Key of value
	 * @return mixed Value for the given key
	 */
	public function getValue( $key );


	/**
	 * Sets the value for the key.
	 *
	 * @param string $key Key of value
	 * @param string $value Value for the given key
	 */
	public function setValue( $key, $value );


	/**
	 * Returns the all key/ value pairs.
	 *
	 * @return array Key/ value pairs
	 */
	public function getValues();
}
