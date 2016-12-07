<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\Helper\Form;


/**
 * Generic interface for the helper form item.
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
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
	 * @return \Aimeos\MShop\Common\Item\Helper\Form\Iface Item for chaining method calls
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
	 * @return \Aimeos\MShop\Common\Item\Helper\Form\Iface Item for chaining method calls
	 */
	public function setMethod( $method );


	/**
	 * Returns the value for the given key.
	 *
	 * @param string $key Unique key
	 * @return \Aimeos\MW\Criteria\Attribute\Iface Attribute item for the given key
	 */
	public function getValue( $key );


	/**
	 * Sets the value for the key.
	 *
	 * @param string $key Unique key
	 * @param \Aimeos\MW\Criteria\Attribute\Iface $value Attribute item for the given key
	 * @return \Aimeos\MShop\Common\Item\Helper\Form\Iface Item for chaining method calls
	 */
	public function setValue( $key, \Aimeos\MW\Criteria\Attribute\Iface $value );


	/**
	 * Returns the all key/value pairs.
	 *
	 * @return array Key/value pairs, values implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getValues();
}
