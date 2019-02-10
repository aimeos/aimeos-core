<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
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
	 * @var string Public code which maps to the internal code
	 */
	private $code;

	/**
	 * @var mixed Default value
	 */
	private $default;

	/**
	 * @var string Name of the attribute in the storage system
	 */
	private $internalCode;

	/**
	 * @var array List of internal dependencies
	 */
	private $internalDeps;

	/**
	 * @var mixed Internal data type, depends on the manager
	 */
	private $internalType;

	/**
	 * @var \Closure Helper function for search parameters
	 */
	private $func;

	/**
	 * @var string Human readable name of the attribute
	 */
	private $label;

	/**
	 * @var boolean Is attribute publically available
	 */
	private $public;

	/**
	 * @var boolean Is required attribute
	 */
	private $required;

	/**
	 * @var string Public data type used in the frontend
	 */
	private $type;


	/**
	 * Initializes the search attribute object.
	 *
	 * @param array $params Parameter to be set on initialisation
	 *		[code] string
	 *		[default] mixed (optional)
	 *		[internalcode] string (optional)
	 *		[internaltype] string (optional)
	 *		[internaldeps] array (optional)
	 *		[function] Closure (optional)
	 *		[label] string
	 *		[public] boolean (optional)
	 *		[required] booblean (optional)
	 *		[type] string
	 */
	public function __construct( array $params = [] )
	{
		foreach( ['type', 'code'] as $entry )
		{
			if ( !isset($params[$entry]) ) {
				throw new \Aimeos\MW\Common\Exception( sprintf('Required parameter "%1$s" is missing', $entry) );
			}
		}

		$this->code = (string) $params['code'];
		$this->type = (string) $params['type'];

		$this->default = $this->check( $params, 'default' );
		$this->internalCode = $this->check( $params, 'internalcode' );
		$this->internalDeps = (array) $this->check( $params, 'internaldeps', [] );
		$this->internalType = $this->check( $params, 'internaltype' );
		$this->func = $this->check( $params, 'function' );
		$this->label = (string) $this->check( $params, 'label', '' );
		$this->public = (bool) $this->check( $params, 'public', true );
		$this->required = (bool) $this->check( $params, 'required', true );
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
	 * @return mixed Internal code of the search attribute
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
	 * Returns the helper function if available
	 *
	 * @return \Closure|null Helper function
	 */
	public function getFunction()
	{
		return $this->func;
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
	 * @return mixed Default value of the search attribute
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


	/**
	 * Tests and returns the value from the array
	 *
	 * @param array $params Associative list of parameters
	 * @param string $name Name of the parameter to test
	 * @param mixed $default Default value if parameter doesn't exist
	 * @return mixed Value from parameter list or default value
	 */
	protected function check( array $params, $name, $default = null )
	{
		return isset( $params[$name] ) ? $params[$name] : $default;
	}
}
