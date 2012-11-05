<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Locale
 * @version $Id: Interface.php 14246 2011-12-09 12:25:12Z nsendetzky $
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
	 * Sets the identifier of the shop instance.
	 *
	 * @param ID of the shop instance.
	 */
	public function setSiteId( $id );


	/**
	 * Returns the two letter ISO language code.
	 *
	 * @return string Language ID
	 */
	public function getLanguageId();


	/**
	 * Sets the language ID.
	 *
	 * @param string $langid Two letter ISO language code
	 */
	public function setLanguageId( $langid );


	/**
	 * Returns the currency ID.
	 *
	 * @return string Currency ID (e.g: EUR)
	 */
	public function getCurrencyId();


	/**
	 * Sets the currency ID.
	 *
	 * @param string $currencyid Currency (e.g: EUR)
	 */
	public function setCurrencyId( $currencyid );


	/**
	 * Returns the position of the item.
	 *
	 * @param integer $pos Position of the item
	 */
	public function getPosition();


	/**
	 * Sets the position of the item.
	 *
	 * @param integer $pos Position of the item
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
	 */
	public function setStatus( $status );

}
