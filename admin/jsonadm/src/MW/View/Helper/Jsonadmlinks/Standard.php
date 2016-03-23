<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Jsonadmlinks;


/**
 * View helper class for generating the links for JSON API
 *
 * @package MW
 * @subpackage View
 */
class Standard
	extends \Aimeos\MW\View\Helper\Base
	implements \Aimeos\MW\View\Helper\Iface
{
	private $target;
	private $cntl;
	private $action;
	private $config;
	private $maxlimit;


	/**
	 * Initializes the links view helper
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance with registered view helpers
	 */
	public function __construct( \Aimeos\MW\View\Iface $view )
	{
		parent::__construct( $view );

		$this->target = $this->config( 'admin/jsonadm/url/target' );
		$this->cntl = $this->config( 'admin/jsonadm/url/controller', 'jsonadm' );
		$this->action = $this->config( 'admin/jsonadm/url/action', 'get' );
		$this->config = $this->config( 'admin/jsonadm/url/config', array() );
		$this->maxlimit = $this->config( 'admin/jsonadm/limit', 100 );
	}


	/**
	 * Returns the list of pagination links
	 *
	 * @param array $params Associative list of parameters
	 * @param integer $total Total number of items
	 * @return array List of pagination links
	 */
	public function transform( array $params, $total )
	{
		$ref = array( 'id', 'resource', 'filter', 'page', 'sort', 'include', 'fields' );
		$params = array_intersect_key( $params, array_flip( $ref ) );

		// set explicitly as workaround for the Laravel router bugs
		if( !isset( $params['id'] ) ) {
			$params['id'] = '';
		}

		$total = $this->get( 'total', 0 );
		$offset = ( isset( $params['offset'] ) ? max( (int) $params['offset'], 0 ) : 0 );
		$limit = ( isset( $params['limit'] ) ? max( (int) $params['limit'], 1 ) : $this->maxlimit );

		if( $offset > 0 )
		{
			$params['page']['offset'] = 0;
			$list['first'] = $this->url( $this->target, $this->cntl, $this->action, $params, array(), $this->config );
		}

		if( ( $num = $offset - $limit ) >= 0 )
		{
			$params['page']['offset'] = $num;
			$list['prev'] = $this->url( $this->target, $this->cntl, $this->action, $params, array(), $this->config );
		}

		if( ( $num = $offset + $limit ) < $total )
		{
			$params['page']['offset'] = $num;
			$list['next'] = $this->url( $this->target, $this->cntl, $this->action, $params, array(), $this->config );
		}

		if( ( $num = ((int) ($total / $limit)) * $limit ) > $offset )
		{
			$params['page']['offset'] = $num;
			$list['last'] = $this->url( $this->target, $this->cntl, $this->action, $params, array(), $this->config );
		}

		$params['page']['offset'] = $offset;
		$list['self'] = $this->url( $this->target, $this->cntl, $this->action, $params, array(), $this->config );

		return $list;
	}
}
