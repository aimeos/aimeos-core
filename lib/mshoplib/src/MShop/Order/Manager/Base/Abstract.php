<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 * @version $Id: Abstract.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Basic methods and constants for order base items (shopping basket).
 *
 * @package MShop
 * @subpackage Order
 */
abstract class MShop_Order_Manager_Base_Abstract
	extends MShop_Common_Manager_Abstract
	implements MShop_Order_Manager_Base_Interface
{
	/**
	 * Unlock basket.
	 * Disable the lock for the serialized basket in the session so
	 * modifications of the basket content are allowed again. Note that the
	 * locks are advisory locks that can't be enforced if code doesn't care
	 * about the lock.
	 */
	const LOCK_DISABLE = 0;

	/**
	 * Lock basket.
	 * Enable the lock for the serialized basket in the session so
	 * modifications of the basket content are not allowed any more. Note that
	 * the locks are advisory locks that can't be enforced if code doesn't care
	 * about the lock.
	 */
	const LOCK_ENABLE = 1;


	/**
	 * Checks if the lock value is a valid constant.
	 *
	 * @param integer $value Lock constant
	 * @throws MShop_Order_Exception If given value is invalid
	 */
	protected function _checkLock($value)
	{
		switch($value)
		{
			case MShop_Order_Manager_Base_Abstract::LOCK_DISABLE:
			case MShop_Order_Manager_Base_Abstract::LOCK_ENABLE:
				break;
			default:
				throw new MShop_Order_Exception( sprintf( 'Lock flag "%1$d" not within allowed range.', $value ) );
		}
	}
}
