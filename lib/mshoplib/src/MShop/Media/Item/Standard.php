<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Media
 */


namespace Aimeos\MShop\Media\Item;


/**
 * Default implementation of the media item.
 *
 * @package MShop
 * @subpackage Media
 */
class Standard
	extends \Aimeos\MShop\Common\Item\ListRef\Base
	implements \Aimeos\MShop\Media\Item\Iface
{
	private $values;


	/**
	 * Initializes the media item object.
	 *
	 * @param array $values Initial values of the media item
	 * @param \Aimeos\MShop\Common\Lists\Item\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 */
	public function __construct( array $values = [], array $listItems = [], array $refItems = [] )
	{
		parent::__construct( 'media.', $values, $listItems, $refItems );

		$this->values = $values;
	}


	/**
	 * Returns the ISO language code.
	 *
	 * @return string|null ISO language code (e.g. de or de_DE)
	 */
	public function getLanguageId()
	{
		if( isset( $this->values['media.languageid'] ) ) {
			return (string) $this->values['media.languageid'];
		}

		return null;
	}


	/**
	 * Sets the ISO language code.
	 *
	 * @param string|null $id ISO language code (e.g. de or de_DE)
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 * @throws \Aimeos\MShop\Exception If the language ID is invalid
	 */
	public function setLanguageId( $id )
	{
		if( $id === $this->getLanguageId() ) { return $this; }

		$this->values['media.languageid'] = $this->checkLanguageId( $id );
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the type id of the media.
	 *
	 * @return integer|null Type of the media
	 */
	public function getTypeId()
	{
		if( isset( $this->values['media.typeid'] ) ) {
			return (int) $this->values['media.typeid'];
		}

		return null;
	}


	/**
	 * Sets the new type of the media.
	 *
	 * @param integer $typeid Type of the media
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 */
	public function setTypeId( $typeid )
	{
		if( $typeid == $this->getTypeId() ) { return $this; }

		$this->values['media.typeid'] = (int) $typeid;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the type code of the media item.
	 *
	 * @return string|null Type code of the media item
	 */
	public function getType()
	{
		if( isset( $this->values['media.type'] ) ) {
			return (string) $this->values['media.type'];
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
		if( isset( $this->values['media.typename'] ) ) {
			return (string) $this->values['media.typename'];
		}

		return null;
	}


	/**
	 * Returns the domain of the media item, if available.
	 *
	 * @return string Domain the media item belongs to
	 */
	public function getDomain()
	{
		if( isset( $this->values['media.domain'] ) ) {
			return (string) $this->values['media.domain'];
		}

		return '';
	}


	/**
	 * Sets the domain of the media item.
	 *
	 * @param string $domain Domain of media item
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 */
	public function setDomain( $domain )
	{
		if( $domain == $this->getDomain() ) { return $this; }

		$this->values['media.domain'] = (string) $domain;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the label of the media item.
	 *
	 * @return string Label of the media item
	 */
	public function getLabel()
	{
		if( isset( $this->values['media.label'] ) ) {
			return (string) $this->values['media.label'];
		}

		return '';
	}


	/**
	 * Sets the new label of the media item.
	 *
	 * @param string $label Label of the media item
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 */
	public function setLabel( $label )
	{
		if( $label == $this->getLabel() ) { return $this; }

		$this->values['media.label'] = (string) $label;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the status of the media item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus()
	{
		if( isset( $this->values['media.status'] ) ) {
			return (int) $this->values['media.status'];
		}

		return 0;
	}


	/**
	 * Sets the new status of the media item.
	 *
	 * @param integer $status Status of the item
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 */
	public function setStatus( $status )
	{
		if( $status == $this->getStatus() ) { return $this; }

		$this->values['media.status'] = (int) $status;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the mime type of the media item.
	 *
	 * @return string Mime type of the media item
	 */
	public function getMimeType()
	{
		if( isset( $this->values['media.mimetype'] ) ) {
			return (string) $this->values['media.mimetype'];
		}

		return '';
	}


	/**
	 * Sets the new mime type of the media.
	 *
	 * @param string $mimetype Mime type of the media item
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 */
	public function setMimeType( $mimetype )
	{
		if( $mimetype == $this->getMimeType() ) { return $this; }

		if( preg_match( '/^[a-z\-]+\/[a-zA-Z0-9\.\-\+]+$/', $mimetype ) !== 1 ) {
			throw new \Aimeos\MShop\Media\Exception( sprintf( 'Invalid mime type "%1$s"', $mimetype ) );
		}

		$this->values['media.mimetype'] = (string) $mimetype;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the url of the media item.
	 *
	 * @return string URL of the media file
	 */
	public function getUrl()
	{
		if( isset( $this->values['media.url'] ) ) {
			return (string) $this->values['media.url'];
		}

		return '';
	}


	/**
	 * Sets the new url of the media item.
	 *
	 * @param string $url URL of the media file
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 */
	public function setUrl( $url )
	{
		if( $url == $this->getUrl() ) { return $this; }

		$this->values['media.url'] = (string) $url;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the preview url of the media item.
	 *
	 * @return string Preview URL of the media file
	 */
	public function getPreview()
	{
		if( isset( $this->values['media.preview'] ) ) {
			return (string) $this->values['media.preview'];
		}

		return '';
	}


	/**
	 * Sets the new preview url of the media item.
	 *
	 * @param string $url Preview URL of the media file
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 */
	public function setPreview( $url )
	{
		if( $url == $this->getPreview() ) { return $this; }

		$this->values['media.preview'] = (string) $url;
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
		return 'media';
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
		unset( $list['media.type'], $list['media.typename'] );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'media.domain': $this->setDomain( $value ); break;
				case 'media.label': $this->setLabel( $value ); break;
				case 'media.languageid': $this->setLanguageId( $value ); break;
				case 'media.mimetype': $this->setMimeType( $value ); break;
				case 'media.typeid': $this->setTypeId( $value ); break;
				case 'media.url': $this->setUrl( $value ); break;
				case 'media.preview': $this->setPreview( $value ); break;
				case 'media.status': $this->setStatus( $value ); break;
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

		$list['media.domain'] = $this->getDomain();
		$list['media.label'] = $this->getLabel();
		$list['media.languageid'] = $this->getLanguageId();
		$list['media.mimetype'] = $this->getMimeType();
		$list['media.type'] = $this->getType();
		$list['media.typename'] = $this->getTypeName();
		$list['media.preview'] = $this->getPreview();
		$list['media.url'] = $this->getUrl();
		$list['media.status'] = $this->getStatus();

		if( $private === true ) {
			$list['media.typeid'] = $this->getTypeId();
		}

		return $list;
	}

}
