<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Response;


/**
 * View helper class for setting response data.
 *
 * @package MW
 * @subpackage View
 */
interface Iface extends \Aimeos\MW\View\Helper\Iface, \Psr\Http\Message\ResponseInterface
{
	/**
	 * Returns the request view helper.
	 *
	 * @return \Aimeos\MW\View\Helper\Response\Iface Response view helper
	 */
	public function transform();

	/**
	 * Creates a new PSR-7 stream object
	 *
	 * @param string|resource $resource Absolute file path or file descriptor
	 * @return \Psr\Http\Message\StreamInterface Stream object
	 */
	public function createStream( $resource );

	/**
	 * Creates a new PSR-7 stream object from a string
	 *
	 * @param string $content Content as string
	 * @return \Psr\Http\Message\StreamInterface Stream object
	 */
	public function createStreamFromString( $content );
}
