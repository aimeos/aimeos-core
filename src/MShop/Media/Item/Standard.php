<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 * @package MShop
 * @subpackage Media
 */


namespace Aimeos\MShop\Media\Item;

use \Aimeos\MShop\Common\Item\ListsRef;
use \Aimeos\MShop\Common\Item\PropertyRef;
use \Aimeos\MShop\Common\Item\TypeRef;


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
	use ListsRef\Traits, PropertyRef\Traits, TypeRef\Traits {
		PropertyRef\Traits::__clone as __cloneProperty;
		ListsRef\Traits::__clone insteadof PropertyRef\Traits;
		ListsRef\Traits::__clone as __cloneList;
		ListsRef\Traits::getName as getNameList;
	}


	private ?string $langid;


	/**
	 * Initializes the media item object.
	 *
	 * @param string $prefix Domain specific prefix string
	 * @param array $values Initial values of the media item
	 */
	public function __construct( string $prefix, array $values = [] )
	{
		parent::__construct( $prefix, $values );

		$this->langid = $values['.languageid'] ?? null;

		$this->initListItems( $values['.listitems'] ?? [] );
		$this->initPropertyItems( $values['.propitems'] ?? [] );
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
	 * Returns the name of the file system the referenced file is stored.
	 *
	 * @return string Name of the file system
	 */
	public function getFileSystem() : string
	{
		return $this->get( 'media.filesystem', 'fs-media' );
	}


	/**
	 * Sets the name of the file system the referenced file is stored.
	 *
	 * @param string $value Name of the file system
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 */
	public function setFileSystem( string $value ) : \Aimeos\MShop\Media\Item\Iface
	{
		return $this->set( 'media.filesystem', $value );
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
		return (string) ( $this->get( 'media.label' ) ?: basename( $this->getUrl() ) );
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
	 * @param bool $version TRUE to add file version as parameter, FALSE for path only
	 * @return string URL of the media file
	 */
	public function getUrl( bool $version = false ) : string
	{
		$url = (string) $this->get( 'media.url', '' );

		if( $url && $version && !\Aimeos\Base\Str::starts( $url, ['http', 'data:', '/'] ) && $this->getTimeModified() ) {
			$url .= '?v=' . str_replace( ['-', ' ', ':'], '', $this->getTimeModified() );
		}

		return $url;
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
		if( ( $list = (array) $this->get( 'media.preview', [] ) ) === [] ) {
			return $this->getUrl();
		}

		ksort( $list );
		$path = '';

		if( $width === false ) {
			$path = reset( $list );
		} elseif( $width === true ) {
			$path = end( $list );
		} elseif( isset( $list[$width] ) ) {
			$path = $list[$width];
		} else {
			$before = $after = [];

			foreach( $list as $idx => $path )
			{
				if( $idx < $width ) {
					$before[$idx] = $path;
				} else {
					$after[$idx] = $path;
				}
			}

			if( ( $path = array_shift( $after ) ) === null && ( $path = array_pop( $before ) ) === null ) {
				return '';
			}
		}

		if( $path && !\Aimeos\Base\Str::starts( $path, ['http', 'data:', '/'] ) && $this->getTimeModified() ) {
			$path .= '?v=' . str_replace( ['-', ' ', ':'], '', $this->getTimeModified() );
		}

		return (string) $path;
	}


	/**
	 * Returns all preview urls for images of different sizes.
	 *
	 * @param bool $version TRUE to add file version as parameter, FALSE for path only
	 * @return array Associative list of widths in pixels as keys and urls as values
	 */
	public function getPreviews( bool $version = false ) : array
	{
		$previews = (array) $this->get( 'media.preview', [] );

		if( $version && $this->getTimeModified() )
		{
			foreach( $previews as $key => $path )
			{
				if( $path && !\Aimeos\Base\Str::starts( $path, ['http', 'data:', '/'] ) ) {
					$previews[$key] = $path . '?v=' . str_replace( ['-', ' ', ':'], '', $this->getTimeModified() );
				}
			}
		}

		return $previews;
	}


	/**
	 * Sets the new preview url of the media item.
	 *
	 * @param string $url Preview URL of the media file
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 */
	public function setPreview( string $url ) : \Aimeos\MShop\Media\Item\Iface
	{
		return $this->set( 'media.preview', [1 => $url] );
	}


	/**
	 * Sets the new preview urls for images of different sizes.
	 *
	 * @param array $url List of preview URLs with widths of the media file in pixels as keys
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 */
	public function setPreviews( array $urls ) : \Aimeos\MShop\Media\Item\Iface
	{
		return $this->set( 'media.preview', $urls );
	}


	/**
	 * Returns the localized text type of the item or the internal label if no name is available.
	 *
	 * @param string $type Text type to be returned
	 * @param string|null $langId Two letter ISO Language code of the text
	 * @return string Specified text type or label of the item
	 */
	public function getName( string $type = 'name', ?string $langId = null ) : string
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
	 * Returns the type of the media item.
	 * Overwritten for different default value.
	 *
	 * @return string Type of the media item
	 */
	public function getType() : string
	{
		return $this->get( 'media.type', 'default' );
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
				case 'media.filesystem': $item->setFileSystem( $value ); break;
				case 'media.domain': $item->setDomain( $value ); break;
				case 'media.label': $item->setLabel( $value ); break;
				case 'media.languageid': $item->setLanguageId( $value ); break;
				case 'media.mimetype': $item->setMimeType( $value ); break;
				case 'media.type': $item->setType( $value ); break;
				case 'media.url': $item->setUrl( $value ); break;
				case 'media.preview': $item->setPreview( $value ); break;
				case 'media.previews': $item->setPreviews( (array) $value ); break;
				case 'media.status': $item->setStatus( (int) $value ); break;
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

		$list['media.filesystem'] = $this->getFileSystem();
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
