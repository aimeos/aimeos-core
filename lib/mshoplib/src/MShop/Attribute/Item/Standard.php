<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Attribute
 */


/**
 * Default attribute item implementation.
 *
 * @package MShop
 * @subpackage Attribute
 */
class MShop_Attribute_Item_Standard
	extends MShop_Common_Item_ListRef_Base
	implements MShop_Attribute_Item_Iface
{
	private $values;


	/**
	 * Initializes the attribute item.
	 *
	 * @param array $values Associative array with id, domain, code, and status to initialize the item properties; Optional
	 * @param MShop_Common_Lists_Item_Iface[] $listItems List of list items
	 * @param MShop_Common_Item_Iface[] $refItems List of referenced items
	 */
	public function __construct( array $values = array(), array $listItems = array(), array $refItems = array() )
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
		return ( isset( $this->values['domain'] ) ? (string) $this->values['domain'] : '' );
	}


	/**
	 * Set the name of the domain for this attribute item.
	 *
	 * @param string $domain Name of the domain e.g. text, media, price...
	 */
	public function setDomain( $domain )
	{
		if( $domain == $this->getDomain() ) { return; }

		$this->values['domain'] = (string) $domain;
		$this->setModified();
	}


	/**
	 * Returns the type id of the attribute.
	 *
	 * @return integer|null Type of the attribute
	 */
	public function getTypeId()
	{
		return ( isset( $this->values['typeid'] ) ? (int) $this->values['typeid'] : null );
	}


	/**
	 * Sets the new type of the attribute.
	 *
	 * @param integer|null $typeid Type of the attribute
	 */
	public function setTypeId( $typeid )
	{
		if( $typeid == $this->getTypeId() ) { return; }

		$this->values['typeid'] = (int) $typeid;
		$this->setModified();
	}


	/**
	 * Returns the type code of the attribute item.
	 *
	 * @return string|null Type code of the attribute item
	 */
	public function getType()
	{
		return ( isset( $this->values['type'] ) ? (string) $this->values['type'] : null );
	}


	/**
	 * Returns a unique code of the attribute item.
	 *
	 * @return string Returns the code of the attribute item
	 */
	public function getCode()
	{
		return ( isset( $this->values['code'] ) ? (string) $this->values['code'] : '' );
	}


	/**
	 * Sets a unique code for the attribute item.
	 *
	 * @param string $code Code of the attribute item
	 */
	public function setCode( $code )
	{
		$this->checkCode( $code );

		if( $code == $this->getCode() ) { return; }

		$this->values['code'] = (string) $code;
		$this->setModified();
	}


	/**
	 * Returns the status (enabled/disabled) of the attribute item.
	 *
	 * @return integer Returns the status of the item
	 */
	public function getStatus()
	{
		return ( isset( $this->values['status'] ) ? (int) $this->values['status'] : 0 );
	}


	/**
	 * Sets the new status of the attribute item.
	 *
	 * @param integer $status Status of the item
	 */
	public function setStatus( $status )
	{
		if( $status == $this->getStatus() ) { return; }

		$this->values['status'] = (int) $status;
		$this->setModified();
	}


	/**
	 * Gets the position of the attribute item.
	 *
	 * @return integer Position of the attribute item
	 */
	public function getPosition()
	{
		return ( isset( $this->values['pos'] ) ? (int) $this->values['pos'] : 0 );
	}


	/**
	 * Sets the position of the attribute item
	 *
	 * @param integer $pos Position of the attribute item
	 */
	public function setPosition( $pos )
	{
		if( $pos == $this->getPosition() ) { return; }

		$this->values['pos'] = (int) $pos;
		$this->setModified();
	}


	/**
	 * Returns the name of the attribute item.
	 *
	 * @return string Label of the attribute item
	 */
	public function getLabel()
	{
		return ( isset( $this->values['label'] ) ? (string) $this->values['label'] : '' );
	}


	/**
	 * Sets the new label of the attribute item.
	 *
	 * @param string $label Type label of the attribute item
	 */
	public function setLabel( $label )
	{
		if( $label == $this->getLabel() ) { return; }

		$this->values['label'] = (string) $label;
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
	 * @return Associative list of item properties and their values
	 */
	public function toArray()
	{
		$list = parent::toArray();

		$list['attribute.domain'] = $this->getDomain();
		$list['attribute.code'] = $this->getCode();
		$list['attribute.status'] = $this->getStatus();
		$list['attribute.typeid'] = $this->getTypeId();
		$list['attribute.type'] = $this->getType();
		$list['attribute.position'] = $this->getPosition();
		$list['attribute.label'] = $this->getLabel();

		return $list;
	}

}
