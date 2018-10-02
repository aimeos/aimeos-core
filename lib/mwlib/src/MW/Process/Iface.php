<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2018
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
	 * @return boolean True if available, false if not
	 */
	public function isAvailable();

	/**
	 * Starts a new task by executing the given anonymous function
	 *
	 * @param \Closure $fcn Anonymous function to execute
	 * @param array $data List of parameters that is passed to the closure function
	 * @param boolean $restart True if the task should be restarted if it fails (only once)
	 * @return \Aimeos\MW\Process\Iface Self object for method chaining
	 */
	public function start( \Closure $fcn, array $data, $restart = false );

	/**
	 * Waits for the running tasks until all have finished
	 *
	 * @return \Aimeos\MW\Process\Iface Self object for method chaining
	 */
	public function wait();
}