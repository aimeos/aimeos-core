<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MW
 * @subpackage View
 */


/**
 * View helper class for generating form parameter names.
 *
 * @package MW
 * @subpackage View
 */
class MW_View_Helper_FormParam_Default
	extends MW_View_Helper_Base
	implements MW_View_Helper_Iface
{
	private $names;


	/**
	 * Initializes the URL view helper.
	 *
	 * @param MW_View_Iface $view View instance with registered view helpers
	 * @param string[] $names Prefix names when generating form parameters (will be "name1[name2][name3]..." )
	 */
	public function __construct( $view, array $names = array() )
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
	 * @return string Form parameter name
	 */
	public function transform( $names )
	{
		$names = array_merge( $this->names, (array) $names );

		if( ( $result = array_shift( $names ) ) === null ) {
			return '';
		}

		foreach( $names as $name ) {
			$result .= '[' . $name . ']';
		}

		return $result;
	}
}