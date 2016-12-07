<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Locale
 */


namespace Aimeos\MShop\Locale\Item;


/**
 * Interface of transfer objects for request specific parameters.
 *
 * @package MShop
 * @subpackage Locale
 */
interface Iface extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Position\Iface
{
	/**
	 * Returns the site item object.
	 *
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Site item object
	 * @throws \Aimeos\MShop\Locale\Exception if site object isn't available
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
	 * @param integer ID of the shop instance
	 * @return \Aimeos\MShop\Locale\Item\Iface Locale item for chaining method calls
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
	 * @return \Aimeos\MShop\Locale\Item\Iface Locale item for chaining method calls
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
	 * @return \Aimeos\MShop\Locale\Item\Iface Locale item for chaining method calls
	 */
	public function setCurrencyId( $currencyid );


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
	 * @return \Aimeos\MShop\Locale\Item\Iface Locale item for chaining method calls
	 */
	public function setStatus( $status );

}
