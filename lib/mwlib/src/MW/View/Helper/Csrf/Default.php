<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MW
 * @subpackage View
 */


/**
 * View helper class for retrieving CSRF tokens.
 *
 * @package MW
 * @subpackage View
 */
class MW_View_Helper_Csrf_Default
	extends MW_View_Helper_Abstract
	implements MW_View_Helper_Interface
{
	private $_name;
	private $_value;


	/**
	 * Initializes the URL view helper.
	 *
	 * @param MW_View_Interface $view View instance with registered view helpers
	 * @param string $name CSRF token name
	 * @param string $value CSRF token value
	 */
	public function __construct( $view, $name = '', $value = '' )
	{
		parent::__construct( $view );

		$this->_name = $name;
		$this->_value = $value;
	}


	/**
	 * Returns the CSRF partial object.
	 *
	 * @return MW_View_Helper_Interface CSRF partial object
	 */
	public function transform()
	{
		return $this;
	}


	/**
	 * Returns the CSRF token name.
	 *
	 * @return string CSRF token name
	 */
	public function name()
	{
		return $this->_name;
	}


	/**
	 * Returns the CSRF token value.
	 *
	 * @return string CSRF token value
	 */
	public function value()
	{
		return $this->_value;
	}


	/**
	 * Returns the HTML form field for the CSRF token.
	 *
	 * @return string HTML form field code
	 */
	public function formfield()
	{
		return '<input type="hidden" name="' . $this->_name . '" value="' . $this->_value . '" />';
	}
}
