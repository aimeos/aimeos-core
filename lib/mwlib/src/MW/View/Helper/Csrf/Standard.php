<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Csrf;


/**
 * View helper class for retrieving CSRF tokens.
 *
 * @package MW
 * @subpackage View
 */
class Standard
	extends \Aimeos\MW\View\Helper\Base
	implements \Aimeos\MW\View\Helper\Csrf\Iface
{
	private $name;
	private $value;
	private $formfield = '';


	/**
	 * Initializes the URL view helper.
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance with registered view helpers
	 * @param string $name CSRF token name
	 * @param string|null $value CSRF token value
	 */
	public function __construct( \Aimeos\MW\View\Iface $view, string $name = '', string $value = null )
	{
		parent::__construct( $view );

		$this->name = $name;
		$this->value = $value;

		if( $value ) {
			$this->formfield = '<input class="csrf-token" type="hidden" name="' . $this->name . '" value="' . $this->value . '" />';
		}
	}


	/**
	 * Returns the CSRF partial object.
	 *
	 * @return \Aimeos\MW\View\Helper\Csrf\Iface CSRF partial object
	 */
	public function transform() : Iface
	{
		return $this;
	}


	/**
	 * Returns the CSRF token name.
	 *
	 * @return string CSRF token name
	 */
	public function name() : string
	{
		return $this->name;
	}


	/**
	 * Returns the CSRF token value.
	 *
	 * @return string|null CSRF token value
	 */
	public function value() : ?string
	{
		return $this->value;
	}


	/**
	 * Returns the HTML form field for the CSRF token.
	 *
	 * @return string HTML form field code
	 */
	public function formfield() : string
	{
		return $this->formfield;
	}
}
