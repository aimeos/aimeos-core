<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Tag
 */


namespace Aimeos\MShop\Tag\Item;


/**
 * Default tag item implementation.
 *
 * @package MShop
 * @subpackage Tag
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Tag\Item\Iface
{
	private $values;


	/**
	 * Initializes the tag item object with the given values
	 *
	 * @param array $values Associative list of item key/value pairs
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'tag.', $values );

		$this->values = $values;
	}


	/**
	 * Returns the domain of the tag item.
	 *
	 * @return string Domain of the tag item
	 */
	public function getDomain()
	{
		if( isset( $this->values['tag.domain'] ) ) {
			return (string) $this->values['tag.domain'];
		}

		return '';
	}


	/**
	 * Sets the domain of the tag item.
	 *
	 * @param string $domain Domain of the tag item
	 * @return \Aimeos\MShop\Tag\Item\Iface Tag item for chaining method calls
	 */
	public function setDomain( $domain )
	{
		if( $domain == $this->getDomain() ) { return $this; }

		$this->values['tag.domain'] = (string) $domain;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the language ID of the product tag item.
	 *
	 * @return string|null Language ID of the product tag item
	 */
	public function getLanguageId()
	{
		if( isset( $this->values['tag.languageid'] ) ) {
			return (string) $this->values['tag.languageid'];
		}

		return null;
	}


	/**
	 *  Sets the language ID of the product tag item.
	 *
	 * @param string|null $id Language ID of the product tag item
	 * @return \Aimeos\MShop\Tag\Item\Iface Tag item for chaining method calls
	 */
	public function setLanguageId( $id )
	{
		if( $id == $this->getLanguageId() ) { return $this; }

		$this->values['tag.languageid'] = $this->checkLanguageId( $id );
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the type code of the product tag item.
	 *
	 * @return string|null Type code of the product tag item
	 */
	public function getType()
	{
		if( isset( $this->values['tag.type'] ) ) {
			return (string) $this->values['tag.type'];
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
		if( isset( $this->values['tag.typename'] ) ) {
			return (string) $this->values['tag.typename'];
		}

		return null;
	}


	/**
	 * Returns the type id of the product tag item
	 *
	 * @return integer|null Type of the product tag item
	 */
	public function getTypeId()
	{
		if( isset( $this->values['tag.typeid'] ) ) {
			return (int) $this->values['tag.typeid'];
		}

		return null;
	}


	/**
	 * Sets the new type of the product tag item
	 *
	 * @param integer|null $id Type of the product tag item
	 * @return \Aimeos\MShop\Tag\Item\Iface Tag item for chaining method calls
	 */
	public function setTypeId( $id )
	{
		if( $id == $this->getTypeId() ) { return $this; }

		$this->values['tag.typeid'] = (int) $id;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the label of the product tag item.
	 *
	 * @return string Label of the product tag item
	 */
	public function getLabel()
	{
		if( isset( $this->values['tag.label'] ) ) {
			return (string) $this->values['tag.label'];
		}

		return '';
	}


	/**
	 * Sets the Label of the product tag item.
	 *
	 * @param string $label Label of the product tag item
	 * @return \Aimeos\MShop\Tag\Item\Iface Tag item for chaining method calls
	 */
	public function setLabel( $label )
	{
		if( $label == $this->getLabel() ) { return $this; }

		$this->values['tag.label'] = (string) $label;
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
		return 'tag';
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
		unset( $list['tag.type'], $list['tag.typename'] );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'tag.domain': $this->setDomain( $value ); break;
				case 'tag.typeid': $this->setTypeId( $value ); break;
				case 'tag.languageid': $this->setLanguageId( $value ); break;
				case 'tag.label': $this->setLabel( $value ); break;
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

		$list['tag.domain'] = $this->getDomain();
		$list['tag.languageid'] = $this->getLanguageId();
		$list['tag.label'] = $this->getLabel();
		$list['tag.type'] = $this->getType();
		$list['tag.typename'] = $this->getTypeName();

		if( $private === true ) {
			$list['tag.typeid'] = $this->getTypeId();
		}

		return $list;
	}

}
