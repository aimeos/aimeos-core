<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Text
 */


namespace Aimeos\MShop\Text\Item;


/**
 * Default text manager implementation.
 *
 * @package MShop
 * @subpackage Text
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Text\Item\Iface
{
	use \Aimeos\MShop\Common\Item\ListRef\Traits;


	/**
	 * Initializes the text item object with the given values.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 */
	public function __construct( array $values = [], array $listItems = [], array $refItems = [] )
	{
		parent::__construct( 'text.', $values );

		$this->initListItems( $listItems, $refItems );
	}


	/**
	 * Returns the ISO language code.
	 *
	 * @return string|null ISO language code (e.g. de or de_DE)
	 */
	public function getLanguageId()
	{
		return $this->get( 'text.languageid' );
	}


	/**
	 * Sets the ISO language code.
	 *
	 * @param string|null $id ISO language code (e.g. de or de_DE)
	 * @return \Aimeos\MShop\Text\Item\Iface Text item for chaining method calls
	 * @throws \Aimeos\MShop\Exception If the language ID is invalid
	 */
	public function setLanguageId( $id )
	{
		return $this->set( 'text.languageid', $this->checkLanguageId( $id ) );
	}


	/**
	 * Returns the type of the text item.
	 *
	 * @return string|null Type of the text item
	 */
	public function getType()
	{
		return $this->get( 'text.type' );
	}


	/**
	 *  Sets the type of the text item.
	 *
	 * @param string $type Type of the text type
	 * @return \Aimeos\MShop\Text\Item\Iface Text item for chaining method calls
	 */
	public function setType( $type )
	{
		return $this->set( 'text.type', $this->checkCode( $type ) );
	}


	/**
	 * Returns the domain of the text item.
	 *
	 * @return string Domain of the text item
	 */
	public function getDomain()
	{
		return (string) $this->get( 'text.domain', '' );
	}


	/**
	 * Sets the domain of the text item.
	 *
	 * @param string $domain Domain of the text item
	 * @return \Aimeos\MShop\Text\Item\Iface Text item for chaining method calls
	 */
	public function setDomain( $domain )
	{
		return $this->set( 'text.domain', (string) $domain );
	}


	/**
	 * Returns the content of the text item.
	 *
	 * @return string Content of the text item
	 */
	public function getContent()
	{
		return (string) $this->get( 'text.content', '' );
	}


	/**
	 * Sets the content of the text item.
	 *
	 * @param string $text Content of the text item
	 * @return \Aimeos\MShop\Text\Item\Iface Text item for chaining method calls
	 */
	public function setContent( $text )
	{
		ini_set( 'mbstring.substitute_character', 'none' );
		return $this->set( 'text.content', @mb_convert_encoding( (string) $text, 'UTF-8', 'UTF-8' ) );
	}


	/**
	 * Returns the name of the attribute item.
	 *
	 * @return string Label of the attribute item
	 */
	public function getLabel()
	{
		return (string) $this->get( 'text.label', '' );
	}


	/**
	 * Sets the new label of the attribute item.
	 *
	 * @param string $label Type label of the attribute item
	 * @return \Aimeos\MShop\Text\Item\Iface Text item for chaining method calls
	 */
	public function setLabel( $label )
	{
		return $this->set( 'text.label', (string) $label );
	}


	/**
	 * Returns the status of the text item.
	 *
	 * @return integer Status of the text item
	 */
	public function getStatus()
	{
		return (int) $this->get( 'text.status', 1 );
	}


	/**
	 * Sets the status of the text item.
	 *
	 * @param integer $status true/false for enabled/disabled
	 * @return \Aimeos\MShop\Text\Item\Iface Text item for chaining method calls
	 */
	public function setStatus( $status )
	{
		return $this->set( 'text.status', (int) $status );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'text';
	}


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return boolean True if available, false if not
	 */
	public function isAvailable()
	{
		return parent::isAvailable() && $this->getStatus() > 0
			&& ( $this->values['.languageid'] === null || $this->getLanguageId() === null
			|| $this->getLanguageId() === $this->values['.languageid'] );
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param boolean True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Text\Item\Iface Text item for chaining method calls
	 */
	public function fromArray( array &$list, $private = false )
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'text.languageid': $item = $item->setLanguageId( $value ); break;
				case 'text.type': $item = $item->setType( $value ); break;
				case 'text.label': $item = $item->setLabel( $value ); break;
				case 'text.domain': $item = $item->setDomain( $value ); break;
				case 'text.content': $item = $item->setContent( $value ); break;
				case 'text.status': $item = $item->setStatus( $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
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

		$list['text.languageid'] = $this->getLanguageId();
		$list['text.type'] = $this->getType();
		$list['text.label'] = $this->getLabel();
		$list['text.domain'] = $this->getDomain();
		$list['text.content'] = $this->getContent();
		$list['text.status'] = $this->getStatus();

		return $list;
	}
}
