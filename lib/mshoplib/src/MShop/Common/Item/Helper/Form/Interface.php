<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
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
	 * @return void
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
	 * @return void
	 */
	public function setMethod( $method );


	/**
	 * Returns the value for the given key.
	 *
	 * @param string $key Unique key
	 * @return MW_Common_Criteria_Attribute_Interface Attribute item for the given key
	 */
	public function getValue( $key );


	/**
	 * Sets the value for the key.
	 *
	 * @param string $key Unique key
	 * @param MW_Common_Criteria_Attribute_Interface $value Attribute item for the given key
	 */
	public function setValue( $key, MW_Common_Criteria_Attribute_Interface $value );


	/**
	 * Returns the all key/value pairs.
	 *
	 * @return array Key/value pairs, values implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getValues();
}
