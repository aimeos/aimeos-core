<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Access;


/**
 * View helper class for checking access levels
 *
 * @package MW
 * @subpackage View
 */
class Standard extends \Aimeos\MW\View\Helper\Base implements Iface
{
	private $groups;


	/**
	 * Initializes the view helper
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @param \Closure|array $groups Group codes assigned to the current user or closure function that returns the list
	 */
	public function __construct( \Aimeos\MW\View\Iface $view, $groups )
	{
		parent::__construct( $view );
		$this->groups = $groups;
	}


	/**
	 * Checks the access level of the current user
	 *
	 * @param string|array $groups Group names that are allowed
	 * @return boolean True if access is allowed, false if not
	 */
	public function transform( $groups )
	{
		if( is_callable( $this->groups ) )
		{
			$fcn = $this->groups;
			$this->groups = $fcn();
		}

		return (bool) count( array_intersect( (array) $groups, (array) $this->groups ) );
	}
}
