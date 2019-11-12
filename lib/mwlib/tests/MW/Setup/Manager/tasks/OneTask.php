<?php

namespace Aimeos\MW\Setup\Task;


class OneTask extends Base
{
	public function getPreDependencies() : array
	{
		return [];
	}


	public function getPostDependencies() : array
	{
		return array( 'TwoTask' );
	}


	public function migrate()
	{
		$this->status( '' );
		$this->msg( 'Executing OneTask' );
		$this->status( 'OK' );
	}


	public function rollback()
	{
		$this->status( '' );
		$this->msg( 'Executing OneTask' );
		$this->status( 'OK' );
	}


	public function clean()
	{
		$this->status( '' );
		$this->msg( 'Executing OneTask' );
		$this->status( 'OK' );
	}


	protected function mysql()
	{
		$this->status( '' );
		$this->msg( 'Executing OneTask' );
		$this->status( 'OK' );
	}
}
