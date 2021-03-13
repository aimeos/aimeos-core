<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 * @package MShop
 * @subpackage Rule
 */


namespace Aimeos\MShop\Rule\Provider\Factory;


/**
 * Base class for rule provider implementations
 *
 * @package MShop
 * @subpackage Rule
 */
abstract class Base
	extends \Aimeos\MShop\Rule\Provider\Base
{
	/**
	 * Initializes the object instance
	 *
	 * PHP 7 fails with a wierd fatal error that decorator constructors must be
	 * compatible with the constructor of the factory interface if this
	 * intermediate constructor isn't implemented!
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @param \Aimeos\MShop\Rule\Item\Iface $item Rule item object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MShop\Rule\Item\Iface $item )
	{
		parent::__construct( $context, $item );
	}
}
