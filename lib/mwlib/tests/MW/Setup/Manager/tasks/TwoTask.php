<?php

namespace Aimeos\MW\Setup\Task;


class TwoTask extends Base
{
	public function getPreDependencies()
	{
		return array();
	}


	public function getPostDependencies()
	{
		return array();
	}


	protected function mysql()
	{
		$this->msg( 'Executing TwoTask' );
		$this->status( 'OK' );
	}
}
