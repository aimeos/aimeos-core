<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
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
	 * @return void
	 */
	public function __construct( \Aimeos\MW\Translation\Iface $object );
}
