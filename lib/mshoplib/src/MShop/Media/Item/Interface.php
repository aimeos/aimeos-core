<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Media
 */


/**
 * Generic interface for all media item.
 *
 * @package MShop
 * @subpackage Media
 */
interface MShop_Media_Item_Interface
	extends MShop_Common_Item_ListRef_Interface, MShop_Common_Item_Typeid_Interface
{
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
	 * @throws MShop_Exception If the language ID is invalid
	 * @return void
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
	 * @return void
	 */
	public function setLabel( $label );

	/**
	 * Returns the status of the media item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus();

	/**
	 * Sets the new status of the media item.
	 *
	 * @param integer $status Status of the item
	 * @return void
	 */
	public function setStatus( $status );

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
	 * @return void
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
	 * @return void
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
	 * @return void
	 */
	public function setPreview( $url );
}
