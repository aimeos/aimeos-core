<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Helper\Form;


/**
 * Generic interface for the form helper
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
{
	/**
	 * Returns if the URL points to an external site.
	 *
	 * @return bool True if URL points to an external site, false if it stays on the same site
	 */
	public function getExternal() : bool;

	/**
	 * Sets if the URL points to an external site.
	 *
	 * @param bool $value True if URL points to an external site, false if it stays on the same site
	 * @return \Aimeos\MShop\Common\Helper\Form\Iface Helper for chaining method calls
	 */
	public function setExternal( bool $value ) : \Aimeos\MShop\Common\Helper\Form\Iface;

	/**
	 * Returns the custom HTML string.
	 *
	 * @return string HTML string
	 */
	public function getHtml() : string;

	/**
	 * Sets the custom HTML string.
	 *
	 * @param string $html HTML string
	 * @return \Aimeos\MShop\Common\Helper\Form\Iface Helper for chaining method calls
	 */
	public function setHtml( string $html ) : \Aimeos\MShop\Common\Helper\Form\Iface;

	/**
	 * Returns the method.
	 *
	 * @return string Method
	 */
	public function getMethod() : string;

	/**
	 * Sets the method.
	 *
	 * @param string $method Method
	 * @return \Aimeos\MShop\Common\Helper\Form\Iface Helper for chaining method calls
	 */
	public function setMethod( string $method ) : \Aimeos\MShop\Common\Helper\Form\Iface;

	/**
	 * Returns the url.
	 *
	 * @return string Url
	 */
	public function getUrl() : string;

	/**
	 * Sets the url.
	 *
	 * @param string $url Url
	 * @return \Aimeos\MShop\Common\Helper\Form\Iface Helper for chaining method calls
	 */
	public function setUrl( string $url ) : \Aimeos\MShop\Common\Helper\Form\Iface;

	/**
	 * Returns the value for the given key.
	 *
	 * @param string $key Unique key
	 * @return \Aimeos\MW\Criteria\Attribute\Iface Attribute item for the given key
	 */
	public function getValue( string $key ) : \Aimeos\MW\Criteria\Attribute\Iface;

	/**
	 * Sets the value for the key.
	 *
	 * @param string $key Unique key
	 * @param \Aimeos\MW\Criteria\Attribute\Iface $value Attribute item for the given key
	 * @return \Aimeos\MShop\Common\Helper\Form\Iface Helper for chaining method calls
	 */
	public function setValue( string $key, \Aimeos\MW\Criteria\Attribute\Iface $value ) : \Aimeos\MShop\Common\Helper\Form\Iface;

	/**
	 * Returns the all key/value pairs.
	 *
	 * @return array Key/value pairs, values implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getValues() : array;
}
