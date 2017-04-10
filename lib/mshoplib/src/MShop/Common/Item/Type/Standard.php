<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\Type;


/**
 * Default implementation of the list item.
 *
 * @package MShop
 * @subpackage Common
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Common\Item\Type\Iface
{
	private $prefix;
	private $values;


	/**
	 * Initializes the type item object.
	 *
	 * @param string $prefix Property prefix when converting to array
	 * @param array $values Initial values of the list type item
	 */
	public function __construct( $prefix, array $values = [] )
	{
		parent::__construct( $prefix, $values );

		$this->prefix = $prefix;
		$this->values = $values;
	}


	/**
	 * Returns the code of the common list type item
	 *
	 * @return string Code of the common list type item
	 */
	public function getCode()
	{
		if( isset( $this->values[$this->prefix . 'code'] ) ) {
			return (string) $this->values[$this->prefix . 'code'];
		}

		return '';
	}


	/**
	 * Sets the code of the common list type item
	 *
	 * @param string $code New code of the common list type item
	 * @return \Aimeos\MShop\Common\Item\Type\Iface Common type item for chaining method calls
	 */
	public function setCode( $code )
	{
		if( $code === $this->getCode() ) { return $this; }

		$this->values[$this->prefix . 'code'] = (string) $this->checkCode( $code );
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the domain of the common list type item
	 *
	 * @return string Domain of the common list type item
	 */
	public function getDomain()
	{
		if( isset( $this->values[$this->prefix . 'domain'] ) ) {
			return (string) $this->values[$this->prefix . 'domain'];
		}

		return '';
	}


	/**
	 * Sets the domain of the common list type item
	 *
	 * @param string $domain New domain of the common list type item
	 * @return \Aimeos\MShop\Common\Item\Type\Iface Common type item for chaining method calls
	 */
	public function setDomain( $domain )
	{
		if( $domain == $this->getDomain() ) { return $this; }

		$this->values[$this->prefix . 'domain'] = (string) $domain;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the translated name for the type item
	 *
	 * @return string Translated name of the type item
	 */
	public function getName()
	{
		if( isset( $this->values[$this->prefix . 'name'] ) ) {
			return (string) $this->values[$this->prefix . 'name'];
		}

		return $this->getLabel();
	}


	/**
	 * Returns the label of the common list type item
	 *
	 * @return string Label of the common list type item
	 */
	public function getLabel()
	{
		if( isset( $this->values[$this->prefix . 'label'] ) ) {
			return (string) $this->values[$this->prefix . 'label'];
		}

		return '';
	}


	/**
	 * Sets the label of the common list type item
	 *
	 * @param string $label New label of the common list type item
	 * @return \Aimeos\MShop\Common\Item\Type\Iface Common type item for chaining method calls
	 */
	public function setLabel( $label )
	{
		if( $label == $this->getLabel() ) { return $this; }

		$this->values[$this->prefix . 'label'] = (string) $label;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the status of the common list type item
	 *
	 * @return integer Status of the common list type item
	 */
	public function getStatus()
	{
		if( isset( $this->values[$this->prefix . 'status'] ) ) {
			return (int) $this->values[$this->prefix . 'status'];
		}

		return 0;
	}


	/**
	 * Sets the status of the common list type item
	 *
	 * @param integer $status New status of the common list type item
	 * @return \Aimeos\MShop\Common\Item\Type\Iface Common type item for chaining method calls
	 */
	public function setStatus( $status )
	{
		if( $status == $this->getStatus() ) { return $this; }

		$this->values[$this->prefix . 'status'] = (int) $status;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return str_replace( '.', '/', rtrim( $this->prefix, '.' ) );
	}


	/**
	 * Sets the item values from the given array.
	 *
	 * @param array $list Associative list of item keys and their values
	 * @return array Associative list of keys and their values that are unknown
	 */
	public function fromArray( array $list )
	{
		$unknown = [];
		$list = parent::fromArray( $list );
		unset( $list[$this->prefix . 'name'] );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case $this->prefix . 'code': $this->setCode( $value ); break;
				case $this->prefix . 'domain': $this->setDomain( $value ); break;
				case $this->prefix . 'label': $this->setLabel( $value ); break;
				case $this->prefix . 'status': $this->setStatus( $value ); break;
				default: $unknown[$key] = $value;
			}
		}

		return $unknown;
	}


	/**
	 * Returns an associative list of item properties.
	 *
	 * @param boolean True to return private properties, false for public only
	 * @return array List of item properties.
	 */
	public function toArray( $private = false )
	{
		$list = parent::toArray( $private );

		$list[$this->prefix . 'code'] = $this->getCode();
		$list[$this->prefix . 'domain'] = $this->getDomain();
		$list[$this->prefix . 'name'] = $this->getName();
		$list[$this->prefix . 'label'] = $this->getLabel();
		$list[$this->prefix . 'status'] = $this->getStatus();

		return $list;
	}
}
