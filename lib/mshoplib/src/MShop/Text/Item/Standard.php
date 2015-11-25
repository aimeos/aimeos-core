<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
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
	extends \Aimeos\MShop\Common\Item\ListRef\Base
	implements \Aimeos\MShop\Text\Item\Iface
{
	private $values;


	/**
	 * Initializes the text item object with the given values.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @param \Aimeos\MShop\Common\Lists\Item\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 */
	public function __construct( array $values = array(), array $listItems = array(), array $refItems = array() )
	{
		parent::__construct( 'text.', $values, $listItems, $refItems );

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

		return null;
	}


	/**
	 * Sets the ISO language code.
	 *
	 * @param string|null $langid ISO language code (e.g. de or de_DE)
	 * @throws \Aimeos\MShop\Exception If the language ID is invalid
	 */
	public function setLanguageId( $langid )
	{
		if( $langid === $this->getLanguageId() ) { return; }

		$this->checkLanguageId( $langid );
		$this->values['text.languageid'] = $langid;
		$this->setModified();
	}


	/**
	 * Returns the type ID of the text item.
	 *
	 * @return integer|null Type ID of the text item
	 */
	public function getTypeId()
	{
		if( isset( $this->values['text.typeid'] ) ) {
			return (int) $this->values['text.typeid'];
		}

		return null;
	}


	/**
	 *  Sets the type ID of the text item.
	 *
	 * @param integer $typeid Type ID of the text type
	 */
	public function setTypeId( $typeid )
	{
		if( $typeid == $this->getTypeId() ) { return; }

		$this->values['text.typeid'] = (int) $typeid;
		$this->setModified();
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

		return null;
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
	 */
	public function setDomain( $domain )
	{
		if( $domain == $this->getDomain() ) { return; }

		$this->values['text.domain'] = (string) $domain;
		$this->setModified();
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
	 */
	public function setContent( $text )
	{
		if( $text == $this->getContent() ) { return; }

		ini_set( 'mbstring.substitute_character', 'none' );
		$this->values['text.content'] = @mb_convert_encoding( (string) $text, 'UTF-8', 'UTF-8' );
		$this->setModified();
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
	 */
	public function setLabel( $label )
	{
		if( $label == $this->getLabel() ) { return; }

		$this->values['text.label'] = (string) $label;
		$this->setModified();
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

		return 0;
	}


	/**
	 * Sets the status of the text item.
	 *
	 * @param integer $status true/false for enabled/disabled
	 */
	public function setStatus( $status )
	{
		if( $status == $this->getStatus() ) { return; }

		$this->values['text.status'] = (int) $status;
		$this->setModified();
	}


	/**
	 * Returns the item type
	 *
	 * @return Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'text';
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
				case 'text.languageid': $this->setLanguageId( $value ); break;
				case 'text.typeid': $this->setTypeId( $value ); break;
				case 'text.label': $this->setLabel( $value ); break;
				case 'text.domain': $this->setDomain( $value ); break;
				case 'text.content': $this->setContent( $value ); break;
				case 'text.status': $this->setStatus( $value ); break;
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

		$list['text.languageid'] = $this->getLanguageId();
		$list['text.typeid'] = $this->getTypeId();
		$list['text.label'] = $this->getLabel();
		$list['text.type'] = $this->getType();
		$list['text.domain'] = $this->getDomain();
		$list['text.content'] = $this->getContent();
		$list['text.status'] = $this->getStatus();

		return $list;
	}
}
;