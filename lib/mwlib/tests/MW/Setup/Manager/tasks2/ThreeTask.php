<?php

namespace Aimeos\MW\Setup\Task;


class ThreeTask extends Base
{
	public function getPreDependencies()
	{
		return array( 'TwoTask' );
	}


	public function getPostDependencies()
	{
		return [];
	}


	public function migrate()
	{
		$this->msg( 'Executing ThreeTask' );
		$this->status( 'OK' );
	}


	public function rollback()
	{
		$this->msg( 'Executing ThreeTask' );
		$this->status( 'OK' );
	}


	public function clean()
	{
		$this->msg( 'Executing ThreeTask' );
		$this->status( 'OK' );
	}


	protected function mysql()
	{
		$this->msg( 'Executing ThreeTask' );
		$this->status( 'OK' );
	}
}
