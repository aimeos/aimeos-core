<?php

/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Jobs
 */


namespace Aimeos\Controller\Jobs\Common\Decorator;


/**
 * Provides common methods for controller decorators.
 *
 * @package Controller
 * @subpackage Jobs
 */
abstract class Base
	implements \Aimeos\Controller\Jobs\Common\Decorator\Iface
{
	private $context;
	private $aimeos;
	private $controller;


	/**
	 * Initializes the controller decorator.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @param \Aimeos\Controller\Jobs\Iface $controller Controller object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\Bootstrap $aimeos,
		\Aimeos\Controller\Jobs\Iface $controller )
	{
		$this->context = $context;
		$this->aimeos = $aimeos;
		$this->controller = $controller;
	}


	/**
	 * Passes unknown methods to wrapped objects.
	 *
	 * @param string $name Name of the method
	 * @param array $param List of method parameter
	 * @return mixed Returns the value of the called method
	 * @throws \Aimeos\Controller\Jobs\Exception If method call failed
	 */
	public function __call( $name, array $param )
	{
		if( ( $result = call_user_func_array( array( $this->controller, $name ), $param ) ) === false ) {
			throw new \Aimeos\Controller\Jobs\Exception( sprintf( 'Unable to call method "%1$s"', $name ) );
		}

		return $result;
	}


	/**
	 * Returns the localized name of the job.
	 *
	 * @return string Name of the job
	 */
	public function getName()
	{
		return $this->controller->getName();
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->controller->getDescription();
	}


	/**
	 * Executes the job.
	 *
	 * @throws \Aimeos\Controller\Jobs\Exception If an error occurs
	 */
	public function run()
	{
		$this->controller->run();
	}


	/**
	 * Returns the context object.
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface context object implementing \Aimeos\MShop\Context\Item\Iface
	 */
	protected function getContext()
	{
		return $this->context;
	}


	/**
	 * Returns the \Aimeos\Bootstrap object.
	 *
	 * @return \Aimeos\Bootstrap \Aimeos\Bootstrap object
	 */
	protected function getAimeos()
	{
		return $this->aimeos;
	}
}