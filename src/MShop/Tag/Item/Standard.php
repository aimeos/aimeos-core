<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
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
	use \Aimeos\MShop\Common\Item\TypeRef\Traits;


	/**
	 * Returns the domain of the tag item.
	 *
	 * @return string Domain of the tag item
	 */
	public function getDomain() : string
	{
		return (string) $this->get( 'tag.domain', '' );
	}


	/**
	 * Sets the domain of the tag item.
	 *
	 * @param string $domain Domain of the tag item
	 * @return static Tag item for chaining method calls
	 */
	public function setDomain( string $domain ) : static
	{
		return $this->set( 'tag.domain', $this->checkDomain( $domain ) );
	}


	/**
	 * Returns the language ID of the product tag item.
	 *
	 * @return string|null Language ID of the product tag item
	 */
	public function getLanguageId(): ?string
	{
		// @phpstan-ignore return.type
		return $this->get( 'tag.languageid' );
	}


	/**
	 *  Sets the language ID of the product tag item.
	 *
	 * @param string|null $id Language ID of the product tag item
	 * @return static Tag item for chaining method calls
	 */
	public function setLanguageId( ?string $id ) : static
	{
		return $this->set( 'tag.languageid', \Aimeos\Utils::language( $id ) );
	}


	/**
	 * Returns the label of the product tag item.
	 *
	 * @return string Label of the product tag item
	 */
	public function getLabel() : string
	{
		return (string) $this->get( 'tag.label', '' );
	}


	/**
	 * Sets the Label of the product tag item.
	 *
	 * @param string $label Label of the product tag item
	 * @return static Tag item for chaining method calls
	 */
	public function setLabel( string $label ) : static
	{
		return $this->set( 'tag.label', $label );
	}


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return bool True if available, false if not
	 */
	public function isAvailable() : bool
	{
		return parent::isAvailable() && $this->getLanguageId() === $this->get( '.languageid' );
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @type array &$list Associative list of item keys and their values
	 * @param bool $private True to set private properties too, false for public only
	 * @return static Tag item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : static
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'tag.languageid': $item->setLanguageId( $value ? (string) $value : null ); break;
				case 'tag.domain': $item->setDomain( (string) $value ); break;
				case 'tag.label': $item->setLabel( (string) $value ); break;
				case 'tag.type': $item->setType( (string) $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param bool $private True to return private properties, false for public only
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
