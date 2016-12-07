<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Communication
 */


namespace Aimeos\MW\Communication;


/**
 * Common class for general SFTP transfer.
 *
 * @package MW
 * @subpackage Communication
 */
class Sftp implements \Aimeos\MW\Communication\Iface
{
	private $sftp;
	private $config;


	/**
	 * Initializes a new connection to the remote host with sftp.
	 *
	 * @param array $config Configuration with required connection datas
	 */
	public function __construct( array $config )
	{
		$this->config = $config;
		$this->sftp = new Net_SFTP( $config['host'] );

		if( $this->sftp->login( $config['username'], $config['password'] ) === false ) {
			throw new \Aimeos\MW\Communication\Exception( sprintf( 'Login to "%1$s" with user "%2$s" failed', $config['host'], $config['username'] ) );
		}
	}


	/**
	 * Disconnects from remote host.
	 */
	public function __destruct()
	{
		$this->sftp->disconnect();
	}


	/**
	 * Sends request parameters to the providers interface.
	 *
	 * @param string $target Target directory on the remote host
	 * @param string $method Transfer method like "file" or "string"
	 * @param mixed $payload Filename or string to transmit to remote host
	 */
	public function transmit( $target, $method, $payload )
	{
		switch( $method )
		{
			case 'file':
				$method = NET_SFTP_LOCAL_FILE; break;
			default:
				$method = NET_SFTP_STRING; break;
		}

		if ( $this->sftp->put( $target, $payload, $method ) === false )
		{
			$msg = sprintf( 'Could not upload payload to "%1$s:%2$s"', $this->config['host'], $target );
			throw new \Aimeos\MW\Communication\Exception( $msg );
		}
	}
}
