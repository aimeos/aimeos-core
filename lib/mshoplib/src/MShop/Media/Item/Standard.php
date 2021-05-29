<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Media
 */


namespace Aimeos\MShop\Media\Item;

use \Aimeos\MShop\Common\Item\ListsRef;
use \Aimeos\MShop\Common\Item\PropertyRef;


/**
 * Default implementation of the media item.
 *
 * @package MShop
 * @subpackage Media
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Media\Item\Iface
{
	use ListsRef\Traits, PropertyRef\Traits {
		PropertyRef\Traits::__clone as __cloneProperty;
		ListsRef\Traits::__clone insteadof PropertyRef\Traits;
		ListsRef\Traits::__clone as __cloneList;
		ListsRef\Traits::getName as getNameList;
	}


	private $langid;


	/**
	 * Initializes the media item object.
	 *
	 * @param array $values Initial values of the media item
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 * @param \Aimeos\MShop\Common\Item\Property\Iface[] $propItems List of property items
	 */
	public function __construct( array $values = [], array $listItems = [],
		array $refItems = [], array $propItems = [] )
	{
		if( isset( $values['media.preview'] ) && !isset( $values['media.previews'] ) ) {
			$values['media.previews'] = [1 => $values['media.preview']];
		}

		parent::__construct( 'media.', $values );

		$this->langid = ( isset( $values['.languageid'] ) ? $values['.languageid'] : null );
		$this->initListItems( $listItems, $refItems );
		$this->initPropertyItems( $propItems );
	}


	/**
	 * Creates a deep clone of all objects
	 */
	public function __clone()
	{
		parent::__clone();
		$this->__cloneList();
		$this->__cloneProperty();
	}


	/**
	 * Returns the ISO language code.
	 *
	 * @return string|null ISO language code (e.g. de or de_DE)
	 */
	public function getLanguageId() : ?string
	{
		return $this->get( 'media.languageid' );
	}


	/**
	 * Sets the ISO language code.
	 *
	 * @param string|null $id ISO language code (e.g. de or de_DE)
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 * @throws \Aimeos\MShop\Exception If the language ID is invalid
	 */
	public function setLanguageId( ?string $id ) : \Aimeos\MShop\Media\Item\Iface
	{
		return $this->set( 'media.languageid', $this->checkLanguageId( $id ) );
	}


	/**
	 * Returns the type code of the media item.
	 *
	 * @return string|null Type code of the media item
	 */
	public function getType() : ?string
	{
		return $this->get( 'media.type', 'default' );
	}


	/**
	 * Sets the new type of the media.
	 *
	 * @param string $type Type of the media
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 */
	public function setType( string $type ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'media.type', $this->checkCode( $type ) );
	}


	/**
	 * Returns the domain of the media item, if available.
	 *
	 * @return string Domain the media item belongs to
	 */
	public function getDomain() : string
	{
		return (string) $this->get( 'media.domain', '' );
	}


	/**
	 * Sets the domain of the media item.
	 *
	 * @param string $domain Domain of media item
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 */
	public function setDomain( string $domain ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'media.domain', (string) $domain );
	}


	/**
	 * Returns the label of the media item.
	 *
	 * @return string Label of the media item
	 */
	public function getLabel() : string
	{
		return (string) $this->get( 'media.label', '' );
	}


	/**
	 * Sets the new label of the media item.
	 *
	 * @param string $label Label of the media item
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 */
	public function setLabel( ?string $label ) : \Aimeos\MShop\Media\Item\Iface
	{
		return $this->set( 'media.label', (string) $label );
	}


	/**
	 * Returns the status of the media item.
	 *
	 * @return int Status of the item
	 */
	public function getStatus() : int
	{
		return (int) $this->get( 'media.status', 1 );
	}


	/**
	 * Sets the new status of the media item.
	 *
	 * @param int $status Status of the item
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 */
	public function setStatus( int $status ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'media.status', $status );
	}


	/**
	 * Returns the mime type of the media item.
	 *
	 * @return string Mime type of the media item
	 */
	public function getMimeType() : string
	{
		return (string) $this->get( 'media.mimetype', '' );
	}


	/**
	 * Sets the new mime type of the media.
	 *
	 * @param string $mimetype Mime type of the media item
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 */
	public function setMimeType( string $mimetype ) : \Aimeos\MShop\Media\Item\Iface
	{
		if( preg_match( '/^[a-z\-]+\/[a-zA-Z0-9\.\-\+]+$/', $mimetype ) !== 1 ) {
			throw new \Aimeos\MShop\Media\Exception( sprintf( 'Invalid mime type "%1$s"', $mimetype ) );
		}

		return $this->set( 'media.mimetype', (string) $mimetype );
	}


	/**
	 * Returns the url of the media item.
	 *
	 * @return string URL of the media file
	 */
	public function getUrl() : string
	{
		return (string) $this->get( 'media.url', '' );
	}


	/**
	 * Sets the new url of the media item.
	 *
	 * @param string|null $url URL of the media file
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 */
	public function setUrl( ?string $url ) : \Aimeos\MShop\Media\Item\Iface
	{
		return $this->set( 'media.url', (string) $url );
	}


	/**
	 * Returns the preview url of the media item.
	 *
	 * @param bool|int $size TRUE for the largest image, FALSE for the smallest or a concrete image width
	 * @return string Preview URL of the media file
	 */
	public function getPreview( $width = false ) : string
	{
		if( ( $list = (array) $this->get( 'media.previews', [] ) ) === [] ) {
			return '';
		}

		if( $width === false ) {
			return (string) reset( $list );
		} elseif( $width === true ) {
			return (string) end( $list );
		} elseif( isset( $list[$width] ) ) {
			return (string) $list[$width];
		}

		$before = $after = [];

		foreach( $list as $idx => $path )
		{
			if( $idx < $width ) {
				$before[$idx] = $path;
			} else {
				$after[$idx] = $path;
			}
		}

		if( ( $path = array_shift( $after ) ) !== null ) {
			return (string) $path;
		} elseif( ( $path = array_pop( $before ) ) !== null ) {
			return (string) $path;
		}

		return '';
	}


	/**
	 * Returns all preview urls for images of different sizes.
	 *
	 * @return array Associative list of widths in pixels as keys and urls as values
	 */
	public function getPreviews() : array
	{
		return (array) $this->get( 'media.previews', [] );
	}


	/**
	 * Sets the new preview url of the media item.
	 *
	 * @param string $url Preview URL of the media file
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 */
	public function setPreview( string $url ) : \Aimeos\MShop\Media\Item\Iface
	{
		return $this->set( 'media.previews', [1 => $url] );
	}


	/**
	 * Sets the new preview urls for images of different sizes.
	 *
	 * @param array $url List of preview URLs with widths of the media file in pixels as keys
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 */
	public function setPreviews( array $urls ) : \Aimeos\MShop\Media\Item\Iface
	{
		return $this->set( 'media.previews', $urls );
	}


	/**
	 * Returns the localized text type of the item or the internal label if no name is available.
	 *
	 * @param string $type Text type to be returned
	 * @param string|null $langId Two letter ISO Language code of the text
	 * @return string Specified text type or label of the item
	 */
	public function getName( string $type = 'name', string $langId = null ) : string
	{
		foreach( $this->getPropertyItems( $type ) as $propItem )
		{
			if( $propItem->getLanguageId() === $langId || $langId === null ) {
				return $propItem->getValue();
			}
		}

		return $this->getNameList( $type );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string
	{
		return 'media';
	}


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return bool True if available, false if not
	 */
	public function isAvailable() : bool
	{
		return parent::isAvailable() && $this->getStatus() > 0
			&& ( $this->langid === null || $this->getLanguageId() === null
			|| $this->getLanguageId() === $this->langid );
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'media.domain': $item = $item->setDomain( $value ); break;
				case 'media.label': $item = $item->setLabel( $value ); break;
				case 'media.languageid': $item = $item->setLanguageId( $value ); break;
				case 'media.mimetype': $item = $item->setMimeType( $value ); break;
				case 'media.type': $item = $item->setType( $value ); break;
				case 'media.url': $item = $item->setUrl( $value ); break;
				case 'media.preview': $item = $item->setPreview( $value ); break;
				case 'media.previews': $item = $item->setPreviews( (array) $value ); break;
				case 'media.status': $item = $item->setStatus( (int) $value ); break;
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

		$list['media.domain'] = $this->getDomain();
		$list['media.label'] = $this->getLabel();
		$list['media.languageid'] = $this->getLanguageId();
		$list['media.mimetype'] = $this->getMimeType();
		$list['media.type'] = $this->getType();
		$list['media.preview'] = $this->getPreview();
		$list['media.previews'] = $this->getPreviews();
		$list['media.url'] = $this->getUrl();
		$list['media.status'] = $this->getStatus();

		return $list;
	}
}
