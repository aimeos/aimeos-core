<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Locale
 */


/**
 * Common locale class containing the site, language and currency information.
 *
 * @package MShop
 * @subpackage Locale
 */
class MShop_Locale_Item_Default
	extends MShop_Common_Item_Abstract
	implements MShop_Locale_Item_Interface
{
	private $_site;
	private $_sitePath;
	private $_siteSubTree;
	private $_values;


	/**
	 * Initializes the object with the locale values.
	 *
	 * @param array $values Values to be set on initialisation
	 * @param MShop_Locale_Item_Site_Interface|null $site Site object
	 * @param array $sitePath List of site IDs up to the root site item
	 * @param array $siteSubTree List of site IDs from all sites below the current site
	 */
	public function __construct( array $values = array( ), MShop_Locale_Item_Site_Interface $site = null,
		array $sitePath = array(), array $siteSubTree = array() )
	{
		parent::__construct( 'locale.', $values );

		$this->_values = $values;
		$this->_site = $site;
		$this->_sitePath = $sitePath;
		$this->_siteSubTree = $siteSubTree;
	}


	/**
	 * Clones internal objects of the locale item.
	 */
	public function __clone()
	{
		$this->_site = ( isset( $this->_site ) ? clone $this->_site : null );
	}


	/**
	 * Returns the site item object.
	 *
	 * @return MShop_Locale_Item_Site_Interface Site item object
	 * @throws MShop_Locale_Exception if site object isn't available
	 */
	public function getSite()
	{
		if( $this->_site === null ) {
			throw new MShop_Locale_Exception( 'No site item available' );
		}

		return $this->_site;
	}


	/**
	 * Returns the list site IDs up to the root site item.
	 *
	 * @return array List of site IDs
	 */
	public function getSitePath()
	{
		return $this->_sitePath;
	}


	/**
	 * Returns the list site IDs of the whole site subtree.
	 *
	 * @return array List of site IDs
	 */
	public function getSiteSubTree()
	{
		return $this->_siteSubTree;
	}


	/**
	 * Returns the Site ID of the item.
	 *
	 * @return integer|null Site ID (or null for global site)
	 */
	public function getSiteId()
	{
		return ( isset( $this->_values['siteid'] ) ? (int) $this->_values['siteid'] : null );
	}


	/**
	 * Sets the identifier of the shop instance.
	 *
	 * @param integer ID of the shop instance.
	 */
	public function setSiteId( $id )
	{
		if( $id === $this->getSiteId() ) { return; }

		$this->_values['siteid'] = (int) $id;
		$this->_sitePath = array( (int) $id );
		$this->_siteSubTree = array( (int) $id );

		/** @todo: Wrong site item shouldn't be available any more but causes problems in controller */

		$this->setModified();
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
		if( $langid == $this->getLanguageId() ) { return; }

		$this->_checkLanguageId( $langid );
		$this->_values['langid'] = $langid;
		$this->setModified();
	}


	/**
	 * Returns the currency ID.
	 *
	 * @return string|null Three letter ISO currency code (e.g. EUR)
	 */
	public function getCurrencyId()
	{
		return ( isset( $this->_values['currencyid'] ) ? (string) $this->_values['currencyid'] : null );
	}


	/**
	 * Sets the currency ID.
	 *
	 * @param string|null $currencyid Three letter ISO currency code (e.g. EUR)
	 * @throws MShop_Exception If the currency ID is invalid
	 */
	public function setCurrencyId( $currencyid )
	{
		if( $currencyid == $this->getCurrencyId() ) { return; }

		$this->_checkCurrencyId( $currencyid );
		$this->_values['currencyid'] = $currencyid;
		$this->setModified();
	}


	/**
	 * Returns the position of the item.
	 *
	 * @return integer Position of the item relative to the other items
	 */
	public function getPosition()
	{
		return ( isset( $this->_values['pos'] ) ? (int) $this->_values['pos'] : 0 );
	}


	/**
	 * Sets the position of the item.
	 *
	 * @param integer $pos Position of the item
	 */
	public function setPosition( $pos )
	{
		if( $pos == $this->getPosition() ) { return; }

		$this->_values['pos'] = (int) $pos;
		$this->setModified();
	}


	/**
	 * Returns the status property of the locale item
	 *
	 * @return integer Returns the status of the locale item
	 */
	public function getStatus()
	{
		return ( isset( $this->_values['status'] ) ? (int) $this->_values['status'] : 0 );
	}


	/**
	 * Sets the status property
	 *
	 * @param integer $status The status of the locale item
	 */
	public function setStatus( $status )
	{
		if( $status == $this->getStatus() ) { return; }

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

		if( isset( $list['locale.siteid'] ) ) {
			$this->setSiteId( $list['locale.siteid'] );
		}

		$list = parent::fromArray( $list );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'locale.languageid': $this->setLanguageId( $value ); break;
				case 'locale.currencyid': $this->setCurrencyId( $value ); break;
				case 'locale.position': $this->setPosition( $value ); break;
				case 'locale.status': $this->setStatus( $value ); break;
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

		$list['locale.siteid'] = $this->getSiteId();
		$list['locale.languageid'] = $this->getLanguageId();
		$list['locale.currencyid'] = $this->getCurrencyId();
		$list['locale.position'] = $this->getPosition();
		$list['locale.status'] = $this->getStatus();

		return $list;
	}

}
