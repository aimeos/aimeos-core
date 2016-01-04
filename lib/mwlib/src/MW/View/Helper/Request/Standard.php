<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Request;


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
	private $body;
	private $clientaddr;
	private $target;
	private $files;


	/**
	 * Initializes the request view helper.
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance with registered view helpers
	 * @param string $body Request body content
	 * @param string $clientaddr Client IP address
	 */
	public function __construct( $view, $body = '', $clientaddr = '', $target = null, $files = array() )
	{
		parent::__construct( $view );

		$this->body = $body;
		$this->clientaddr = $clientaddr;
		$this->target = $target;
		$this->files = $files;
	}


	/**
	 * Returns the request view helper.
	 *
	 * @return \Aimeos\MW\View\Helper\Iface Request view helper
	 */
	public function transform()
	{
		return $this;
	}


	/**
	 * Returns the request body.
	 *
	 * @return string Request body
	 */
	public function getBody()
	{
		return $this->body;
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
	 * Retrieve normalized file upload data.
	 *
	 * This method returns upload metadata in a normalized tree, with each leaf
	 * an instance of Psr\Http\Message\UploadedFileInterface.
	 *
	 * These values MAY be prepared from $_FILES or the message body during
	 * instantiation, or MAY be injected via withUploadedFiles().
	 *
	 * @return array An array tree of UploadedFileInterface instances; an empty
	 *     array MUST be returned if no data is present.
	 */
	public function getUploadedFiles()
	{
		return $this->createUploadedFiles( $this->files );
	}


	/**
	 * Creates a normalized file upload data from the given array.
	 *
	 * @param array $files File upload data from $_FILES
	 * @return array Multi-dimensional list of file objects
	 */
	protected function createUploadedFiles( array $files )
	{
		$list = array();

		foreach( $files as $key => $value )
		{
			if( !isset( $value['tmp_name'] ) )
			{
				$list[$key] = $this->createUploadedFiles( $value );
				continue;
			}

			if( is_array( $value['tmp_name'] ) )
			{
				for( $i = 0; $i < count( $value['tmp_name'] ); $i++ )
				{
					$list[$key][] = new \Aimeos\MW\View\Helper\Request\File\Standard(
						$value['tmp_name'][$i],
						( isset( $value['name'][$i] ) ? $value['name'][$i] : '' ),
						( isset( $value['size'][$i] ) ? $value['size'][$i] : 0 ),
						( isset( $value['type'][$i] ) ? $value['type'][$i] : 'application/binary' ),
						( isset( $value['error'][$i] ) ? $value['error'][$i] : 0 )
					);
				}
			}
			else
			{
				$list[$key] = new \Aimeos\MW\View\Helper\Request\File\Standard(
					$value['tmp_name'],
					( isset( $value['name'] ) ? $value['name'] : '' ),
					( isset( $value['size'] ) ? $value['size'] : 0 ),
					( isset( $value['type'] ) ? $value['type'] : 'application/binary' ),
					( isset( $value['error'] ) ? $value['error'] : 0 )
				);
			}
		}

		return $list;
	}
}
