<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Locale
 */


/**
 * Interface of transfer objects for request specific parameters.
 *
 * @package MShop
 * @subpackage Locale
 */
interface MShop_Locale_Item_Interface extends MShop_Common_Item_Interface
{
	/**
	 * Returns the site item object.
	 *
	 * @return MShop_Locale_Item_Site_Interface Site item object
	 * @throws MShop_Locale_Exception if site object isn't available
	 */
	public function getSite();


	/**
	 * Returns the list site IDs up to the root site item.
	 *
	 * @return array List of site IDs
	 */
	public function getSitePath();


	/**
	 * Returns the list site IDs of the whole site subtree.
	 *
	 * @return array List of site IDs
	 */
	public function getSiteSubTree();


	/**
	 * Sets the identifier of the shop instance.
	 *
	 * @param integer ID of the shop instance.
	 * @return void
	 */
	public function setSiteId( $id );


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
	 * @return void
	 */
	public function setLanguageId( $langid );


	/**
	 * Returns the currency ID.
	 *
	 * @return string|null Three letter ISO currency code (e.g. EUR)
	 */
	public function getCurrencyId();


	/**
	 * Sets the currency ID.
	 *
	 * @param string|null $currencyid Three letter ISO currency code (e.g. EUR)
	 * @throws MShop_Exception If the currency ID is invalid
	 * @return void
	 */
	public function setCurrencyId( $currencyid );


	/**
	 * Returns the position of the item.
	 *
	 * @return integer Position of the item
	 */
	public function getPosition();


	/**
	 * Sets the position of the item.
	 *
	 * @param integer $pos Position of the item
	 * @return void
	 */
	public function setPosition( $pos );


	/**
	 * Returns the status property of the locale item
	 *
	 * @return integer Returns the status of the locale item
	 */
	public function getStatus();


	/**
	 * Sets the status property
	 *
	 * @param integer $status The status of the locale item
	 * @return void
	 */
	public function setStatus( $status );

}
