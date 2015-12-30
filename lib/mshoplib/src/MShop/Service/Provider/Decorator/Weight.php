<?php

/**
 * @copyright Aimeos (aimeos.org), 2015
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Decorator for service providers adding additional costs.
 *
 * @package MShop
 * @subpackage Service
 */
class Weight
	extends \Aimeos\MShop\Service\Provider\Decorator\Base
	implements \Aimeos\MShop\Service\Provider\Decorator\Iface
{
	private $beConfig = array(
		'weight.min' => array(
			'code' => 'weight.min',
			'internalcode' => 'weight.min',
			'label' => 'Minimum weight of the package',
			'type' => 'string',
			'internaltype' => 'float',
			'default' => '',
			'required' => false,
		),
		'weight.max' => array(
			'code' => 'weight.max',
			'internalcode' => 'weight.max',
			'label' => 'Maximum weight of the package',
			'type' => 'string',
			'internaltype' => 'float',
			'default' => '',
			'required' => false,
		),
	);


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 *    known by the provider but aren't valid
	 */
	public function checkConfigBE(array $attributes)
	{
		$error = $this->getProvider()->checkConfigBE($attributes);
		$error += $this->checkConfig($this->beConfig, $attributes);

		return $error;
	}


	/**
	 * Returns the configuration attribute definitions of the provider
	 *
	 * This will generate a list of available fields and rules for the value of
	 * each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing MW_Common_Critera_Attribute_Interface
	 */
	public function getConfigBE()
	{
		$list = $this->getProvider()->getConfigBE();

		foreach ($this->beConfig as $key => $config) {
			$list[$key] = new \Aimeos\MW\Criteria\Attribute\Standard($config);
		}

		return $list;
	}


	/**
	 * Checks if the the basket weight is ok for the service provider.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object
	 * @return boolean True if payment provider can be used, false if not
	 */
	public function isAvailable(\Aimeos\MShop\Order\Item\Base\Iface $basket)
	{
		$context = $this->getContext();
		$prodMap = array();
		$basketWeight = 0;


		foreach ($basket->getProducts() as $basketItem)
		{
			$prodId = $basketItem->getProductId();

			// basket can contain a product several times in different basket items
			if (!isset($prodMap[$prodId])) {
				$prodMap[$prodId] = 0.0;
			}
			$prodMap[$prodId] += $basketItem->getQuantity();
		}

		$propertyManager = \Aimeos\MShop\Factory::createManager($context, 'product/property');
		$search = $propertyManager->createSearch(true);
		$expr = array(
			$search->compare('==', 'product.property.parentid', array_keys($prodMap)),
			$search->compare('==', 'product.property.type.code', 'package-weight'),
			$search->getConditions(),
		);
		$search->setConditions($search->combine('&&', $expr));
		$search->setSlice(0, 0x7fffffff); // if more than 100 products are in the basket

		foreach ($propertyManager->searchItems($search) as $property) {
			$basketWeight += ((float) $property->getValue()) * $prodMap[$property->getParentId()];
		}

		if ($this->checkWeightScale($basketWeight) === false) {
			return false;
		}

		return $this->getProvider()->isAvailable($basket);
	}


	/**
	 * Checks if the country code is in the list of codes specified by the given key
	 *
	 * @param float $basketWeight The basket weight
	 * @return boolean True if the current basket weight is within the providers weight range
	 */
	protected function checkWeightScale($basketWeight)
	{
		$min = $this->getConfigValue(array('weight.min'));
		$max = $this->getConfigValue(array('weight.max'));

		if ($min !== null && ((float) $min) > $basketWeight) {
			return false;
		}

		if ($max !== null && ((float) $max) < $basketWeight) {
			return false;
		}

		return true;
	}
}