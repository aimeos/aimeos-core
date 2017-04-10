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
 * Common locale class containing the site, language and currency information.
 *
 * @package MShop
 * @subpackage Locale
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Locale\Item\Iface
{
	private $site;
	private $sitePath;
	private $siteSubTree;
	private $values;


	/**
	 * Initializes the object with the locale values.
	 *
	 * @param array $values Values to be set on initialisation
	 * @param \Aimeos\MShop\Locale\Item\Site\Iface|null $site Site object
	 * @param array $sitePath List of site IDs up to the root site item
	 * @param array $siteSubTree List of site IDs from all sites below the current site
	 */
	public function __construct( array $values = [], \Aimeos\MShop\Locale\Item\Site\Iface $site = null,
		array $sitePath = [], array $siteSubTree = [] )
	{
		parent::__construct( 'locale.', $values );

		$this->values = $values;
		$this->site = $site;
		$this->sitePath = $sitePath;
		$this->siteSubTree = $siteSubTree;
	}


	/**
	 * Clones internal objects of the locale item.
	 */
	public function __clone()
	{
		$this->site = ( isset( $this->site ) ? clone $this->site : null );
	}


	/**
	 * Returns the site item object.
	 *
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Site item object
	 * @throws \Aimeos\MShop\Locale\Exception if site object isn't available
	 */
	public function getSite()
	{
		if( $this->site === null ) {
			throw new \Aimeos\MShop\Locale\Exception( 'No site item available' );
		}

		return $this->site;
	}


	/**
	 * Returns the list site IDs up to the root site item.
	 *
	 * @return array List of site IDs
	 */
	public function getSitePath()
	{
		return $this->sitePath;
	}


	/**
	 * Returns the list site IDs of the whole site subtree.
	 *
	 * @return array List of site IDs
	 */
	public function getSiteSubTree()
	{
		return $this->siteSubTree;
	}


	/**
	 * Returns the Site ID of the item.
	 *
	 * @return integer|null Site ID (or null for global site)
	 */
	public function getSiteId()
	{
		if( isset( $this->values['locale.siteid'] ) ) {
			return (int) $this->values['locale.siteid'];
		}

		return null;
	}


	/**
	 * Sets the identifier of the shop instance.
	 *
	 * @param integer ID of the shop instance
	 * @return \Aimeos\MShop\Locale\Item\Iface Locale item for chaining method calls
	 */
	public function setSiteId( $id )
	{
		if( $id === $this->getSiteId() ) { return $this; }

		$this->values['locale.siteid'] = (int) $id;
		$this->sitePath = array( (int) $id );
		$this->siteSubTree = array( (int) $id );

		/** @todo: Wrong site item shouldn't be available any more but causes problems in controller */

		$this->setModified();

		return $this;
	}


	/**
	 * Returns the ISO language code.
	 *
	 * @return string|null ISO language code (e.g. de or de_DE)
	 */
	public function getLanguageId()
	{
		if( isset( $this->values['locale.languageid'] ) ) {
			return (string) $this->values['locale.languageid'];
		}

		return null;
	}


	/**
	 * Sets the ISO language code.
	 *
	 * @param string|null $id ISO language code (e.g. de or de_DE)
	 * @return \Aimeos\MShop\Locale\Item\Iface Locale item for chaining method calls
	 * @throws \Aimeos\MShop\Exception If the language ID is invalid
	 */
	public function setLanguageId( $id )
	{
		if( $id == $this->getLanguageId() ) { return $this; }

		$this->values['locale.languageid'] = $this->checkLanguageId( $id );
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the currency ID.
	 *
	 * @return string|null Three letter ISO currency code (e.g. EUR)
	 */
	public function getCurrencyId()
	{
		if( isset( $this->values['locale.currencyid'] ) ) {
			return (string) $this->values['locale.currencyid'];
		}

		return null;
	}


	/**
	 * Sets the currency ID.
	 *
	 * @param string|null $currencyid Three letter ISO currency code (e.g. EUR)
	 * @return \Aimeos\MShop\Locale\Item\Iface Locale item for chaining method calls
	 * @throws \Aimeos\MShop\Exception If the currency ID is invalid
	 */
	public function setCurrencyId( $currencyid )
	{
		if( $currencyid == $this->getCurrencyId() ) { return $this; }

		$this->values['locale.currencyid'] = $this->checkCurrencyId( $currencyid );
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the position of the item.
	 *
	 * @return integer Position of the item relative to the other items
	 */
	public function getPosition()
	{
		if( isset( $this->values['locale.position'] ) ) {
			return (int) $this->values['locale.position'];
		}

		return 0;
	}


	/**
	 * Sets the position of the item.
	 *
	 * @param integer $pos Position of the item
	 * @return \Aimeos\MShop\Locale\Item\Iface Locale item for chaining method calls
	 */
	public function setPosition( $pos )
	{
		if( $pos == $this->getPosition() ) { return $this; }

		$this->values['locale.position'] = (int) $pos;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the status property of the locale item
	 *
	 * @return integer Returns the status of the locale item
	 */
	public function getStatus()
	{
		if( isset( $this->values['locale.status'] ) ) {
			return (int) $this->values['locale.status'];
		}

		return 0;
	}


	/**
	 * Sets the status property
	 *
	 * @param integer $status The status of the locale item
	 * @return \Aimeos\MShop\Locale\Item\Iface Locale item for chaining method calls
	 */
	public function setStatus( $status )
	{
		if( $status == $this->getStatus() ) { return $this; }

		$this->values['locale.status'] = (int) $status;
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
		return 'locale';
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
	 * @param boolean True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( $private = false )
	{
		$list = parent::toArray( $private );

		$list['locale.siteid'] = $this->getSiteId();
		$list['locale.languageid'] = $this->getLanguageId();
		$list['locale.currencyid'] = $this->getCurrencyId();
		$list['locale.position'] = $this->getPosition();
		$list['locale.status'] = $this->getStatus();

		return $list;
	}

}
