<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
 * @package MW
 * @subpackage Process
 */


namespace Aimeos\MW\Process\Decorator;


/**
 * Check avaiability of parallel processing
 *
 * If not available, execute the tasks one after another
 *
 * @package MW
 * @subpackage Process
 */
class Check implements Iface
{
	private $object;


	/**
	 * Initializes the object
	 *
	 * @param \Aimeos\MW\Process\Iface $object Parallel processing object
	 */
	public function __construct( \Aimeos\MW\Process\Iface $object )
	{
		$this->object = $object;
	}


	/**
	 * Checks if processing tasks in parallel is available
	 *
	 * @return bool True if available, false if not
	 */
	public function isAvailable() : bool
	{
		return $this->object->isAvailable();
	}


	/**
	 * Starts a new task by executing the given anonymous function
	 *
	 * @param \Closure $fcn Anonymous function to execute
	 * @param array $data List of parameters that is passed to the closure function
	 * @param bool $restart True if the task should be restarted if it fails (only once)
	 * @return \Aimeos\MW\Process\Iface Self object for method chaining
	 * @throws \Aimeos\MW\Process\Exception If starting the new task failed
	 */
	public function start( \Closure $fcn, array $data, bool $restart = false ) : \Aimeos\MW\Process\Iface
	{
		if( $this->object->isAvailable() === true ) {
			$this->object->start( $fcn, $data, $restart );
		} else {
			call_user_func_array( $fcn, $data );
		}

		return $this;
	}


	/**
	 * Waits for the running tasks until all have finished
	 *
	 * @return \Aimeos\MW\Process\Iface Self object for method chaining
	 */
	public function wait() : \Aimeos\MW\Process\Iface
	{
		if( $this->object->isAvailable() === true ) {
			$this->object->wait();
		}

		return $this;
	}
}
