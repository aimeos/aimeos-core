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
	 * @param string $target Page ID or route name
	 * @param \Traversable|array $files Uploaded files
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
		$list = array();

		foreach( $this->files as $name => $array ) {
			$list[$name] = $this->createUploadedFiles( $array );
		}

		return $list;
	}


	/**
	 * Creates a normalized file upload data from the given array.
	 *
	 * @param array $list File upload data from $_FILES with name/tmp_name/error/type/size as keys
	 * @return array|Psr\Http\Message\UploadedFileInterface Single file object or multi-dimensional list of file objects
	 */
	protected function createUploadedFiles( $list )
	{
		$result = array();

		if( !isset( $list['tmp_name'] ) || !isset( $list['error'] ) || $list['error'] === UPLOAD_ERR_NO_FILE ) {
			return array();
		}

		if( is_array( $list['tmp_name'] ) )
		{
			foreach( $list['tmp_name'] as $key => $value )
			{
				$temp = array(
					'tmp_name' => $value,
					'name' => $list['name'][$key],
					'type' => $list['type'][$key],
					'size' => $list['size'][$key],
					'error' => $list['error'][$key],
				);

				$result[$key] = $this->createUploadedFiles( $temp );
			}
		}
		else
		{
			$this->checkUploadedFile( $list['tmp_name'] );

			$result = new \Aimeos\MW\View\Helper\Request\File\Standard(
				$list['tmp_name'],
				( isset( $list['name'] ) ? $list['name'] : '' ),
				( isset( $list['size'] ) ? $list['size'] : 0 ),
				( isset( $list['type'] ) ? $list['type'] : 'application/octet-stream' ),
				( isset( $list['error'] ) ? $list['error'] : 0 )
			);
		}

		return $result;
	}


	/**
	 * Checks if the file was uploaded
	 *
	 * @param string $path Path to the file
	 * @throws \Aimeos\MW\View\Exception If file wasn't uploaded and this can lead to a security issue
	 */
	protected function checkUploadedFile( $path )
	{
		if( is_uploaded_file( $path ) === false ) {
			throw new \Aimeos\MW\View\Exception( sprintf( 'File "%1$s" was not uploaded', $path ) );
		}
	}
}
