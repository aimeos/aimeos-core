<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2025
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds default records to tables.
 */
class MShopAddTypeData extends Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['Type', 'MShopSetLocale'];
	}


	public function up()
	{
	}


	protected function add( ?string $filename = null )
	{
		$context = $this->context();
		$sitecode = $context->locale()->getSiteItem()->getCode();

		$this->info( sprintf( 'Adding MShop type data for site "%1$s"', $sitecode ), 'vv' );

		if( !$filename )
		{
			$ds = DIRECTORY_SEPARATOR;
			$filename = __DIR__ . $ds . 'default' . $ds . 'data' . $ds . 'type.php';
		}

		if( ( $data = include( $filename ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No type file found in "%1$s"', $filename ) );
		}

		$this->update( $data );
	}


	protected function existing( array $entries ) : array
	{
		$expr = $map = [];

		$manager = $this->manager( 'type' );
		$filter = $manager->filter()->slice( 0, 0x7fffffff );

		foreach( $entries as $entry )
		{
			$expr[] = $filter->and( [
				$filter->is( 'type.domain', '==', $entry['domain'] ),
				$filter->is( 'type.code', '==', $entry['code'] )
			] );
		}

		$filter->add( $filter->or( $expr ) );

		foreach( $manager->search( $filter ) as $id => $item ) {
			$map[$item->getDomain()][$item->getCode()][$id] = $item;
		}

		return $map;
	}


	protected function manager( $domain ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return \Aimeos\MShop::create( $this->context(), $domain );
	}


	protected function update( array $data )
	{
		$manager = $this->manager( 'type' );
		$map = $this->existing( $data );

		foreach( $data as $entry )
		{
			if( isset( $map[$entry['domain']][$entry['code']] ) ) {
				continue;
			}

			$item = $manager->create()
				->setCode( $entry['code'] )
				->setDomain( $entry['domain'] )
				->setLabel( $entry['label'] )
				->setI18n( $entry['i18n'] ?? [] )
				->setStatus( $entry['status'] );

			$manager->save( $item );
		}
	}
}
