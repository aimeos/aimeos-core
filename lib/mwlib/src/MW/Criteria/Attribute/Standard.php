<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	private $values;


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
			if( !isset( $params[$entry] ) ) {
				throw new \Aimeos\MW\Common\Exception( sprintf( 'Required parameter "%1$s" is missing', $entry ) );
			}
		}

		$this->values = $params;
	}


	/**
	 * Returns the type of the attribute.
	 *
	 * Can be used in the frontend to create a speacial form for this type
	 *
	 * @return string Available types are "string", "integer", "float", "boolean", "date", "time", "datetime"
	 */
	public function getType() : string
	{
		return $this->values['type'];
	}


	/**
	 * Returns the type internally used by the manager.
	 *
	 * @re  turn mixed Type used by the manager
	 */
	public function getInternalType()
	{
		return array_key_exists( 'internaltype', $this->values ) ? $this->values['internaltype'] : $this->getType();
	}


	/**
	 * Returns the public code for the search attribute.
	 *
	 * @return string Public code of the search attribute
	 */
	public function getCode() : string
	{
		return $this->values['code'];
	}


	/**
	 * Returns the internal code for the search attribute.
	 *
	 * @return mixed Internal code of the search attribute
	 */
	public function getInternalCode()
	{
		return array_key_exists( 'internalcode', $this->values ) ? $this->values['internalcode'] : $this->getCode();
	}


	/**
	 * Returns the list of internal dependency strings for the search attribute.
	 *
	 * @return array List of internal dependency strings
	 */
	public function getInternalDeps() : array
	{
		return isset( $this->values['internaldeps'] ) ? (array) $this->values['internaldeps'] : [];
	}


	/**
	 * Returns the helper function if available
	 *
	 * @return \Closure|null Helper function
	 */
	public function getFunction() : ?\Closure
	{
		return isset( $this->values['function'] ) ? $this->values['function'] : null;
	}


	/**
	 * Returns the human readable label for the search attribute.
	 *
	 * @return string Name of the search attribute
	 */
	public function getLabel() : string
	{
		return isset( $this->values['label'] ) ? $this->values['label'] : '';
	}


	/**
	 * Returns the default value of the search attribute.
	 *
	 * @return mixed Default value of the search attribute
	 */
	public function getDefault()
	{
		return isset( $this->values['default'] ) ? $this->values['default'] : null;
	}


	/**
	 * Returns true if the attribute should be publically available.
	 *
	 * @return bool True if the attribute is public, false if not
	 */
	public function isPublic() : bool
	{
		return isset( $this->values['public'] ) ? (bool) $this->values['public'] : true;
	}


	/**
	 * Returns true if the attribute is required.
	 *
	 * @return bool True if the attribute is required, false if not
	 */
	public function isRequired() : bool
	{
		return isset( $this->values['required'] ) ? (bool) $this->values['required'] : true;
	}


	/**
	 * Returns the attribute properties as key/value pairs.
	 *
	 * @return array Associative list of attribute key/value pairs
	 */
	public function toArray() : array
	{
		return array(
			'code' => $this->getCode(),
			'type' => $this->getType(),
			'label' => $this->getLabel(),
			'public' => $this->isPublic(),
			'default' => $this->getDefault(),
			'required' => $this->isRequired(),
		);
	}
}
