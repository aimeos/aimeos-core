<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
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
	public function __construct( $prefix, array $values = array() )
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
		return ( isset( $this->values['code'] ) ? (string) $this->values['code'] : '' );
	}


	/**
	 * Sets the code of the common list type item
	 *
	 * @param string $code New code of the common list type item
	 */
	public function setCode( $code )
	{
		$this->checkCode( $code );

		if( $code == $this->getCode() ) { return; }

		$this->values['code'] = (string) $code;
		$this->setModified();
	}


	/**
	 * Returns the domain of the common list type item
	 *
	 * @return string Domain of the common list type item
	 */
	public function getDomain()
	{
		return ( isset( $this->values['domain'] ) ? (string) $this->values['domain'] : '' );
	}


	/**
	 * Sets the domain of the common list type item
	 *
	 * @param string $domain New domain of the common list type item
	 */
	public function setDomain( $domain )
	{
		if( $domain == $this->getDomain() ) { return; }

		$this->values['domain'] = (string) $domain;
		$this->setModified();
	}


	/**
	 * Returns the label of the common list type item
	 *
	 * @return string Label of the common list type item
	 */
	public function getLabel()
	{
		return ( isset( $this->values['label'] ) ? (string) $this->values['label'] : '' );
	}


	/**
	 * Sets the label of the common list type item
	 *
	 * @param string $label New label of the common list type item
	 */
	public function setLabel( $label )
	{
		if( $label == $this->getLabel() ) { return; }

		$this->values['label'] = (string) $label;
		$this->setModified();
	}


	/**
	 * Returns the status of the common list type item
	 *
	 * @return integer Status of the common list type item
	 */
	public function getStatus()
	{
		return ( isset( $this->values['status'] ) ? (int) $this->values['status'] : 0 );
	}


	/**
	 * Sets the status of the common list type item
	 *
	 * @param integer $status New status of the common list type item
	 */
	public function setStatus( $status )
	{
		if( $status == $this->getStatus() ) { return; }

		$this->values['status'] = (int) $status;
		$this->setModified();
	}


	/**
	 * Sets the item values from the given array.
	 *
	 * @param array $list Associative list of item keys and their values
	 * @return array Associative list of keys and their values that are unknown
	 */
	public function fromArray( array $list )
	{
		$unknown = array();
		$list = parent::fromArray( $list );

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
	 * @return array List of item properties.
	 */
	public function toArray()
	{
		$list = parent::toArray();

		$list[$this->prefix . 'code'] = $this->getCode();
		$list[$this->prefix . 'domain'] = $this->getDomain();
		$list[$this->prefix . 'label'] = $this->getLabel();
		$list[$this->prefix . 'status'] = $this->getStatus();

		return $list;
	}
}
