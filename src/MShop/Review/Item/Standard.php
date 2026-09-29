<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2026
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
	 * @return static Review item for chaining method calls
	 */
	public function setComment( ?string $value ) : static
	{
		return $this->set( 'review.comment', strip_tags( $value ?? '' ) );
	}


	/**
	 * Returns the ID of the reviewer
	 *
	 * @return string|null ID of the customer item
	 */
	public function getCustomerId() : ?string
	{
		// @phpstan-ignore return.type
		return $this->get( 'review.customerid' );
	}


	/**
	 * Sets the ID of the reviewer
	 *
	 * @param string $value New ID of the customer item
	 * @return static Review item for chaining method calls
	 */
	public function setCustomerId( string $value ) : static
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
	 * @return static Common item for chaining method calls
	 */
	public function setDomain( string $value ) : static
	{
		return $this->set( 'review.domain', $this->checkDomain( $value ) );
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
	 * @return static Review item for chaining method calls
	 */
	public function setOrderProductId( string $value ) : static
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
	 * @return static Review item for chaining method calls
	 */
	public function setName( string $value ) : static
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
	 * @return static Review item for chaining method calls
	 */
	public function setRating( int $value ) : static
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
	 * @return static Review item for chaining method calls
	 */
	public function setRefId( string $value ) : static
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
	 * @return static Review item for chaining method calls
	 */
	public function setResponse( ?string $value ) : static
	{
		return $this->set( 'review.response', strip_tags( $value ?? '' ) );
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
	 * @return static Review item for chaining method calls
	 */
	public function setStatus( int $status ) : static
	{
		return $this->set( 'review.status', $status );
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
	 * @type array &$list Associative list of item keys and their values
	 * @param bool $private True to set private properties too, false for public only
	 * @return static Common item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : static
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'review.orderproductid': !$private ?: $item->setOrderProductId( (string) $value ); break;
				case 'review.customerid': !$private ?: $item->setCustomerId( (string) $value ); break;
				case 'review.refid': $item->setRefId( (string) $value ); break;
				case 'review.domain': $item->setDomain( (string) $value ); break;
				case 'review.comment': $item->setComment( $value ? (string) $value : null ); break;
				case 'review.response': $item->setResponse( $value ? (string) $value : null ); break;
				case 'review.status': $item->setStatus( (int) $value ); break;
				case 'review.rating': $item->setRating( (int) $value ); break;
				case 'review.name': $item->setName( (string) $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param bool $private True to return private properties, false for public only
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
