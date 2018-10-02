<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2018
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
	 * @return boolean True if available, false if not
	 */
	public function isAvailable()
	{
		return $this->object->isAvailable();
	}


	/**
	 * Starts a new task by executing the given anonymous function
	 *
	 * @param \Closure $fcn Anonymous function to execute
	 * @param array $data List of parameters that is passed to the closure function
	 * @param boolean $restart True if the task should be restarted if it fails (only once)
	 * @return \Aimeos\MW\Process\Iface Self object for method chaining
	 * @throws \Aimeos\MW\Process\Exception If starting the new task failed
	 */
	public function start( \Closure $fcn, array $data, $restart = false )
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
	public function wait()
	{
		if( $this->object->isAvailable() === true ) {
			$this->object->wait();
		}

		return $this;
	}
}