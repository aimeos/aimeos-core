<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Media
 */


/**
 * Default implementation of the media item.
 *
 * @package MShop
 * @subpackage Media
 */
class MShop_Media_Item_Default
	extends MShop_Common_Item_ListRef_Abstract
	implements MShop_Media_Item_Interface
{
	private $_values;


	/**
	 * Initializes the media item object.
	 *
	 * @param array $values Initial values of the media item
	 */
	public function __construct( array $values = array(), array $listItems = array(), array $refItems = array()  )
	{
		parent::__construct( 'media.', $values, $listItems, $refItems );

		$this->_values = $values;
	}


	/**
	 * Returns the language ID of the text item.
	 *
	 * @return string|null Language ID of the text item
	 */
	public function getLanguageId()
	{
		return ( isset( $this->_values['langid'] ) ? (string) $this->_values['langid'] : null );
	}


	/**
	 *  Sets the language ID of the text item.
	 *
	 * @param string|null $langid Language ID of the text type
	 */
	public function setLanguageId( $langid )
	{
		if ( $langid === $this->getLanguageId() ) { return; }

		$this->_values['langid'] = ( $langid !== null ? (string) $langid : null );
		$this->setModified();
	}


	/**
	 * Returns the type id of the media.
	 *
	 * @return integer|null Type of the media
	 */
	public function getTypeId()
	{
		return ( isset( $this->_values['typeid'] ) ? (int) $this->_values['typeid'] : null );
	}


	/**
	 * Sets the new type of the media.
	 *
	 * @param integer $typeid Type of the media
	 */
	public function setTypeId( $typeid )
	{
		if ( $typeid == $this->getTypeId() ) { return; }

		$this->_values['typeid'] = (int) $typeid;
		$this->setModified();
	}


	/**
	 * Returns the type code of the media item.
	 *
	 * @return string|null Type code of the media item
	 */
	public function getType()
	{
		return ( isset( $this->_values['type'] ) ? (string) $this->_values['type'] : null );
	}


	/**
	 * Returns the domain of the media item, if available.
	 *
	 * @return string Domain the media item belongs to
	 */
	public function getDomain()
	{
		return ( isset( $this->_values['domain'] ) ? (string) $this->_values['domain'] : '' );
	}


	/**
	 * Sets the domain of the media item.
	 *
	 * @param string $domain Domain of media item
	 */
	public function setDomain( $domain )
	{
		if ( $domain == $this->getDomain() ) { return; }

		$this->_values['domain'] = (string) $domain;
		$this->setModified();
	}


	/**
	 * Returns the label of the media item.
	 *
	 * @return string Label of the media item
	 */
	public function getLabel()
	{
		return ( isset( $this->_values['label'] ) ? (string) $this->_values['label'] : '' );
	}


	/**
	 * Sets the new label of the media item.
	 *
	 * @param string $label Label of the media item
	 */
	public function setLabel( $label )
	{
		if ( $label == $this->getLabel() ) { return; }

		$this->_values['label'] = (string) $label;
		$this->setModified();
	}


	/**
	 * Returns the status of the media item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus()
	{
		return ( isset( $this->_values['status'] ) ? (int) $this->_values['status'] : 0 );
	}


	/**
	 * Sets the new status of the media item.
	 *
	 * @param integer $status Status of the item
	 */
	public function setStatus( $status )
	{
		if ( $status == $this->getStatus() ) { return; }

		$this->_values['status'] = (int) $status;
		$this->setModified();
	}


	/**
	 * Returns the mime type of the media item.
	 *
	 * @return string Mime type of the media item
	 */
	public function getMimeType()
	{
		return ( isset( $this->_values['mimetype'] ) ? (string) $this->_values['mimetype'] : '' );
	}


	/**
	 * Sets the new mime type of the media.
	 *
	 * @param string $mimetype Mime type of the media item
	 */
	public function setMimeType( $mimetype )
	{
		if ( $mimetype == $this->getMimeType() ) { return; }

		$this->_values['mimetype'] = (string) $mimetype;
		$this->setModified();
	}


	/**
	 * Returns the url of the media item.
	 *
	 * @return string URL of the media file
	 */
	public function getUrl()
	{
		return ( isset( $this->_values['url'] ) ? (string) $this->_values['url'] : '' );
	}


	/**
	 * Sets the new url of the media item.
	 *
	 * @param string $url URL of the media file
	 */
	public function setUrl( $url )
	{
		if ( $url == $this->getUrl() ) { return; }

		$this->_values['url'] = (string) $url;
		$this->setModified();
	}


	/**
	 * Returns the preview url of the media item.
	 *
	 * @return string Preview URL of the media file
	 */
	public function getPreview()
	{
		return ( isset( $this->_values['preview'] ) ? (string) $this->_values['preview'] : '' );
	}


	/**
	 * Sets the new preview url of the media item.
	 *
	 * @param string $url Preview URL of the media file
	 */
	public function setPreview( $url )
	{
		if ( $url == $this->getPreview() ) { return; }

		$this->_values['preview'] = (string) $url;
		$this->setModified();
	}


	/**
	 * Returns the item values as array.
	 *
	 * @return Associative list of item properties and their values
	 */
	public function toArray()
	{
		$list = parent::toArray();

		$list['media.domain'] = $this->getDomain();
		$list['media.label'] = $this->getLabel();
		$list['media.languageid'] = $this->getLanguageId();
		$list['media.mimetype'] = $this->getMimeType(); 
		$list['media.typeid'] = $this->getTypeId();
		$list['media.type'] = $this->getType(); 
		$list['media.url'] = $this->getUrl();
		$list['media.preview'] = $this->getPreview();
		$list['media.status'] = $this->getStatus();

		return $list;
	}

}

