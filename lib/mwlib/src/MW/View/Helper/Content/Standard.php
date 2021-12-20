<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Content;


/**
 * View helper class for generating media URLs
 *
 * @package MW
 * @subpackage View
 */
class Standard
	extends \Aimeos\MW\View\Helper\Base
	implements \Aimeos\MW\View\Helper\Content\Iface
{
	private $baseurls = [];
	private $enc;


	/**
	 * Initializes the content view helper.
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance with registered view helpers
	 */
	public function __construct( \Aimeos\MW\View\Iface $view )
	{
		parent::__construct( $view );
		$this->enc = $view->encoder();
	}


	/**
	 * Returns the complete encoded content URL.
	 *
	 * @param string|null $url Absolute, relative or data: URL
	 * @param string $fsname File system name the file is stored at
	 * @return string Complete encoded content URL
	 */
	public function transform( ?string $url, $fsname = 'fs-media' ) : string
	{
		if( $url && !\Aimeos\MW\Str::starts( $url, ['http', 'data:', '/'] ) ) {
			$url = $this->baseurl( $fsname ) . '/' . $url;
		}

		return $this->enc->attr( $url );
	}


	/**
	 * Returns the base URL for the given file system.
	 *
	 * @param string $fsname File system name
	 * @return string Base URL of the file system
	 */
	protected function baseurl( string $fsname ) : string
	{
		if( !isset( $this->baseurls[$fsname] ) ) {
			$this->baseurls[$fsname] = rtrim( $this->view()->config( 'resource/' . $fsname . '/baseurl', '' ), '/' );
		}

		return $this->baseurls[$fsname];
	}
}
