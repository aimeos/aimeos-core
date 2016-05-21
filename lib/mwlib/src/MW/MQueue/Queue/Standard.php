<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 * @package MW
 * @subpackage MQueue
 */


namespace Aimeos\MW\MQueue\Queue;


/**
 * Default queue implementation
 *
 * @package MW
 * @subpackage MQueue
 */
class Standard implements Iface
{
	private $cname;
	private $conn;
	private $queue;
	private $sql;
	private $rtime;


	/**
	 * Initializes the queue object
	 *
	 * @param \Aimeos\MW\DB\Connection\Iface $conn Database connection
	 * @param string $queue Message queue name
	 * @param array $sql Associative list of SQL statements as key/value pairs for insert/reserve/get/delete
	 * @param integer $rtime Time before the job is released again in seconds
	 */
	public function __construct( \Aimeos\MW\DB\Connection\Iface $conn, $queue, $sql, $rtime )
	{
		$this->cname = md5( microtime(true) . getmypid() );
		$this->conn = $conn;
		$this->queue = $queue;
		$this->sql = $sql;
		$this->rtime = $rtime;
	}


	/**
	 * Adds a new message to the message queue
	 *
	 * @param string $msg Message, e.g. JSON encoded data
	 */
	public function add( $msg )
	{
		try
		{
			$stmt = $this->conn->create( $this->sql['insert'] );

			$stmt->bind( 1, $this->queue );
			$stmt->bind( 2, $this->cname );
			$stmt->bind( 3, '0001-01-01 00:00:00' );
			$stmt->bind( 4, $msg );

			$stmt->execute()->finish();
		}
		catch( \Exception $e )
		{
			throw new \Aimeos\MW\MQueue\Exception( $e->getMessage() );
		}
	}


	/**
	 * Removes the message from the queue
	 *
	 * @param \Aimeos\MW\MQueue\Message\Iface $msg Message object
	 */
	public function del( \Aimeos\MW\MQueue\Message\Iface $msg )
	{
		try
		{
			$stmt = $this->conn->create( $this->sql['delete'] );

			$stmt->bind( 1, $msg->getId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 2, $this->queue );

			$stmt->execute()->finish();
		}
		catch( \Exception $e )
		{
			throw new \Aimeos\MW\MQueue\Exception( $e->getMessage() );
		}
	}


	/**
	 * Returns the next message from the queue
	 *
	 * @return \Aimeos\MW\MQueue\Message\Iface|null Message object or null if none is available
	 */
	public function get()
	{
		$msg = null;

		try
		{
			$rtime = date( 'Y-m-d H:i:s', time() + $this->rtime );
			$stmt = $this->conn->create( $this->sql['reserve'] );

			$stmt->bind( 1, $this->cname );
			$stmt->bind( 2, $rtime );
			$stmt->bind( 3, $this->queue );
			$stmt->bind( 4, $rtime );

			$stmt->execute()->finish();


			$stmt = $this->conn->create( $this->sql['get'] );

			$stmt->bind( 1, $this->queue );
			$stmt->bind( 2, $this->cname );
			$stmt->bind( 3, $rtime );

			$result = $stmt->execute();

			if( ( $row = $result->fetch() ) !== false ) {
				$msg = new \Aimeos\MW\MQueue\Message\Standard( $row );
			}

			$result->finish();
		}
		catch( \Exception $e )
		{
			throw new \Aimeos\MW\MQueue\Exception( $e->getMessage() );
		}

		return $msg;
	}
}
