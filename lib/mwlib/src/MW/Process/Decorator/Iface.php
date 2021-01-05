<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
 * @package MW
 * @subpackage Process
 */


namespace Aimeos\MW\Process\Decorator;


/**
 * Common interface for parallel processing decorators
 *
 * @package MW
 * @subpackage Process
 */
interface Iface extends \Aimeos\MW\Process\Iface
{
	/**
	 * Initializes the object
	 *
	 * @param \Aimeos\MW\Process\Iface $object Parallel processing object
	 * @return void
	 */
	public function __construct( \Aimeos\MW\Process\Iface $object );
}
