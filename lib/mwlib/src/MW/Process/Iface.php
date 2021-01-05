<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
 * @package MW
 * @subpackage Process
 */


namespace Aimeos\MW\Process;


/**
 * Common interface for parallel processing classes
 *
 * @package MW
 * @subpackage Process
 */
interface Iface
{
	/**
	 * Checks if processing tasks in parallel is available
	 *
	 * @return bool True if available, false if not
	 */
	public function isAvailable() : bool;

	/**
	 * Starts a new task by executing the given anonymous function
	 *
	 * @param \Closure $fcn Anonymous function to execute
	 * @param array $data List of parameters that is passed to the closure function
	 * @param bool $restart True if the task should be restarted if it fails (only once)
	 * @return \Aimeos\MW\Process\Iface Self object for method chaining
	 */
	public function start( \Closure $fcn, array $data, bool $restart = false ) : Iface;

	/**
	 * Waits for the running tasks until all have finished
	 *
	 * @return \Aimeos\MW\Process\Iface Self object for method chaining
	 */
	public function wait() : Iface;
}
