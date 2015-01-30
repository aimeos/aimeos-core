<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 */


/**
 * Common methods for all item objects.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class MShop_Common_Item_Abstract extends MW_Common_Item_Abstract
{
	private $_prefix;
	private $_values;
	private $_modified = false;

	/**
	 * Initializes the class properties.
	 *
	 * @param string $prefix Prefix for the keys returned by toArray()
	 * @param array $values Associative list of key/value pairs of the item properties
	 */
	public function __construct( $prefix, array $values )
	{
		$this->_prefix = (string) $prefix;
		$this->_values = $values;
	}


	/**
	 * Returns the ID of the item if available.
	 *
	 * @return string|null ID of the item
	 */
	public function getId()
	{
		return ( isset( $this->_values['id'] ) && $this->_values['id'] != '' ? (string) $this->_values['id'] : null );
	}


	/**
	 * Sets the new ID of the item.
	 *
	 * @param string|null $id ID of the item
	 */
	public function setId( $id )
	{
		if ( ( $this->_values['id'] = MShop_Common_Item_Abstract::checkId( $this->getId(), $id ) ) === null ) {
			$this->_modified = true;
		} else {
			$this->_modified = false;
		}
	}


	/**
	 * Returns the site ID of the item.
	 *
	 * @return integer|null Site ID or null if no site id is available
	 */
	public function getSiteId()
	{
		return ( isset( $this->_values['siteid'] ) ? (int) $this->_values['siteid'] : null );
	}


	/**
	 * Returns modification time of the order coupon.
	 *
	 * @return string|null Modification time (YYYY-MM-DD HH:mm:ss)
	 */
	public function getTimeModified()
	{
		return ( isset( $this->_values['mtime'] ) ? (string) $this->_values['mtime'] : null );
	}


	/**
	 * Returns the create date of the item.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getTimeCreated()
	{
		return ( isset( $this->_values['ctime'] ) ? (string) $this->_values['ctime'] : null );
	}


	/**
	 * Returns the name of editor who created/modified the item at last.
	 *
	 * @return string Name of editor who created/modified the item at last
	 */
	public function getEditor()
	{
		return ( isset( $this->_values['editor'] ) ? (string) $this->_values['editor'] : '' );
	}


	/**
	 * Tests if this Item object was modified.
	 *
	 * @return boolean True if modified, false if not
	 */
	public function isModified()
	{
		return $this->_modified;
	}


	/**
	 * Sets the modified flag of the object.
	 */
	public function setModified()
	{
		$this->_modified = true;
	}


	/**
	 * Sets the item values from the given array.
	 *
	 * @param array Associative list of item keys and their values
	 * @return array Associative list of keys and their values that are unknown
	 */
	public function fromArray( array $list )
	{
		if( array_key_exists( $this->_prefix . 'id', $list ) )
		{
			$this->setId( $list[$this->_prefix . 'id'] );
			unset( $list[$this->_prefix . 'id'] );
		}

		unset( $list[$this->_prefix . 'siteid'] );
		unset( $list[$this->_prefix . 'ctime'] );
		unset( $list[$this->_prefix . 'mtime'] );
		unset( $list[$this->_prefix . 'editor'] );

		return $list;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @return Associative list of item properties and their values
	 */
	public function toArray()
	{
		return array(
			$this->_prefix . 'id' => $this->getId(),
			$this->_prefix . 'siteid' => $this->getSiteId(),
			$this->_prefix . 'ctime' => $this->getTimeCreated(),
			$this->_prefix . 'mtime' => $this->getTimeModified(),
			$this->_prefix . 'editor' => $this->getEditor(),
		);
	}


	/**
	 * Checks if the new ID is valid for the item.
	 *
	 * @param string $old Current ID of the item
	 * @param string $new New ID which should be set in the item
	 * @return string Value of the new ID
	 * @throws MShop_Common_Exception if the ID is not null or not the same as the old one
	 */
	public static function checkId( $old, $new )
	{
		if( $new != null && $old != null && $old != $new ) {
			throw new MShop_Exception( sprintf( 'New ID "%1$s" for item differs from old ID "%2$s"', $new, $old ) );
		}

		return $new;
	}


	/**
	 * Tests if the date parameter represents an ISO format.
	 *
	 * @param string|null $date ISO date in yyyy-mm-dd HH:ii:ss format or null
	 * @throws MShop_Exception If the date is invalid
	 */
	protected function _checkDateFormat( $date )
	{
		$regex = '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/';

		if( $date !== null && preg_match( $regex, $date ) !== 1 ) {
			throw new MShop_Exception( sprintf( 'Invalid characters in date "%1$s". ISO format "YYYY-MM-DD hh:mm:ss" expected.', $date ) );
		}
	}


	/**
	 * Tests if the code is valid.
	 *
	 * @param string $code New code for an item
	 * @throws MShop_Exception If the code is invalid
	 */
	protected function _checkCode( $code )
	{
		if( strlen( $code ) > 32 ) {
			throw new MShop_Exception( sprintf( 'Code must not be longer than 32 characters' ) );
		}
	}


	/**
	 * Tests if the currency ID parameter represents an ISO currency format.
	 *
	 * @param string|null $currencyid Three letter ISO currency format, e.g. EUR
	 * @param boolean $null True if null is allowed, false if not
	 * @throws MShop_Exception If the currency ID is invalid
	 */
	protected function _checkCurrencyId( $currencyid, $null = true )
	{
		if( $null === false && $currencyid === null ) {
			throw new MShop_Exception( sprintf( 'Invalid ISO currency code "%1$s"', '<null>' ) );
		}

		if( $currencyid !== null && preg_match( '/^[A-Z]{3}$/', $currencyid ) !== 1 ) {
			throw new MShop_Exception( sprintf( 'Invalid ISO currency code "%1$s"', $currencyid ) );
		}
	}


	/**
	 * Tests if the language ID parameter represents an ISO language format.
	 *
	 * @param string|null $langid ISO language format, e.g. de or de_DE
	 * @throws MShop_Exception If the language ID is invalid
	 */
	protected function _checkLanguageId( $langid )
	{
		if( $langid !== null && preg_match( '/^[a-z]{2}(_[A-Z]{2})?$/', $langid ) !== 1 ) {
			throw new MShop_Exception( sprintf( 'Invalid ISO language code "%1$s"', $langid ) );
		}
	}


	/**
	 * Returns the raw value list.
	 *
	 * @return array Associative list of key/value pairs
	 */
	protected function _getRawValues()
	{
		return $this->_values;
	}
}
