<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2017
 * @package MShop
 * @subpackage Media
 */


namespace Aimeos\MShop\Media\Item;


/**
 * Generic interface for all media item.
 *
 * @package MShop
 * @subpackage Media
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Domain\Iface,
		\Aimeos\MShop\Common\Item\ListRef\Iface, \Aimeos\MShop\Common\Item\Status\Iface,
		\Aimeos\MShop\Common\Item\Typeid\Iface
{
	/**
	 * Returns the property items of the media item
	 *
	 * @param string|null $type Name of the property item type or null for all
	 * @param boolean $active True to return only active items, false to return all
	 * @return \Aimeos\MShop\Product\Item\Property\Iface[] Associative list of property IDs as keys and property items as values
	 */
	public function getPropertyItems( $type = null, $active = true );

	/**
	 * Returns the ISO language code.
	 *
	 * @return string|null ISO language code (e.g. de or de_DE)
	 */
	public function getLanguageId();

	/**
	 * Sets the ISO language code.
	 *
	 * @param string|null $langid ISO language code (e.g. de or de_DE)
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 */
	public function setLanguageId( $langid );

	/**
	 * Returns the name of the media item.
	 *
	 * @return string Label of the media item
	 */
	public function getLabel();

	/**
	 * Sets the new label of the media item.
	 *
	 * @param string $label Type label of the media item
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 */
	public function setLabel( $label );

	/**
	 * Returns the mime type of the media item.
	 *
	 * @return string Mime type of the media item
	 */
	public function getMimeType();

	/**
	 * Sets the new mime type of the media.
	 *
	 * @param string $mimetype Mime type of the media item
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 */
	public function setMimeType( $mimetype );

	/**
	 * Returns the url of the media item.
	 *
	 * @return string URL of the media file
	 */
	public function getUrl();

	/**
	 * Sets the new url of the media item.
	 *
	 * @param string $url URL of the media file
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 */
	public function setUrl( $url );

	/**
	 * Returns the preview url of the media item.
	 *
	 * @return string Preview URL of the media file
	 */
	public function getPreview();

	/**
	 * Sets the new preview url of the media item.
	 *
	 * @param string $url Preview URL of the media file
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 */
	public function setPreview( $url );
}
