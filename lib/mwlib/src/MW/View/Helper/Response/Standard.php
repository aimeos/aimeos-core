<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Response;

use Psr\Http\Message\StreamInterface;


/**
 * View helper class for setting response data.
 *
 * @package MW
 * @subpackage View
 */
class Standard
	extends \Aimeos\MW\View\Helper\Base
	implements \Aimeos\MW\View\Helper\Response\Iface
{
	private $response;


	/**
	 * Initializes the request view helper.
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance with registered view helpers
	 * @param \Psr\Http\Message\ResponseInterface $response Response object
	 */
	public function __construct( \Aimeos\MW\View\Iface $view, \Psr\Http\Message\ResponseInterface $response )
	{
		parent::__construct( $view );

		$this->response = $response;
	}


	/**
	 * Returns the request view helper.
	 *
	 * @return \Aimeos\MW\View\Helper\Response\Iface Response view helper
	 */
	public function transform() : Iface
	{
		return $this;
	}


	/**
	 * Creates a new PSR-7 stream object
	 *
	 * @param string|resource $resource Absolute file path or file descriptor
	 * @return \Psr\Http\Message\StreamInterface Stream object
	 */
	public function createStream( $resource ) : \Psr\Http\Message\StreamInterface
	{
		$psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();

		if( is_resource( $resource ) ) {
			return $psr17Factory->createStreamFromResource( $resource );
		}

		return $psr17Factory->createStreamFromFile( $resource );
	}


	/**
	 * Creates a new PSR-7 stream object from a content string
	 *
	 * @param string $content Content as string
	 * @return \Psr\Http\Message\StreamInterface Stream object
	 */
	public function createStreamFromString( string $content ) : \Psr\Http\Message\StreamInterface
	{
		$psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();
		return $psr17Factory->createStream( $content );
	}


	/**
	 * Retrieves the HTTP protocol version as a string.
	 *
	 * @return string HTTP protocol version.
	 */
	public function getProtocolVersion()
	{
		return $this->response->getProtocolVersion();
	}


	/**
	 * Return an instance with the specified HTTP protocol version.
	 *
	 * @param string $version HTTP protocol version
	 * @return self
	 */
	public function withProtocolVersion( $version )
	{
		$this->response = $this->response->withProtocolVersion( $version );
		return $this;
	}


	/**
	 * Retrieves all message header values.
	 *
	 * @return string[][] Returns an associative array of the message's headers.
	 *	 Each key MUST be a header name, and each value MUST be an array of
	 *	 strings for that header.
	 */
	public function getHeaders()
	{
		return $this->response->getHeaders();
	}


	/**
	 * Checks if a header exists by the given case-insensitive name.
	 *
	 * @param string $name Case-insensitive header field name.
	 * @return bool Returns true if any header names match the given header
	 *	 name using a case-insensitive string comparison. Returns false if
	 *	 no matching header name is found in the message.
	 */
	public function hasHeader( $name )
	{
		return $this->response->hasHeader( $name );
	}


	/**
	 * Retrieves a message header value by the given case-insensitive name.
	 *
	 * @param string $name Case-insensitive header field name.
	 * @return string[] An array of string values as provided for the given
	 *	header. If the header does not appear in the message, this method MUST
	 *	return an empty array.
	 */
	public function getHeader( $name )
	{
		return $this->response->getHeader( $name );
	}


	/**
	 * Retrieves a comma-separated string of the values for a single header.
	 *
	 * @param string $name Case-insensitive header field name.
	 * @return string A string of values as provided for the given header
	 *	concatenated together using a comma. If the header does not appear in
	 *	the message, this method MUST return an empty string.
	 */
	public function getHeaderLine( $name )
	{
		return $this->response->getHeaderLine( $name );
	}


	/**
	 * Return an instance with the provided value replacing the specified header.
	 *
	 * @param string $name Case-insensitive header field name.
	 * @param string|string[] $value Header value(s).
	 * @return self
	 * @throws \InvalidArgumentException for invalid header names or values.
	 */
	public function withHeader( $name, $value )
	{
		$this->response = $this->response->withHeader( $name, $value );
		return $this;
	}


	/**
	 * Return an instance with the specified header appended with the given value.
	 *
	 * @param string $name Case-insensitive header field name to add.
	 * @param string|string[] $value Header value(s).
	 * @return self
	 */
	public function withAddedHeader( $name, $value )
	{
		$this->response = $this->response->withAddedHeader( $name, $value );
		return $this;
	}


	/**
	 * Return an instance without the specified header.
	 *
	 * @param string $name Case-insensitive header field name to remove.
	 * @return self
	 */
	public function withoutHeader( $name )
	{
		$this->response = $this->response->withoutHeader( $name );
		return $this;
	}


	/**
	 * Gets the body of the message.
	 *
	 * @return StreamInterface Returns the body as a stream.
	 */
	public function getBody()
	{
		return $this->response->getBody();
	}


	/**
	 * Return an instance with the specified message body.
	 *
	 * @param StreamInterface $body Body.
	 * @return self
	 * @throws \InvalidArgumentException When the body is not valid.
	 */
	public function withBody( StreamInterface $body )
	{
		$this->response = $this->response->withBody( $body );
		return $this;
	}


	/**
	 * Gets the response status code.
	 *
	 * @return int Status code.
	 */
	public function getStatusCode()
	{
		return $this->response->getStatusCode();
	}


	/**
	 * Return an instance with the specified status code and, optionally, reason phrase.
	 *
	 * @param int $code The 3-digit integer result code to set.
	 * @param string $reasonPhrase The reason phrase to use with the
	 *	 provided status code; if none is provided, implementations MAY
	 *	 use the defaults as suggested in the HTTP specification.
	 * @return self
	 * @throws \InvalidArgumentException For invalid status code arguments.
	 */
	public function withStatus( $code, $reasonPhrase = '' )
	{
		$this->response = $this->response->withStatus( $code, $reasonPhrase );
		return $this;
	}


	/**
	 * Gets the response reason phrase associated with the status code.
	 *
	 * @return string Reason phrase; must return an empty string if none present.
	 */
	public function getReasonPhrase()
	{
		return $this->response->getReasonPhrase();
	}
}
