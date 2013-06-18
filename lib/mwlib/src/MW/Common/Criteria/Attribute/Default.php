<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Common
 */


/**
 * Default search attribute class.
 *
 * Instances of this class define input fields that can be searched for, how they
 * should be validated and what type is used by a manager internally when
 * storing data in the database
 *
 * @package MW
 * @subpackage Common
 */
class MW_Common_Criteria_Attribute_Default implements MW_Common_Criteria_Attribute_Interface
{
	/**
	 * @var string Public data type used in the frontend
	 */
	private $_type = 'string';

	/**
	 * @var mixed Internal data type, depends on the manager
	 */
	private $_internalType = null;

	/**
	 * @var string Public code which maps to the internal code
	 */
	private $_code = '';

	/**
	 * @var string Name of the attribute in the storage system
	 */
	private $_internalCode = '';

	/**
	 * @var array List of internal dependencies
	 */
	private $_internalDeps = array();

	/**
	 * @var string Human readable name of the attribute
	 */
	private $_label = '';

	/**
	 * @var string Default value
	 */
	private $_default = null;

	/**
	 * @var boolean Is required attribute
	 */
	private $_required = true;

	/**
	 * @var boolean Is attribute publically available
	 */
	private $_public = true;


	/**
	 * Initializes the search attribute object.
	 *
	 * @param array $params Parameter to be set on initialisation
	 *		[code] string
	 *		[default] string (optional)
	 *		[internalcode] string
	 *		[internaltype] string
	 *		[internaldeps] array (optional)
	 *		[label] string
	 *		[public] boolean (optional)
	 *		[required] booblean (optional)
	 *		[type] string
	 */
	public function  __construct( array $params = array() )
	{
		$required = array( 'type', 'internaltype', 'code', 'internalcode', 'label' );

		foreach( $required as $entry )
		{
			if ( !isset($params[$entry]) ) {
				throw new MW_Common_Exception( sprintf('Required parameter "%1$s" is missing', $entry) );
			}
		}

		$this->_code = (string) $params['code'];
		$this->_internalType = (string) $params['internaltype'];
		$this->_internalCode = (string) $params['internalcode'];
		$this->_label = (string) $params['label'];
		$this->_type = (string) $params['type'];

		if( isset( $params['default'] ) ) {
			$this->_default = $params['default'];
		}

		if( isset( $params['internaldeps'] ) ) {
			$this->_internalDeps = $params['internaldeps'];
		}

		if( isset( $params['public'] ) ) {
			$this->_public = (bool) $params['public'];
		}

		if( isset( $params['required'] ) ) {
			$this->_required = (bool) $params['required'];
		}
	}


	/**
	 * Returns the type of the attribute.
	 *
	 * Can be used in the frontend to create a speacial form for this type
	 *
	 * @return string Available types are "string", "integer", "float", "boolean", "date", "time", "datetime"
	 */
	public function getType()
	{
		return $this->_type;
	}


	/**
	 * Returns the type internally used by the manager.
	 *
	 * @return mixed Type used by the manager
	 */
	public function getInternalType()
	{
		return $this->_internalType;
	}


	/**
	 * Returns the public code for the search attribute.
	 *
	 * @return string Public code of the search attribute
	 */
	public function getCode()
	{
		return $this->_code;
	}


	/**
	 * Returns the internal code for the search attribute.
	 *
	 * @return string Internal code of the search attribute
	 */
	public function getInternalCode()
	{
		return $this->_internalCode;
	}


	/**
	 * Returns the list of internal dependency strings for the search attribute.
	 *
	 * @return array List of internal dependency strings
	 */
	public function getInternalDeps()
	{
		return $this->_internalDeps;
	}


	/**
	 * Returns the human readable label for the search attribute.
	 *
	 * @return string Name of the search attribute
	 */
	public function getLabel()
	{
		return $this->_label;
	}


	/**
	 * Returns the default value of the search attribute.
	 *
	 * @return string Default value of the search attribute
	 */
	public function getDefault()
	{
		return $this->_default;
	}


	/**
	 * Returns true if the attribute should be publically available.
	 *
	 * @return boolean True if the attribute is public, false if not
	 */
	public function isPublic()
	{
		return $this->_public;
	}


	/**
	 * Returns true if the attribute is required.
	 *
	 * @return boolean True if the attribute is required, false if not
	 */
	public function isRequired()
	{
		return $this->_required;
	}
}
