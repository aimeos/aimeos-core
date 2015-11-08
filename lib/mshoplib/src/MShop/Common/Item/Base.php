<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item;


/**
 * Common methods for all item objects.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class Base extends \Aimeos\MW\Common\Item\Base
{
	private $prefix;
	private $values;
	private $modified = false;

	/**
	 * Initializes the class properties.
	 *
	 * @param string $prefix Prefix for the keys returned by toArray()
	 * @param array $values Associative list of key/value pairs of the item properties
	 */
	public function __construct( $prefix, array $values )
	{
		$this->prefix = (string) $prefix;
		$this->values = $values;
	}


	/**
	 * Returns the ID of the item if available.
	 *
	 * @return string|null ID of the item
	 */
	public function getId()
	{
		return ( isset( $this->values['id'] ) && $this->values['id'] != '' ? (string) $this->values['id'] : null );
	}


	/**
	 * Sets the new ID of the item.
	 *
	 * @param string|null $id ID of the item
	 */
	public function setId( $id )
	{
		if( ( $this->values['id'] = \Aimeos\MShop\Common\Item\Base::checkId( $this->getId(), $id ) ) === null ) {
			$this->modified = true;
		} else {
			$this->modified = false;
		}
	}


	/**
	 * Returns the site ID of the item.
	 *
	 * @return integer|null Site ID or null if no site id is available
	 */
	public function getSiteId()
	{
		return ( isset( $this->values['siteid'] ) ? (int) $this->values['siteid'] : null );
	}


	/**
	 * Returns modification time of the order coupon.
	 *
	 * @return string|null Modification time (YYYY-MM-DD HH:mm:ss)
	 */
	public function getTimeModified()
	{
		return ( isset( $this->values['mtime'] ) ? (string) $this->values['mtime'] : null );
	}


	/**
	 * Returns the create date of the item.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getTimeCreated()
	{
		return ( isset( $this->values['ctime'] ) ? (string) $this->values['ctime'] : null );
	}


	/**
	 * Returns the name of editor who created/modified the item at last.
	 *
	 * @return string Name of editor who created/modified the item at last
	 */
	public function getEditor()
	{
		return ( isset( $this->values['editor'] ) ? (string) $this->values['editor'] : '' );
	}


	/**
	 * Tests if this Item object was modified.
	 *
	 * @return boolean True if modified, false if not
	 */
	public function isModified()
	{
		return $this->modified;
	}


	/**
	 * Sets the modified flag of the object.
	 */
	public function setModified()
	{
		$this->modified = true;
	}


	/**
	 * Sets the item values from the given array.
	 *
	 * @param array Associative list of item keys and their values
	 * @return array Associative list of keys and their values that are unknown
	 */
	public function fromArray( array $list )
	{
		if( array_key_exists( $this->prefix . 'id', $list ) )
		{
			$this->setId( $list[$this->prefix . 'id'] );
			unset( $list[$this->prefix . 'id'] );
		}

		unset( $list[$this->prefix . 'siteid'] );
		unset( $list[$this->prefix . 'ctime'] );
		unset( $list[$this->prefix . 'mtime'] );
		unset( $list[$this->prefix . 'editor'] );

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
			$this->prefix . 'id' => $this->getId(),
			$this->prefix . 'siteid' => $this->getSiteId(),
			$this->prefix . 'ctime' => $this->getTimeCreated(),
			$this->prefix . 'mtime' => $this->getTimeModified(),
			$this->prefix . 'editor' => $this->getEditor(),
		);
	}


	/**
	 * Checks if the new ID is valid for the item.
	 *
	 * @param string $old Current ID of the item
	 * @param string $new New ID which should be set in the item
	 * @return string Value of the new ID
	 * @throws \Aimeos\MShop\Common\Exception if the ID is not null or not the same as the old one
	 */
	public static function checkId( $old, $new )
	{
		if( $new != null && $old != null && $old != $new ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'New ID "%1$s" for item differs from old ID "%2$s"', $new, $old ) );
		}

		return $new;
	}


	/**
	 * Tests if the date parameter represents an ISO format.
	 *
	 * @param string|null $date ISO date in yyyy-mm-dd HH:ii:ss format or null
	 * @return string|null Clean date or null for no date
	 * @throws \Aimeos\MShop\Exception If the date is invalid
	 */
	protected function checkDateFormat( $date )
	{
		$regex = '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]( |T)[0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/';

		if( $date !== null )
		{
			if( preg_match( $regex, (string) $date ) !== 1 ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Invalid characters in date "%1$s". ISO format "YYYY-MM-DD hh:mm:ss" expected.', $date ) );
			}

			return str_replace( 'T', ' ', (string) $date );
		}
	}


	/**
	 * Tests if the code is valid.
	 *
	 * @param string $code New code for an item
	 * @return string Item code
	 * @throws \Aimeos\MShop\Exception If the code is invalid
	 */
	protected function checkCode( $code )
	{
		if( strlen( $code ) > 32 ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Code must not be longer than 32 characters' ) );
		}

		return (string) $code;
	}


	/**
	 * Tests if the currency ID parameter represents an ISO currency format.
	 *
	 * @param string|null $currencyid Three letter ISO currency format, e.g. EUR
	 * @param boolean $null True if null is allowed, false if not
	 * @return string|null Three letter ISO currency ID or null for no currency
	 * @throws \Aimeos\MShop\Exception If the currency ID is invalid
	 */
	protected function checkCurrencyId( $currencyid, $null = true )
	{
		if( $null === false && $currencyid === null ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Invalid ISO currency code "%1$s"', '<null>' ) );
		}

		if( $currencyid !== null && preg_match( '/^[A-Z]{3}$/', $currencyid ) !== 1 ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Invalid ISO currency code "%1$s"', $currencyid ) );
		}

		return $currencyid;
	}


	/**
	 * Tests if the language ID parameter represents an ISO language format.
	 *
	 * @param string|null $langid ISO language format, e.g. de or de_DE
	 * @return string|null ISO language ID or null for no language
	 * @throws \Aimeos\MShop\Exception If the language ID is invalid
	 */
	protected function checkLanguageId( $langid )
	{
		if( $langid !== null && preg_match( '/^[a-z]{2}(_[A-Z]{2})?$/', $langid ) !== 1 ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Invalid ISO language code "%1$s"', $langid ) );
		}

		return $langid;
	}


	/**
	 * Returns the raw value list.
	 *
	 * @return array Associative list of key/value pairs
	 */
	protected function getRawValues()
	{
		return $this->values;
	}
}
