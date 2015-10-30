<?php

/**
 * @copyright Aimeos (aimeos.org), 2015
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Service
 */


/**
 * Decorator for service providers adding additional costs.
 *
 * @package MShop
 * @subpackage Service
 */
class MShop_Service_Provider_Decorator_Weight extends MShop_Service_Provider_Decorator_Abstract
{
	private $_beConfig = array(
		'weight' => array(
			'code'         => 'weight',
			'internalcode' => 'weight',
			'label'        => 'Gewichtsstaffel für Versandkosten',
			'type'         => 'string',
			'internaltype' => 'string',
			'default'      => '',
			'required'     => false,
		)
	);


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 *
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 *    known by the provider but aren't valid
	 */
	public function checkConfigBE(array $attributes)
	{
		$error = $this->_getProvider()->checkConfigBE($attributes);
		$error += $this->_checkConfig($this->_beConfig, $attributes);

		return $error;
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing MW_Common_Critera_Attribute_Interface
	 */
	public function getConfigBE()
	{
		$list = $this->_getProvider()->getConfigBE();

		foreach ($this->_beConfig as $key => $config) {
			$list[$key] = new MW_Common_Criteria_Attribute_Default($config);
		}

		return $list;
	}


	/**
	 * Checks if the the basket weight is ok for the service provider.
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket object
	 *
	 * @return boolean True if payment provider can be used, false if not
	 */
	public function isAvailable(MShop_Order_Item_Base_Interface $basket)
	{
		$context      = $this->_getContext();
		$basketWeight = 0;
		$basketItems  = $basket->getProducts();


		foreach ($basketItems as $basketItem)
		{
		    $prodId = $basketItem->getProductId();

		    if( !isset( $prodMap[$prodId] ) ) { // basket can contain a product several times in different basket items
		        $prodMap[$prodId] = 0.0;
		    }
		    $prodMap[$prodId] += $basketItem->getQuantity();
		}

		$propertyManager = MShop_Factory::createManager($context, 'product/property');
		$search = $propertyManager->createSearch(true);
		$expr = array(
		    $search->compare( '==', 'product.property.productid', array_keys( $prodMap ) ),
		    $search->compare( '==', 'product.property.type.code', 'package-weight' ),
		    $search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 0x7fffffff ); // if more than 100 products are in the basket

		foreach ($propertyManager->searchItems($search) as $property) {
		    $basketWeight += ((float) $property->getValue()) * $prodMap[$property->getParentId()];
		}

		if ($this->_checkWeightScale($basketWeight) === false) {
			return false;
		}

		return $this->_getProvider()->isAvailable($basket);
	}


	/**
	 * Checks if the country code is in the list of codes specified by the given key
	 *
	 * @param float $basketWeight The basket weight
	 *
	 * @return boolean True if the current basket weight is within the providers weight range
	 */
	protected function _checkWeightScale($basketWeight)
	{
		if (!(float) $this->_getConfigValue(array('weight.min')) > 0 || !(float) $this->_getConfigValue(array('weight.max')) > 0) {
			return false;
		}
		$weightMin = (float) $this->_getConfigValue(array('weight.min'));
		$weightMax = (float) $this->_getConfigValue(array('weight.max'));

		if ($basketWeight > $weightMin && $basketWeight <= $weightMax) {
			return true;
		} else {
			return false;
		}
	}

}