<?php

namespace Aimeos\MW\Setup\Task;


class TwoTask extends Base
{
	public function getPreDependencies()
	{
		return [];
	}


	public function getPostDependencies()
	{
		return [];
	}


	public function migrate()
	{
		$this->msg( 'Executing TwoTask' );
		$this->status( 'OK' );
	}


	public function rollback()
	{
		$this->msg( 'Executing TwoTask' );
		$this->status( 'OK' );
	}


	public function clean()
	{
		$this->msg( 'Executing TwoTask' );
		$this->status( 'OK' );
	}


	protected function mysql()
	{
		$this->msg( 'Executing TwoTask' );
		$this->status( 'OK' );
	}
}
