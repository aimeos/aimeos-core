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
	private $baseurl;
	private $enc;


	/**
	 * Initializes the content view helper.
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance with registered view helpers
	 * @param string|null $baseurl Base URL for the content
	 */
	public function __construct( \Aimeos\MW\View\Iface $view, string $baseurl = null )
	{
		parent::__construct( $view );

		if( $baseurl === null ) {
			$baseurl = $view->config( 'resource/fs/baseurl' );
		}

		$this->baseurl = rtrim( $baseurl, '/' );
		$this->enc = $view->encoder();
	}


	/**
	 * Returns the complete encoded content URL.
	 *
	 * @param string|null $url Absolute, relative or data: URL
	 * @return string Complete encoded content URL
	 */
	public function transform( ?string $url ) : string
	{
		if( $url && !\Aimeos\MW\Str::starts( $url, ['http', 'data:', '/'] ) ) {
			$url = $this->baseurl . '/' . $url;
		}

		return $this->enc->attr( $url );
	}
}
