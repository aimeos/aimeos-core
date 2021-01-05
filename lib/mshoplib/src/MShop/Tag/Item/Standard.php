<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	private $langid;


	/**
	 * Initializes the tag item object with the given values
	 *
	 * @param array $values Associative list of item key/value pairs
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'tag.', $values );

		$this->langid = ( isset( $values['.languageid'] ) ? $values['.languageid'] : null );
	}


	/**
	 * Returns the domain of the tag item.
	 *
	 * @return string Domain of the tag item
	 */
	public function getDomain() : string
	{
		return $this->get( 'tag.domain', '' );
	}


	/**
	 * Sets the domain of the tag item.
	 *
	 * @param string $domain Domain of the tag item
	 * @return \Aimeos\MShop\Tag\Item\Iface Tag item for chaining method calls
	 */
	public function setDomain( string $domain ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'tag.domain', $domain );
	}


	/**
	 * Returns the language ID of the product tag item.
	 *
	 * @return string|null Language ID of the product tag item
	 */
	public function getLanguageId(): ?string
	{
		return $this->get( 'tag.languageid' );
	}


	/**
	 *  Sets the language ID of the product tag item.
	 *
	 * @param string|null $id Language ID of the product tag item
	 * @return \Aimeos\MShop\Tag\Item\Iface Tag item for chaining method calls
	 */
	public function setLanguageId( ?string $id ) : \Aimeos\MShop\Tag\Item\Iface
	{
		return $this->set( 'tag.languageid', $this->checkLanguageId( $id ) );
	}


	/**
	 * Returns the type code of the product tag item.
	 *
	 * @return string|null Type code of the product tag item
	 */
	public function getType() : ?string
	{
		return $this->get( 'tag.type' );
	}


	/**
	 * Sets the new type of the product tag item
	 *
	 * @param string $type Type of the product tag item
	 * @return \Aimeos\MShop\Tag\Item\Iface Tag item for chaining method calls
	 */
	public function setType( string $type ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'tag.type', $this->checkCode( $type ) );
	}


	/**
	 * Returns the label of the product tag item.
	 *
	 * @return string Label of the product tag item
	 */
	public function getLabel() : string
	{
		return $this->get( 'tag.label', '' );
	}


	/**
	 * Sets the Label of the product tag item.
	 *
	 * @param string $label Label of the product tag item
	 * @return \Aimeos\MShop\Tag\Item\Iface Tag item for chaining method calls
	 */
	public function setLabel( string $label ) : \Aimeos\MShop\Tag\Item\Iface
	{
		return $this->set( 'tag.label', $label );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string
	{
		return 'tag';
	}


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return bool True if available, false if not
	 */
	public function isAvailable() : bool
	{
		return parent::isAvailable() && $this->getLanguageId() === $this->langid;
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Tag\Item\Iface Tag item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'tag.languageid': $item = $item->setLanguageId( $value ); break;
				case 'tag.domain': $item = $item->setDomain( $value ); break;
				case 'tag.label': $item = $item->setLabel( $value ); break;
				case 'tag.type': $item = $item->setType( $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param bool True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( bool $private = false ) : array
	{
		$list = parent::toArray( $private );

		$list['tag.languageid'] = $this->getLanguageId();
		$list['tag.domain'] = $this->getDomain();
		$list['tag.label'] = $this->getLabel();
		$list['tag.type'] = $this->getType();

		return $list;
	}

}
