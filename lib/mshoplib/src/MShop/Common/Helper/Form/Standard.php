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
 * Default implementation of the form helper
 *
 * @package MShop
 * @subpackage Common
 */
class Standard implements \Aimeos\MShop\Common\Helper\Form\Iface
{
	private $url;
	private $method;
	private $values;
	private $external;
	private $html;


	/**
	 * Initializes the object.
	 *
	 * @param string $url Initial url
	 * @param string $method Initial method (e.g. post or get)
	 * @param \Aimeos\MW\Criteria\Attribute\Iface[] $values Form parameter items
	 * @param bool $external True if URL points to an external site, false if it stays on the same site
	 * @param string $html Custom HTML for rendering form (e.g. Including JS or custom html)
	 */
	public function __construct( string $url = '', string $method = '', array $values = [], bool $external = true, string $html = '' )
	{
		\Aimeos\MW\Common\Base::checkClassList( \Aimeos\MW\Criteria\Attribute\Iface::class, $values );

		$this->url = $url;
		$this->external = $external;
		$this->method = $method;
		$this->values = $values;
		$this->html = $html;
	}


	/**
	 * Returns if the URL points to an external site.
	 *
	 * @return bool True if URL points to an external site, false if it stays on the same site
	 */
	public function getExternal() : bool
	{
		return $this->external;
	}


	/**
	 * Sets if the URL points to an external site.
	 *
	 * @param bool $value True if URL points to an external site, false if it stays on the same site
	 * @return \Aimeos\MShop\Common\Helper\Form\Iface Helper for chaining method calls
	 */
	public function setExternal( bool $value ) : \Aimeos\MShop\Common\Helper\Form\Iface
	{
		$this->external = (bool) $value;

		return $this;
	}


	/**
	 * Returns the custom HTML string.
	 *
	 * @return string HTML string
	 */
	public function getHtml() : string
	{
		return $this->html;
	}


	/**
	 * Sets the custom HTML string.
	 *
	 * @param string $html HTML string
	 * @return \Aimeos\MShop\Common\Helper\Form\Iface Helper for chaining method calls
	 */
	public function setHtml( string $html ) : \Aimeos\MShop\Common\Helper\Form\Iface
	{
		$this->html = $html;
		return $this;
	}


	/**
	 * Returns the method.
	 *
	 * @return string Method
	 */
	public function getMethod() : string
	{
		return $this->method;
	}


	/**
	 * Sets the method.
	 *
	 * @param string $method Method
	 * @return \Aimeos\MShop\Common\Helper\Form\Iface Helper for chaining method calls
	 */
	public function setMethod( string $method ) : \Aimeos\MShop\Common\Helper\Form\Iface
	{
		$this->method = $method;
		return $this;
	}


	/**
	 * Returns the url.
	 *
	 * @return string Url
	 */
	public function getUrl() : string
	{
		return $this->url;
	}


	/**
	 * Sets the url.
	 *
	 * @param string $url Url
	 * @return \Aimeos\MShop\Common\Helper\Form\Iface Helper for chaining method calls
	 */
	public function setUrl( string $url ) : \Aimeos\MShop\Common\Helper\Form\Iface
	{
		$this->url = $url;
		return $this;
	}


	/**
	 * Returns the value for the given key.
	 *
	 * @param string $key Unique key
	 * @return \Aimeos\MW\Criteria\Attribute\Iface Attribute item for the given key
	 */
	public function getValue( string $key ) : \Aimeos\MW\Criteria\Attribute\Iface
	{
		if( !isset( $this->values[$key] ) ) {
			return null;
		}

		return $this->values[$key];
	}


	/**
	 * Sets the value for the key.
	 *
	 * @param string $key Unique key
	 * @param \Aimeos\MW\Criteria\Attribute\Iface $value Attribute item for the given key
	 * @return \Aimeos\MShop\Common\Helper\Form\Iface Helper for chaining method calls
	 */
	public function setValue( string $key, \Aimeos\MW\Criteria\Attribute\Iface $value ) : \Aimeos\MShop\Common\Helper\Form\Iface
	{
		$this->values[$key] = $value;
		return $this;
	}


	/**
	 * Returns the all key/value pairs.
	 *
	 * @return array Key/value pairs, values implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getValues() : array
	{
		return $this->values;
	}
}
