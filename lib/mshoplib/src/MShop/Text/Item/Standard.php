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


	private $values;


	/**
	 * Initializes the text item object with the given values.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @param \Aimeos\MShop\Common\Lists\Item\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 */
	public function __construct( array $values = [], array $listItems = [], array $refItems = [] )
	{
		parent::__construct( 'text.', $values );

		$this->initListItems( $listItems, $refItems );
		$this->values = $values;
	}


	/**
	 * Returns the ISO language code.
	 *
	 * @return string|null ISO language code (e.g. de or de_DE)
	 */
	public function getLanguageId()
	{
		if( isset( $this->values['text.languageid'] ) ) {
			return (string) $this->values['text.languageid'];
		}
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
		if( $id !== $this->getLanguageId() )
		{
			$this->values['text.languageid'] = $this->checkLanguageId( $id );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the type of the text item.
	 *
	 * @return string|null Type of the text item
	 */
	public function getType()
	{
		if( isset( $this->values['text.type'] ) ) {
			return (string) $this->values['text.type'];
		}
	}


	/**
	 *  Sets the type of the text item.
	 *
	 * @param string $type Type of the text type
	 * @return \Aimeos\MShop\Text\Item\Iface Text item for chaining method calls
	 */
	public function setType( $type )
	{
		if( (string) $type !== $this->getType() )
		{
			$this->values['text.type'] = (string) $type;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the domain of the text item.
	 *
	 * @return string Domain of the text item
	 */
	public function getDomain()
	{
		if( isset( $this->values['text.domain'] ) ) {
			return (string) $this->values['text.domain'];
		}

		return '';
	}


	/**
	 * Sets the domain of the text item.
	 *
	 * @param string $domain Domain of the text item
	 * @return \Aimeos\MShop\Text\Item\Iface Text item for chaining method calls
	 */
	public function setDomain( $domain )
	{
		if( (string) $domain !== $this->getDomain() )
		{
			$this->values['text.domain'] = (string) $domain;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the content of the text item.
	 *
	 * @return string Content of the text item
	 */
	public function getContent()
	{
		if( isset( $this->values['text.content'] ) ) {
			return (string) $this->values['text.content'];
		}

		return '';
	}


	/**
	 * Sets the content of the text item.
	 *
	 * @param string $text Content of the text item
	 * @return \Aimeos\MShop\Text\Item\Iface Text item for chaining method calls
	 */
	public function setContent( $text )
	{
		if( (string) $text !== $this->getContent() )
		{
			ini_set( 'mbstring.substitute_character', 'none' );
			$this->values['text.content'] = @mb_convert_encoding( (string) $text, 'UTF-8', 'UTF-8' );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the name of the attribute item.
	 *
	 * @return string Label of the attribute item
	 */
	public function getLabel()
	{
		if( isset( $this->values['text.label'] ) ) {
			return (string) $this->values['text.label'];
		}

		return '';
	}


	/**
	 * Sets the new label of the attribute item.
	 *
	 * @param string $label Type label of the attribute item
	 * @return \Aimeos\MShop\Text\Item\Iface Text item for chaining method calls
	 */
	public function setLabel( $label )
	{
		if( (string) $label !== $this->getLabel() )
		{
			$this->values['text.label'] = (string) $label;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the status of the text item.
	 *
	 * @return integer Status of the text item
	 */
	public function getStatus()
	{
		if( isset( $this->values['text.status'] ) ) {
			return (int) $this->values['text.status'];
		}

		return 1;
	}


	/**
	 * Sets the status of the text item.
	 *
	 * @param integer $status true/false for enabled/disabled
	 * @return \Aimeos\MShop\Text\Item\Iface Text item for chaining method calls
	 */
	public function setStatus( $status )
	{
		if( (int) $status !== $this->getStatus() )
		{
			$this->values['text.status'] = (int) $status;
			$this->setModified();
		}

		return $this;
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
			&& ( $this->values['languageid'] === null
			|| $this->getLanguageId() === null
			|| $this->getLanguageId() === $this->values['languageid'] );
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
