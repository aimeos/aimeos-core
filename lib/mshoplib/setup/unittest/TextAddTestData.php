<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds attribute test data and all items from other domains.
 */
class TextAddTestData extends \Aimeos\MW\Setup\Task\Base
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
	 * Adds attribute test data.
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->additional );

		$this->msg( 'Adding text test data', 0 );
		$this->additional->setEditor( 'core:lib/mshoplib' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'text.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for text domain', $path ) );
		}

		$this->addTextData( $testdata );

		$this->status( 'done' );
	}


	/**
	 * Adds the required text test data for text.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	private function addTextData( array $testdata )
	{
		$textManager = \Aimeos\MShop\Text\Manager\Factory::create( $this->additional, 'Standard' );
		$textTypeManager = $textManager->getSubManager( 'type', 'Standard' );

		$ttype = $textTypeManager->createItem();

		$textManager->begin();

		foreach( $testdata['text/type'] as $key => $dataset )
		{
			$ttype->setId( null );
			$ttype->setCode( $dataset['code'] );
			$ttype->setDomain( $dataset['domain'] );
			$ttype->setLabel( $dataset['label'] );
			$ttype->setStatus( $dataset['status'] );

			$textTypeManager->saveItem( $ttype );
		}

		$text = $textManager->createItem();
		foreach( $testdata['text'] as $key => $dataset )
		{
			$text->setId( null );
			$text->setLanguageId( $dataset['langid'] );
			$text->setType( $dataset['type'] );
			$text->setDomain( $dataset['domain'] );
			$text->setLabel( $dataset['label'] );
			$text->setContent( $dataset['content'] );
			$text->setStatus( $dataset['status'] );

			$textManager->saveItem( $text, false );
		}

		$textManager->commit();
	}
}
