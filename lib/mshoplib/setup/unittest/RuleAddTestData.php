<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds rule test data and all items from other domains.
 */
class RuleAddTestData extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['MShopSetLocale'];
	}


	/**
	 * Adds rule test data.
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->additional );

		$this->msg( 'Adding rule test data', 0 );
		$this->additional->setEditor( 'core:lib/mshoplib' );

		$this->addRuleData();

		$this->status( 'done' );
	}


	/**
	 * Adds the rule test data.
	 *
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	private function addRuleData()
	{
		$ruleManager = \Aimeos\MShop\Rule\Manager\Factory::create( $this->additional, 'Standard' );
		$ruleTypeManager = $ruleManager->getSubManager( 'type', 'Standard' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'rule.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for rule domain', $path ) );
		}

		$ruleManager->begin();

		foreach( $testdata['rule/type'] as $dataset ) {
			$ruleTypeManager->save( $ruleTypeManager->create()->fromArray( $dataset ), false );
		}

		foreach( $testdata['rule'] as $dataset ) {
			$ruleManager->save( $ruleManager->create()->fromArray( $dataset ), false );
		}

		$ruleManager->commit();
	}
}
