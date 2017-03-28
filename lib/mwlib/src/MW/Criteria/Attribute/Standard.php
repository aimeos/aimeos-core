<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Common
 */


namespace Aimeos\MW\Criteria\Attribute;


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
class Standard implements \Aimeos\MW\Criteria\Attribute\Iface
{
	/**
	 * @var string Public data type used in the frontend
	 */
	private $type = 'string';

	/**
	 * @var mixed Internal data type, depends on the manager
	 */
	private $internalType = null;

	/**
	 * @var string Public code which maps to the internal code
	 */
	private $code = '';

	/**
	 * @var string Name of the attribute in the storage system
	 */
	private $internalCode = '';

	/**
	 * @var array List of internal dependencies
	 */
	private $internalDeps = [];

	/**
	 * @var string Human readable name of the attribute
	 */
	private $label = '';

	/**
	 * @var string Default value
	 */
	private $default = null;

	/**
	 * @var boolean Is required attribute
	 */
	private $required = true;

	/**
	 * @var boolean Is attribute publically available
	 */
	private $public = true;


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
	public function  __construct( array $params = [] )
	{
		$required = array( 'type', 'internaltype', 'code', 'internalcode', 'label' );

		foreach( $required as $entry )
		{
			if ( !isset($params[$entry]) ) {
				throw new \Aimeos\MW\Common\Exception( sprintf('Required parameter "%1$s" is missing', $entry) );
			}
		}

		$this->code = (string) $params['code'];
		$this->internalType = (string) $params['internaltype'];
		$this->internalCode = (string) $params['internalcode'];
		$this->label = (string) $params['label'];
		$this->type = (string) $params['type'];

		if( isset( $params['default'] ) ) {
			$this->default = $params['default'];
		}

		if( isset( $params['internaldeps'] ) ) {
			$this->internalDeps = $params['internaldeps'];
		}

		if( isset( $params['public'] ) ) {
			$this->public = (bool) $params['public'];
		}

		if( isset( $params['required'] ) ) {
			$this->required = (bool) $params['required'];
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
		return $this->type;
	}


	/**
	 * Returns the type internally used by the manager.
	 *
	 * @return mixed Type used by the manager
	 */
	public function getInternalType()
	{
		return $this->internalType;
	}


	/**
	 * Returns the public code for the search attribute.
	 *
	 * @return string Public code of the search attribute
	 */
	public function getCode()
	{
		return $this->code;
	}


	/**
	 * Returns the internal code for the search attribute.
	 *
	 * @return string Internal code of the search attribute
	 */
	public function getInternalCode()
	{
		return $this->internalCode;
	}


	/**
	 * Returns the list of internal dependency strings for the search attribute.
	 *
	 * @return array List of internal dependency strings
	 */
	public function getInternalDeps()
	{
		return $this->internalDeps;
	}


	/**
	 * Returns the human readable label for the search attribute.
	 *
	 * @return string Name of the search attribute
	 */
	public function getLabel()
	{
		return $this->label;
	}


	/**
	 * Returns the default value of the search attribute.
	 *
	 * @return string Default value of the search attribute
	 */
	public function getDefault()
	{
		return $this->default;
	}


	/**
	 * Returns true if the attribute should be publically available.
	 *
	 * @return boolean True if the attribute is public, false if not
	 */
	public function isPublic()
	{
		return $this->public;
	}


	/**
	 * Returns true if the attribute is required.
	 *
	 * @return boolean True if the attribute is required, false if not
	 */
	public function isRequired()
	{
		return $this->required;
	}


	/**
	 * Returns the attribute properties as key/value pairs.
	 *
	 * @return array Associative list of attribute key/value pairs
	 */
	public function toArray()
	{
		return array(
			'code' => $this->code,
			'type' => $this->type,
			'label' => $this->label,
			'public' => $this->public,
			'default' => $this->default,
			'required' => $this->required,
		);
	}
}
