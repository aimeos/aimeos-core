<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Communication
 */


namespace Aimeos\MW\Communication;


/**
 * Common class for communication with delivery and payment providers.
 *
 * @package MW
 * @subpackage Communication
 */
class Curl implements \Aimeos\MW\Communication\Iface
{

	/**
	 * Sends request parameters to the providers interface.
	 *
	 * @param string $target Receivers address e.g. url.
	 * @param string $method Initial method (e.g. post or get)
	 * @param mixed $payload Update information whose format depends on the payment provider
	 * @return string response body of a http request
	 */
	public function transmit( $target, $method, $payload )
	{
		$response = '';

		if( ( $curl = curl_init() )=== false ) {
			throw new \Aimeos\MW\Communication\Exception( 'Could not initialize curl' );
		}

		try
		{
			curl_setopt( $curl, CURLOPT_URL, $target );

			curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, strtoupper( $method ) );
			curl_setopt( $curl, CURLOPT_POSTFIELDS, $payload );
			curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, 25 );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );   // return data as string

			curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, true );

			if ( ( $response = curl_exec( $curl ) ) === false ) {
				throw new \Aimeos\MW\Communication\Exception( sprintf( 'Sending order failed: "%1$s"', curl_error( $curl ) ) );
			}

			if ( curl_errno($curl) ) {
				throw new \Aimeos\MW\Communication\Exception( sprintf( 'Error with nr."%1$s" - "%2$s"', curl_errno($curl), curl_error($curl) ) );
			}

			curl_close( $curl );
		}
		catch( \Exception $e )
		{
			curl_close( $curl );
			throw $e;
		}

		return $response;
	}
}