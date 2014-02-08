<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MW
 * @subpackage View
 */


/**
 * Common abstract class for all view helper classes.
 *
 * @package MW
 * @subpackage View
 */
abstract class MW_View_Helper_Abstract
{
	private $_view;


	/**
	 * Initializes the view helper classes.
	 *
	 * @param MW_View_Interface $view View instance with registered view helpers
	 */
	public function __construct( MW_View_Interface $view )
	{
		$this->_view = $view;
	}


	/**
	 * Calls the view helper with the given name and arguments and returns it's output.
	 *
	 * @param string $name Name of the view helper
	 * @param array $args Arguments passed to the view helper
	 * @return mixed Output depending on the view helper
	 */
	public function __call( $name, array $args )
	{
		return call_user_func_array( array( $this->_view, $name ), $args );
	}


	/**
	 * Returns the view object.
	 *
	 * @return MW_View_Interface View object
	 */
	protected function _getView()
	{
		return $this->_view;
	}
}