<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 * @package MShop
 * @subpackage Review
 */


namespace Aimeos\MShop\Review\Item;


/**
 * Default impelementation of a review item.
 *
 * @package MShop
 * @subpackage Review
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Review\Item\Iface
{
	/**
	 * Initializes the item object.
	 *
	 * @param array $values Parameter for initializing the basic properties
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'review.', $values );
	}


	/**
	 * Returns the comment for the reviewed item
	 *
	 * @return string Comment for the reviewed item
	 */
	public function getComment() : string
	{
		return (string) $this->get( 'review.comment', '' );
	}


	/**
	 * Sets the new comment for the reviewed item
	 *
	 * @param string|null $value New comment for the reviewed item
	 * @return \Aimeos\MShop\Review\Item\Iface Review item for chaining method calls
	 */
	public function setComment( ?string $value ) : \Aimeos\MShop\Review\Item\Iface
	{
		return $this->set( 'review.comment', strip_tags( $value ) );
	}


	/**
	 * Returns the ID of the reviewer
	 *
	 * @return string ID of the customer item
	 */
	public function getCustomerId() : string
	{
		return (string) $this->get( 'review.customerid', '' );
	}


	/**
	 * Sets the ID of the reviewer
	 *
	 * @param string $value New ID of the customer item
	 * @return \Aimeos\MShop\Review\Item\Iface Review item for chaining method calls
	 */
	public function setCustomerId( string $value ) : \Aimeos\MShop\Review\Item\Iface
	{
		return $this->set( 'review.customerid', $value );
	}


	/**
	 * Returns the domain the review is valid for.
	 *
	 * @return string Domain name
	 */
	public function getDomain() : string
	{
		return (string) $this->get( 'review.domain', '' );
	}


	/**
	 * Sets the new domain the review is valid for.
	 *
	 * @param string $value Domain name
	 * @return \Aimeos\MShop\Common\Item\Iface Common item for chaining method calls
	 */
	public function setDomain( string $value ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'review.domain', $value );
	}


	/**
	 * Returns the ID of the ordered review
	 *
	 * @return string|null ID of the ordered review
	 */
	public function getOrderProductId() : ?string
	{
		return (string) $this->get( 'review.orderproductid', '' );
	}


	/**
	 * Sets the ID of the ordered review item which the customer subscribed for
	 *
	 * @param string $value ID of the ordered review
	 * @return \Aimeos\MShop\Review\Item\Iface Review item for chaining method calls
	 */
	public function setOrderProductId( string $value ) : \Aimeos\MShop\Review\Item\Iface
	{
		return $this->set( 'review.orderproductid', $value );
	}


	/**
	 * Returns the name of the reviewer
	 *
	 * @return string Name of the reviewer
	 */
	public function getName() : string
	{
		return (string) $this->get( 'review.name', '' );
	}


	/**
	 * Sets the new name of the reviewer
	 *
	 * @param string $value New name of the reviewer
	 * @return \Aimeos\MShop\Review\Item\Iface Review item for chaining method calls
	 */
	public function setName( string $value ) : \Aimeos\MShop\Review\Item\Iface
	{
		return $this->set( 'review.name', strip_tags( $value ) );
	}


	/**
	 * Returns the rating for the reviewed item
	 *
	 * @return int Rating for the reviewed item (higher is better)
	 */
	public function getRating() : int
	{
		return (int) $this->get( 'review.rating', 0 );
	}


	/**
	 * Sets the new rating for the reviewed item
	 *
	 * @param int $value Rating for the reviewed item (higher is better)
	 * @return \Aimeos\MShop\Review\Item\Iface Review item for chaining method calls
	 */
	public function setRating( int $value ) : \Aimeos\MShop\Review\Item\Iface
	{
		return $this->set( 'review.rating', min( 5, max( 0, $value ) ) );
	}


	/**
	 * Returns the reference ID of the reviewed item, like the unique ID of a product item or a customer item
	 *
	 * @return string Reference ID of the common list item
	 */
	public function getRefId() : string
	{
		return (string) $this->get( 'review.refid', '' );
	}


	/**
	 * Sets the new reference ID of the common list item, like the unique ID of a product item or a customer item
	 *
	 * @param string $value New reference ID of the common list item
	 * @return \Aimeos\MShop\Review\Item\Iface Review item for chaining method calls
	 */
	public function setRefId( string $value ) : \Aimeos\MShop\Review\Item\Iface
	{
		return $this->set( 'review.refid', $value );
	}


	/**
	 * Returns the response to the review
	 *
	 * @return string Response to the review
	 */
	public function getResponse() : string
	{
		return (string) $this->get( 'review.response', '' );
	}


	/**
	 * Sets the new response to the review
	 *
	 * @param string|null $value New response to the review
	 * @return \Aimeos\MShop\Review\Item\Iface Review item for chaining method calls
	 */
	public function setResponse( ?string $value ) : \Aimeos\MShop\Review\Item\Iface
	{
		return $this->set( 'review.response', strip_tags( $value ) );
	}


	/**
	 * Returns the status of the review item.
	 *
	 * @return int Status of the review item
	 */
	public function getStatus() : int
	{
		return (int) $this->get( 'review.status', 1 );
	}


	/**
	 * Sets the new status of the review item.
	 *
	 * @param int $status New status of the review item
	 * @return \Aimeos\MShop\Review\Item\Iface Review item for chaining method calls
	 */
	public function setStatus( int $status ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'review.status', $status );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string
	{
		return 'review';
	}


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return bool True if available, false if not
	 */
	public function isAvailable() : bool
	{
		return parent::isAvailable() && $this->getStatus() > 0;
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Common\Item\Iface Common item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'review.orderproductid': !$private ?: $item = $item->setOrderProductId( $value ); break;
				case 'review.customerid': !$private ?: $item = $item->setCustomerId( $value ); break;
				case 'review.refid': $item = $item->setRefId( $value ); break;
				case 'review.domain': $item = $item->setDomain( $value ); break;
				case 'review.comment': $item = $item->setComment( $value ); break;
				case 'review.response': $item = $item->setResponse( $value ); break;
				case 'review.status': $item = $item->setStatus( (int) $value ); break;
				case 'review.rating': $item = $item->setRating( (int) $value ); break;
				case 'review.name': $item = $item->setName( $value ); break;
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

		$list['review.refid'] = $this->getRefId();
		$list['review.domain'] = $this->getDomain();
		$list['review.response'] = $this->getResponse();
		$list['review.comment'] = $this->getComment();
		$list['review.rating'] = $this->getRating();
		$list['review.status'] = $this->getStatus();
		$list['review.name'] = $this->getName();
		$list['review.ctime'] = $this->getTimeCreated();

		if( $private )
		{
			$list['review.orderproductid'] = $this->getOrderProductId();
			$list['review.customerid'] = $this->getCustomerId();
		}

		return $list;
	}
}
