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
 * Default implementation of the helper form item.
 *
 * @package MShop
 * @subpackage Common
 */
class Standard implements \Aimeos\MShop\Common\Item\Helper\Form\Iface
{
	private $url;
	private $method;
	private $values;
	private $external;


	/**
	 * Initializes the object.
	 *
	 * @param string $url Initial url
	 * @param string $method Initial method (e.g. post or get)
	 * @param array $values Form parameters implementing \Aimeos\MW\Criteria\Attribute\Iface
	 * @param boolean $external True if URL points to an external site, false if it stays on the same site
	 */
	public function __construct( $url = '', $method = '', array $values = [], $external = true )
	{
		\Aimeos\MW\Common\Base::checkClassList( '\\Aimeos\\MW\\Criteria\\Attribute\\Iface', $values );

		$this->url = (string) $url;
		$this->external = (bool) $external;
		$this->method = (string) $method;
		$this->values = $values;
	}


	/**
	 * Returns if the URL points to an external site.
	 *
	 * @return boolean True if URL points to an external site, false if it stays on the same site
	 */
	public function getExternal()
	{
		return $this->external;
	}


	/**
	 * Sets if the URL points to an external site.
	 *
	 * @param boolean $value True if URL points to an external site, false if it stays on the same site
	 * @return \Aimeos\MShop\Common\Item\Helper\Form\Iface Item for chaining method calls
	 */
	public function setExternal( $value )
	{
		$this->external = (bool) $value;

		return $this;
	}


	/**
	 * Returns the url.
	 *
	 * @return string Url
	 */
	public function getUrl()
	{
		return $this->url;
	}


	/**
	 * Sets the url.
	 *
	 * @param string $url Url
	 * @return \Aimeos\MShop\Common\Item\Helper\Form\Iface Item for chaining method calls
	 */
	public function setUrl( $url )
	{
		$this->url = (string) $url;

		return $this;
	}


	/**
	 * Returns the method.
	 *
	 * @return string Method
	 */
	public function getMethod()
	{
		return $this->method;
	}


	/**
	 * Sets the method.
	 *
	 * @param string $method Method
	 * @return \Aimeos\MShop\Common\Item\Helper\Form\Iface Item for chaining method calls
	 */
	public function setMethod( $method )
	{
		$this->method = (string) $method;

		return $this;
	}


	/**
	 * Returns the value for the given key.
	 *
	 * @param string $key Unique key
	 * @return \Aimeos\MW\Criteria\Attribute\Iface Attribute item for the given key
	 */
	public function getValue( $key )
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
	 * @return \Aimeos\MShop\Common\Item\Helper\Form\Iface Item for chaining method calls
	 */
	public function setValue( $key, \Aimeos\MW\Criteria\Attribute\Iface $value )
	{
		$this->values[$key] = $value;

		return $this;
	}


	/**
	 * Returns the all key/value pairs.
	 *
	 * @return array Key/value pairs, values implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getValues()
	{
		return $this->values;
	}
}
