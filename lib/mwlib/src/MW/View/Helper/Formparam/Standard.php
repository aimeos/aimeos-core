<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Formparam;


/**
 * View helper class for generating form parameter names.
 *
 * @package MW
 * @subpackage View
 */
class Standard
	extends \Aimeos\MW\View\Helper\Base
	implements \Aimeos\MW\View\Helper\Formparam\Iface
{
	private $names;


	/**
	 * Initializes the URL view helper.
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance with registered view helpers
	 * @param string[] $names Prefix names when generating form parameters (will be "name1[name2][name3]..." )
	 */
	public function __construct( \Aimeos\MW\View\Iface $view, array $names = [] )
	{
		parent::__construct( $view );

		$this->names = $names;
	}


	/**
	 * Returns the name of the form parameter.
	 * The result is a string that allows parameters to be passed as arrays if
	 * this is necessary, e.g. "name1[name2][name3]..."
	 *
	 * @param string|array $names Name or list of names
	 * @param bool $prefix TRUE to use available prefix, FALSE for names without prefix
	 * @return string Form parameter name
	 */
	public function transform( $names, bool $prefix = true ) : string
	{
		$names = array_merge( $prefix ? $this->names : [], (array) $names );

		if( ( $result = array_shift( $names ) ) === null ) {
			return '';
		}

		foreach( $names as $name ) {
			$result .= '[' . $name . ']';
		}

		return $result;
	}
}
