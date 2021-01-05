<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 * @package MShop
 * @subpackage Review
 */


namespace Aimeos\MShop\Review\Item;


/**
 * Generic interface for review items
 *
 * @package MShop
 * @subpackage Review
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Domain\Iface, \Aimeos\MShop\Common\Item\Status\Iface
{
	/**
	 * Returns the comment for the reviewed item
	 *
	 * @return string Comment for the reviewed item
	 */
	public function getComment() : string;

	/**
	 * Sets the new comment for the reviewed item
	 *
	 * @param string|null $value New comment for the reviewed item
	 * @return \Aimeos\MShop\Review\Item\Iface Review item for chaining method calls
	 */
	public function setComment( ?string $value ) : \Aimeos\MShop\Review\Item\Iface;

	/**
	 * Returns the ID of the reviewer
	 *
	 * @return string ID of the customer item
	 */
	public function getCustomerId() : string;

	/**
	 * Sets the ID of the reviewer
	 *
	 * @param string $value New ID of the customer item
	 * @return \Aimeos\MShop\Review\Item\Iface Review item for chaining method calls
	 */
	public function setCustomerId( string $value ) : \Aimeos\MShop\Review\Item\Iface;

	/**
	 * Returns the name of the reviewer
	 *
	 * @return string Name of the reviewer
	 */
	public function getName() : string;

	/**
	 * Sets the new name of the reviewer
	 *
	 * @param string $value New name of the reviewer
	 * @return \Aimeos\MShop\Review\Item\Iface Review item for chaining method calls
	 */
	public function setName( string $value ) : \Aimeos\MShop\Review\Item\Iface;

	/**
	 * Returns the ID of the ordered product
	 *
	 * @return string|null ID of the ordered product
	 */
	public function getOrderProductId() : ?string;

	/**
	 * Sets the ID of the ordered product item which the customer subscribed for
	 *
	 * @param string $value ID of the ordered product
	 * @return \Aimeos\MShop\Review\Item\Iface Review item for chaining method calls
	 */
	public function setOrderProductId( string $value ) : \Aimeos\MShop\Review\Item\Iface;

	/**
	 * Returns the rating for the reviewed item
	 *
	 * @return int Rating for the reviewed item (higher is better)
	 */
	public function getRating() : int;

	/**
	 * Sets the new rating for the reviewed item
	 *
	 * @param int $value Rating for the reviewed item (higher is better)
	 * @return \Aimeos\MShop\Review\Item\Iface Review item for chaining method calls
	 */
	public function setRating( int $value ) : \Aimeos\MShop\Review\Item\Iface;

	/**
	 * Returns the reference ID of the reviewed item, like the unique ID of a product item or a customer item
	 *
	 * @return string Reference ID of the common list item
	 */
	public function getRefId() : string;

	/**
	 * Sets the new reference ID of the common list item, like the unique ID of a product item or a customer item
	 *
	 * @param string $value New reference ID of the common list item
	 * @return \Aimeos\MShop\Review\Item\Iface Review item for chaining method calls
	 */
	public function setRefId( string $value ) : \Aimeos\MShop\Review\Item\Iface;

	/**
	 * Returns the response to the review
	 *
	 * @return string Response to the review
	 */
	public function getResponse() : string;

	/**
	 * Sets the new response to the review
	 *
	 * @param string|null $value New response to the review
	 * @return \Aimeos\MShop\Review\Item\Iface Review item for chaining method calls
	 */
	public function setResponse( ?string $value ) : \Aimeos\MShop\Review\Item\Iface;
}
