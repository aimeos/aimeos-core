<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Translation
 */


namespace Aimeos\MW\Translation\Decorator;


/**
 * Decorator interface for translation classes
 *
 * @package MW
 * @subpackage Translation
 */
interface Iface extends \Aimeos\MW\Translation\Iface
{
	/**
	 * Initializes the decorator.
	 *
	 * @param \Aimeos\MW\Translation\Iface $object Translation object or decorator
	 * @return null
	 */
	public function __construct( \Aimeos\MW\Translation\Iface $object );
}
