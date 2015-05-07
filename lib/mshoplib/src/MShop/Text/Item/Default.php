<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Text
 */


/**
 * Default text manager implementation.
 *
 * @package MShop
 * @subpackage Text
 */
class MShop_Text_Item_Default
	extends MShop_Common_Item_ListRef_Abstract
	implements MShop_Text_Item_Interface
{
	private $_values;


	/**
	 * Initializes the text item object with the given values.
	 *
	 * @param array $values Associative list of key/value pairs
	 */
	public function __construct( array $values = array(), array $listItems = array(), array $refItems = array() )
	{
		parent::__construct( 'text.', $values, $listItems, $refItems );

		$this->_values = $values;
	}


	/**
	 * Returns the ISO language code.
	 *
	 * @return string|null ISO language code (e.g. de or de_DE)
	 */
	public function getLanguageId()
	{
		return ( isset( $this->_values['langid'] ) ? (string) $this->_values['langid'] : null );
	}


	/**
	 * Sets the ISO language code.
	 *
	 * @param string|null $langid ISO language code (e.g. de or de_DE)
	 * @throws MShop_Exception If the language ID is invalid
	 */
	public function setLanguageId( $langid )
	{
		if ( $langid === $this->getLanguageId() ) { return; }

		$this->_checkLanguageId( $langid );
		$this->_values['langid'] = $langid;
		$this->setModified();
	}


	/**
	 * Returns the type ID of the text item.
	 *
	 * @return integer|null Type ID of the text item
	 */
	public function getTypeId()
	{
		return ( isset( $this->_values['typeid'] ) ? (int) $this->_values['typeid'] : null );
	}


	/**
	 *  Sets the type ID of the text item.
	 *
	 * @param integer $typeid Type ID of the text type
	 */
	public function setTypeId( $typeid )
	{
		if ( $typeid == $this->getTypeId() ) { return; }

		$this->_values['typeid'] = (int) $typeid;
		$this->setModified();
	}


	/**
	 * Returns the type of the text item.
	 *
	 * @return string|null Type of the text item
	 */
	public function getType()
	{
		return ( isset( $this->_values['type'] ) ? (string) $this->_values['type'] : null );
	}


	/**
	 * Returns the domain of the text item.
	 *
	 * @return string Domain of the text item
	 */
	public function getDomain()
	{
		return ( isset( $this->_values['domain'] ) ? (string) $this->_values['domain'] : '' );
	}


	/**
	 * Sets the domain of the text item.
	 *
	 * @param string $domain Domain of the text item
	 */
	public function setDomain( $domain )
	{
		if ( $domain == $this->getDomain() ) { return; }

		$this->_values['domain'] = (string) $domain;
		$this->setModified();
	}


	/**
	 * Returns the content of the text item.
	 *
	 * @return string Content of the text item
	 */
	public function getContent()
	{
		return ( isset( $this->_values['content'] ) ? (string) $this->_values['content'] : '' );
	}


	/**
	 * Sets the content of the text item.
	 *
	 * @param string $text Content of the text item
	 */
	public function setContent( $text )
	{
		if ( $text == $this->getContent() ) { return; }

		@ini_set( 'mbstring.substitute_character', 'none' );
		$this->_values['content'] = @mb_convert_encoding( (string) $text, 'UTF-8', 'UTF-8' );
		$this->setModified();
	}


	/**
	 * Returns the name of the attribute item.
	 *
	 * @return string Label of the attribute item
	 */
	public function getLabel()
	{
		return ( isset( $this->_values['label'] ) ? (string) $this->_values['label'] : '' );
	}


	/**
	 * Sets the new label of the attribute item.
	 *
	 * @param string $label Type label of the attribute item
	 */
	public function setLabel( $label )
	{
		if ( $label == $this->getLabel() ) { return; }

		$this->_values['label'] = (string) $label;
		$this->setModified();
	}


	/**
	 * Returns the status of the text item.
	 *
	 * @return integer Status of the text item
	 */
	public function getStatus()
	{
		return ( isset( $this->_values['status'] ) ? (int) $this->_values['status'] : 0 );
	}


	/**
	 * Sets the status of the text item.
	 *
	 * @param integer $status true/false for enabled/disabled
	 */
	public function setStatus( $status )
	{
		if ( $status == $this->getStatus() ) { return; }

		$this->_values['status'] = (int) $status;
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

		$list['text.languageid'] =  $this->getLanguageId();
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