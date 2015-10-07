<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Config
 */


namespace Aimeos\MW\Config\Decorator;


/**
 * Decorator interface for configuration setting classes
 *
 * @package MW
 * @subpackage Config
 */
interface Iface extends \Aimeos\MW\Config\Iface
{
	/**
	 * Initializes the decorator.
	 *
	 * @param \Aimeos\MW\Config\Iface $object Config object or decorator
	 * @return void
	 */
	public function __construct( \Aimeos\MW\Config\Iface $object );
}
