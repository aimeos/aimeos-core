<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Request;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;


/**
 * View helper class for accessing request data.
 *
 * @package MW
 * @subpackage View
 */
class Standard
	extends \Aimeos\MW\View\Helper\Base
	implements \Aimeos\MW\View\Helper\Request\Iface
{
	private $request;
	private $clientaddr;
	private $target;


	/**
	 * Initializes the request view helper.
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance with registered view helpers
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param string $clientaddr Client IP address
	 * @param string $target Page ID or route name
	 */
	public function __construct( \Aimeos\MW\View\Iface $view, \Psr\Http\Message\ServerRequestInterface $request, $clientaddr = '', $target = null )
	{
		parent::__construct( $view );

		$this->request = $request;
		$this->clientaddr = $clientaddr;
		$this->target = $target;
	}


	/**
	 * Returns the request view helper.
	 *
	 * @return \Aimeos\MW\View\Helper\Request\Iface Request view helper
	 */
	public function transform()
	{
		return $this;
	}


	/**
	 * Returns the client IP address.
	 *
	 * @return string Client IP address
	 */
	public function getClientAddress()
	{
		return $this->clientaddr;
	}


	/**
	 * Returns the current page or route name
	 *
	 * @return string|null Current page or route name
	 */
	public function getTarget()
	{
		return $this->target;
	}


	/**
	 * Retrieves the HTTP protocol version as a string.
	 *
	 * {@inheritDoc}
	 *
	 * @return string HTTP protocol version.
	 */
	public function getProtocolVersion()
	{
		return $this->request->getProtocolVersion();
	}


	/**
	 * Return an instance with the specified HTTP protocol version.
	 *
	 * {@inheritDoc}
	 *
	 * @param string $version HTTP protocol version
	 * @return self
	*/
	public function withProtocolVersion( $version )
	{
		$this->request = $this->request->withProtocolVersion( $version );
		return $this;
	}


	/**
	 * Retrieves all message header values.
	 *
	 * {@inheritDoc}
	 *
	 * @return string[][] Returns an associative array of the message's headers.
	 *	 Each key MUST be a header name, and each value MUST be an array of
	 *	 strings for that header.
	*/
	public function getHeaders()
	{
		return $this->request->getHeaders();
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
		return $this->request->hasHeader( $name );
	}


	/**
	 * Retrieves a message header value by the given case-insensitive name.
	 *
	 * {@inheritDoc}
	 *
	 * @param string $name Case-insensitive header field name.
	 * @return string[] An array of string values as provided for the given
	 *	header. If the header does not appear in the message, this method MUST
	 *	return an empty array.
	 */
	public function getHeader( $name )
	{
		return $this->request->getHeader( $name );
	}


	/**
	 * Retrieves a comma-separated string of the values for a single header.
	 *
	 * {@inheritDoc}
	 *
	 * @param string $name Case-insensitive header field name.
	 * @return string A string of values as provided for the given header
	 *	concatenated together using a comma. If the header does not appear in
	 *	the message, this method MUST return an empty string.
	 */
	public function getHeaderLine( $name )
	{
		return $this->request->getHeaderLine( $name );
	}


	/**
	 * Return an instance with the provided value replacing the specified header.
	 *
	 * {@inheritDoc}
	 *
	 * @param string $name Case-insensitive header field name.
	 * @param string|string[] $value Header value(s).
	 * @return self
	 * @throws \InvalidArgumentException for invalid header names or values.
	 */
	public function withHeader( $name, $value )
	{
		$this->request = $this->request->withHeader( $name, $value );
		return $this;
	}


	/**
	 * Return an instance with the specified header appended with the given value.
	 *
	 * {@inheritDoc}
	 *
	 * @param string $name Case-insensitive header field name to add.
	 * @param string|string[] $value Header value(s).
	 * @return self
	 */
	public function withAddedHeader( $name, $value )
	{
		$this->request = $this->request->withAddedHeader( $name, $value );
		return $this;
	}


	/**
	 * Return an instance without the specified header.
	 *
	 * {@inheritDoc}
	 *
	 * @param string $name Case-insensitive header field name to remove.
	 * @return self
	 */
	public function withoutHeader( $name )
	{
		$this->request = $this->request->withoutHeader( $name );
		return $this;
	}


	/**
	 * Gets the body of the message.
	 *
	 * @return StreamInterface Returns the body as a stream.
	 */
	public function getBody()
	{
		return $this->request->getBody();
	}


	/**
	 * Return an instance with the specified message body.
	 *
	 * {@inheritDoc}
	 *
	 * @param StreamInterface $body Body.
	 * @return self
	 * @throws \InvalidArgumentException When the body is not valid.
	 */
	public function withBody( StreamInterface $body )
	{
		$this->request = $this->request->withBody( $body );
		return $this;
	}


	/**
	 * Retrieves the message's request target.
	 *
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function getRequestTarget()
	{
		return $this->request->getRequestTarget();
	}


	/**
	 * Return an instance with the specific request-target.
	 *
	 * {@inheritDoc}
	 *
	 * @see http://tools.ietf.org/html/rfc7230#section-2.7 (for the various
	 *	 request-target forms allowed in request messages)
	 * @param mixed $requestTarget
	 * @return self
	*/
	public function withRequestTarget( $requestTarget )
	{
		$this->request = $this->request->withRequestTarget( $requestTarget );
		return $this;
	}


	/**
	 * Retrieves the HTTP method of the request.
	 *
	 * @return string Returns the request method.
	*/
	public function getMethod()
	{
		return $this->request->getMethod();
	}


	/**
	 * Return an instance with the provided HTTP method.
	 *
	 * {@inheritDoc}
	 *
	 * @param string $method Case-sensitive method.
	 * @return self
	 * @throws \InvalidArgumentException for invalid HTTP methods.
	*/
	public function withMethod( $method )
	{
		$this->request = $this->request->withMethod( $method );
		return $this;
	}


	/**
	 * Retrieves the URI instance.
	 *
	 * {@inheritDoc}
	 *
	 * @see http://tools.ietf.org/html/rfc3986#section-4.3
	 * @return UriInterface Returns a UriInterface instance
	 *	 representing the URI of the request.
	*/
	public function getUri()
	{
		return $this->request->getUri();
	}


	/**
	 * Returns an instance with the provided URI.
	 *
	 * {@inheritDoc}
	 *
	 * @param UriInterface $uri New request URI to use.
	 * @param bool $preserveHost Preserve the original state of the Host header.
	 * @return self
	*/
	public function withUri( UriInterface $uri, $preserveHost = false )
	{
		$this->request = $this->request->withUri( $uri, $preserveHost );
		return $this;
	}


	/**
	 * Retrieve server parameters.
	 *
	 * {@inheritDoc}
	 *
	 * @return array List of key/value pairs from $_SERVER
	*/
	public function getServerParams()
	{
		return $this->request->getServerParams();
	}


	/**
	 * Retrieve cookies.
	 *
	 * {@inheritDoc}
	 *
	 * @return array List of key/value pairs from $_SERVER
	 */
	public function getCookieParams()
	{
		return $this->request->getCookieParams();
	}


	/**
	 * Return an instance with the specified cookies.
	 *
	 * {@inheritDoc}
	 *
	 * @param array $cookies Array of key/value pairs representing cookies.
	 * @return self
	 */
	public function withCookieParams( array $cookies )
	{
		$this->request = $this->request->withCookieParams( $cookies );
		return $this;
	}


	/**
	 * Retrieve query string arguments.
	 *
	 * {@inheritDoc}
	 *
	 * @return array
	 */
	public function getQueryParams()
	{
		return $this->request->getQueryParams();
	}


	/**
	 * Return an instance with the specified query string arguments.
	 *
	 * {@inheritDoc}
	 *
	 * @param array $query Array of query string arguments, typically from $_GET.
	 * @return self
	 */
	public function withQueryParams( array $query )
	{
		$this->request = $this->request->withQueryParams( $query );
		return $this;
	}


	/**
	 * Retrieve normalized file upload data.
	 *
	 * {@inheritDoc}
	 *
	 * @return array An array tree of UploadedFileInterface instances; an empty
	 *	 array MUST be returned if no data is present.
	 */
	public function getUploadedFiles()
	{
		return $this->request->getUploadedFiles();
	}


	/**
	 * Create a new instance with the specified uploaded files.
	 *
	 * {@inheritDoc}
	 *
	 * @param array An array tree of UploadedFileInterface instances.
	 * @return self
	 */
	public function withUploadedFiles( array $uploadedFiles )
	{
		$this->request = $this->request->withUploadedFiles( $uploadedFiles );
		return $this;
	}


	/**
	 * Retrieve any parameters provided in the request body.
	 *
	 * {@inheritDoc}
	 *
	 * @return null|array|object The deserialized body parameters, if any.
	 *	 These will typically be an array or object.
	 */
	public function getParsedBody()
	{
		return $this->request->getParsedBody();
	}


	/**
	 * Return an instance with the specified body parameters.
	 *
	 * {@inheritDoc}
	 *
	 * @param null|array|object $data The deserialized body data. This will
	 *	 typically be in an array or object.
	 * @return self
	 * @throws \InvalidArgumentException if an unsupported argument type is
	 *	 provided.
	 */
	public function withParsedBody( $data )
	{
		$this->request = $this->request->withParsedBody( $data );
		return $this;
	}


	/**
	 * Retrieve attributes derived from the request.
	 *
	 * {@inheritDoc}
	 *
	 * @return mixed[] Attributes derived from the request.
	 */
	public function getAttributes()
	{
		return $this->request->getAttributes();
	}


	/**
	 * Retrieve a single derived request attribute.
	 *
	 * {@inheritDoc}
	 *
	 * @param string $name The attribute name.
	 * @param mixed $default Default value to return if the attribute does not exist.
	 * @return mixed
	 */
	public function getAttribute( $name, $default = null )
	{
		return $this->request->getAttribute( $name, $default );
	}


	/**
	 * Return an instance with the specified derived request attribute.
	 *
	 * {@inheritDoc}
	 *
	 * @param string $name The attribute name.
	 * @param mixed $value The value of the attribute.
	 * @return self
	 */
	public function withAttribute( $name, $value )
	{
		$this->request = $this->request->withAttribute( $name, $value );
		return $this;
	}


	/**
	 * Return an instance that removes the specified derived request attribute.
	 *
	 * {@inheritDoc}
	 *
	 * @param string $name The attribute name.
	 * @return self
	 */
	public function withoutAttribute( $name )
	{
		$this->request = $this->request->withoutAttribute( $name );
		return $this;
	}
}
