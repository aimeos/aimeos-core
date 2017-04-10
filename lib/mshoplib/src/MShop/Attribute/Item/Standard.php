<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Attribute
 */


namespace Aimeos\MShop\Attribute\Item;


/**
 * Default attribute item implementation.
 *
 * @package MShop
 * @subpackage Attribute
 */
class Standard
	extends \Aimeos\MShop\Common\Item\ListRef\Base
	implements \Aimeos\MShop\Attribute\Item\Iface
{
	private $values;


	/**
	 * Initializes the attribute item.
	 *
	 * @param array $values Associative array with id, domain, code, and status to initialize the item properties; Optional
	 * @param \Aimeos\MShop\Common\Lists\Item\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 */
	public function __construct( array $values = [], array $listItems = [], array $refItems = [] )
	{
		parent::__construct( 'attribute.', $values, $listItems, $refItems );

		$this->values = $values;
	}


	/**
	 * Returns the domain of the attribute item.
	 *
	 * @return string Returns the domain for this item e.g. text, media, price...
	 */
	public function getDomain()
	{
		if( isset( $this->values['attribute.domain'] ) ) {
			return (string) $this->values['attribute.domain'];
		}

		return '';
	}


	/**
	 * Set the name of the domain for this attribute item.
	 *
	 * @param string $domain Name of the domain e.g. text, media, price...
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
	 */
	public function setDomain( $domain )
	{
		if( $domain == $this->getDomain() ) { return $this; }

		$this->values['attribute.domain'] = (string) $domain;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the type id of the attribute.
	 *
	 * @return integer|null Type of the attribute
	 */
	public function getTypeId()
	{
		if( isset( $this->values['attribute.typeid'] ) ) {
			return (int) $this->values['attribute.typeid'];
		}

		return null;
	}


	/**
	 * Sets the new type of the attribute.
	 *
	 * @param integer|null $typeid Type of the attribute
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
	 */
	public function setTypeId( $typeid )
	{
		if( $typeid == $this->getTypeId() ) { return $this; }

		$this->values['attribute.typeid'] = (int) $typeid;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the type code of the attribute item.
	 *
	 * @return string|null Type code of the attribute item
	 */
	public function getType()
	{
		if( isset( $this->values['attribute.type'] ) ) {
			return (string) $this->values['attribute.type'];
		}

		return null;
	}


	/**
	 * Returns the localized name of the type
	 *
	 * @return string|null Localized name of the type
	 */
	public function getTypeName()
	{
		if( isset( $this->values['attribute.typename'] ) ) {
			return (string) $this->values['attribute.typename'];
		}

		return null;
	}


	/**
	 * Returns a unique code of the attribute item.
	 *
	 * @return string Returns the code of the attribute item
	 */
	public function getCode()
	{
		if( isset( $this->values['attribute.code'] ) ) {
			return (string) $this->values['attribute.code'];
		}

		return '';
	}


	/**
	 * Sets a unique code for the attribute item.
	 *
	 * @param string $code Code of the attribute item
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
	 */
	public function setCode( $code )
	{
		if( strlen( $code ) > 255 ) {
			throw new \Aimeos\MShop\Attribute\Exception( sprintf( 'Code must not be longer than 255 characters' ) );
		}

		if( $code == $this->getCode() ) { return $this; }

		$this->values['attribute.code'] = (string) $code;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the name of the attribute item.
	 *
	 * @return string Label of the attribute item
	 */
	public function getLabel()
	{
		if( isset( $this->values['attribute.label'] ) ) {
			return (string) $this->values['attribute.label'];
		}

		return '';
	}


	/**
	 * Sets the new label of the attribute item.
	 *
	 * @param string $label Type label of the attribute item
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
	 */
	public function setLabel( $label )
	{
		if( $label == $this->getLabel() ) { return $this; }

		$this->values['attribute.label'] = (string) $label;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the status (enabled/disabled) of the attribute item.
	 *
	 * @return integer Returns the status of the item
	 */
	public function getStatus()
	{
		if( isset( $this->values['attribute.status'] ) ) {
			return (int) $this->values['attribute.status'];
		}

		return 0;
	}


	/**
	 * Sets the new status of the attribute item.
	 *
	 * @param integer $status Status of the item
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
	 */
	public function setStatus( $status )
	{
		if( $status == $this->getStatus() ) { return $this; }

		$this->values['attribute.status'] = (int) $status;
		$this->setModified();

		return $this;
	}


	/**
	 * Gets the position of the attribute item.
	 *
	 * @return integer Position of the attribute item
	 */
	public function getPosition()
	{
		if( isset( $this->values['attribute.position'] ) ) {
			return (int) $this->values['attribute.position'];
		}

		return 0;
	}


	/**
	 * Sets the position of the attribute item
	 *
	 * @param integer $pos Position of the attribute item
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
	 */
	public function setPosition( $pos )
	{
		if( $pos == $this->getPosition() ) { return $this; }

		$this->values['attribute.position'] = (int) $pos;
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
		return 'attribute';
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
		unset( $list['attribute.type'], $list['attribute.typename'] );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'attribute.domain': $this->setDomain( $value ); break;
				case 'attribute.code': $this->setCode( $value ); break;
				case 'attribute.status': $this->setStatus( $value ); break;
				case 'attribute.typeid': $this->setTypeId( $value ); break;
				case 'attribute.position': $this->setPosition( $value ); break;
				case 'attribute.label': $this->setLabel( $value ); break;
				default: $unknown[$key] = $value;
			}
		}

		return $unknown;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param boolean True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( $private = false )
	{
		$list = parent::toArray( $private );

		$list['attribute.domain'] = $this->getDomain();
		$list['attribute.code'] = $this->getCode();
		$list['attribute.status'] = $this->getStatus();
		$list['attribute.type'] = $this->getType();
		$list['attribute.typename'] = $this->getTypeName();
		$list['attribute.position'] = $this->getPosition();
		$list['attribute.label'] = $this->getLabel();

		if( $private === true ) {
			$list['attribute.typeid'] = $this->getTypeId();
		}

		return $list;
	}

}
