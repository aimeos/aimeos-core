<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MW
 * @subpackage View
 */


/**
 * View helper class for translating strings.
 *
 * @package MW
 * @subpackage View
 */
class MW_View_Helper_Translate_Default
	extends MW_View_Helper_Abstract
	implements MW_View_Helper_Interface
{
	private $_translator;


	/**
	 * Initializes the translator view helper.
	 *
	 * @param MW_View_Interface $view View instance with registered view helpers
	 * @param MW_Translation_Interface $translator Translation object
	 */
	public function __construct( MW_View_Interface $view, MW_Translation_Interface $translator )
	{
		parent::__construct( $view );

		$this->_translator = $translator;
	}


	/**
	 * Returns the translated string or the original one if no translation is available.
	 *
	 * @param string $domain Translation domain from core or an extension
	 * @param string $singular Singular form of the text to translate
	 * @param string $plural Plural form of the text, used if $number is greater than one
	 * @param integer $number Amount of things relevant for the plural form
	 * @return string Translated string
	 */
	public function transform( $domain, $singular, $plural = '', $number = 1 )
	{
		if( $plural !== '' ) {
			return $this->_translator->dn( $domain, $singular, $plural, $number );
		}

		return $this->_translator->dt( $domain, $singular );
	}
}