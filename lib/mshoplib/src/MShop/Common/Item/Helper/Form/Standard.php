<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Common
 */


/**
 * Default implementation of the helper form item.
 *
 * @package MShop
 * @subpackage Common
 */
class MShop_Common_Item_Helper_Form_Standard implements MShop_Common_Item_Helper_Form_Iface
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
	 * @param array $values Form parameters implementing MW_Common_Criteria_Attribute_Iface
	 * @param boolean $external True if URL points to an external site, false if it stays on the same site
	 */
	public function __construct( $url = '', $method = '', array $values = array(), $external = true )
	{
		MW_Common_Base::checkClassList( 'MW_Common_Criteria_Attribute_Iface', $values );

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
	 */
	public function setExternal( $value )
	{
		$this->external = (bool) $value;
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
	 */
	public function setUrl( $url )
	{
		$this->url = (string) $url;
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
	 */
	public function setMethod( $method )
	{
		$this->method = (string) $method;
	}


	/**
	 * Returns the value for the given key.
	 *
	 * @param string $key Unique key
	 * @return MW_Common_Criteria_Attribute_Iface Attribute item for the given key
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
	 * @param MW_Common_Criteria_Attribute_Iface $value Attribute item for the given key
	 */
	public function setValue( $key, MW_Common_Criteria_Attribute_Iface $value )
	{
		$this->values[$key] = $value;
	}


	/**
	 * Returns the all key/value pairs.
	 *
	 * @return array Key/value pairs, values implementing MW_Common_Criteria_Attribute_Iface
	 */
	public function getValues()
	{
		return $this->values;
	}
}
