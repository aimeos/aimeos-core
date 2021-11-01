<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds rule test data and all items from other domains.
 */
class RuleAddTestData extends Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['Rule', 'MShopSetLocale'];
	}


	/**
	 * Adds rule test data.
	 */
	public function up()
	{
		$this->info( 'Adding rule test data', 'v' );
		$this->context()->setEditor( 'core:lib/mshoplib' );

		$this->addRuleData();
	}


	/**
	 * Adds the rule test data.
	 *
	 * @throws \RuntimeException If no type ID is found
	 */
	private function addRuleData()
	{
		$ruleManager = \Aimeos\MShop\Rule\Manager\Factory::create( $this->context(), 'Standard' );
		$ruleTypeManager = $ruleManager->getSubManager( 'type', 'Standard' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'rule.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No file "%1$s" found for rule domain', $path ) );
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
