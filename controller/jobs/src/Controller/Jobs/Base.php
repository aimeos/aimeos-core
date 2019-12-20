<?php

/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package Controller
 * @subpackage Jobs
 */


namespace Aimeos\Controller\Jobs;


/**
 * Common methods for Jobs controller classes.
 *
 * @package Controller
 * @subpackage Jobs
 */
abstract class Base
{
	private $aimeos;
	private $context;


	/**
	 * Initializes the object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context MShop context object
	 * @param \Aimeos\Bootstrap $aimeos \Aimeos\Bootstrap main object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\Bootstrap $aimeos )
	{
		$this->context = $context;
		$this->aimeos = $aimeos;
	}


	/**
	 * Catch unknown methods
	 *
	 * @param string $name Name of the method
	 * @param array $param List of method parameter
	 * @throws \Aimeos\Controller\Jobs\Exception If method call failed
	 */
	public function __call( string $name, array $param )
	{
		throw new \Aimeos\Controller\Jobs\Exception( sprintf( 'Unable to call method "%1$s"', $name ) );
	}


	/**
	 * Returns the context object.
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface Context object
	 */
	protected function getContext() : \Aimeos\MShop\Context\Item\Iface
	{
		return $this->context;
	}


	/**
	 * Returns the \Aimeos\Bootstrap object.
	 *
	 * @return \Aimeos\Bootstrap \Aimeos\Bootstrap object
	 */
	protected function getAimeos() : \Aimeos\Bootstrap
	{
		return $this->aimeos;
	}


	/**
	 * Returns the value from the list or the default value
	 *
	 * @param array $list Associative list of key/value pairs
	 * @param string $key Key for the value to retrieve
	 * @param mixed $default Default value if key isn't found
	 * @return mixed Value for the key in the list or the default value
	 */
	protected function getValue( array $list, string $key, $default = null )
	{
		return isset( $list[$key] ) && ( $value = trim( $list[$key] ) ) !== '' ? $value : $default;
	}
}
