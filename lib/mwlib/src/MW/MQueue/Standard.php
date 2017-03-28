<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 * @package MW
 * @subpackage MQueue
 */


namespace Aimeos\MW\MQueue;


/**
 * Default message queue implementation
 *
 * @package MW
 * @subpackage MQueue
 */
class Standard extends Base implements Iface
{
	private $conn;
	private $queues = [];


	/**
	 * Initializes the message queue object
	 *
	 * @param array $config Associative list of configuration key/value pairs
	 */
	public function __construct( array $config )
	{
		parent::__construct( $config );

		try {
			$this->conn = $this->createConnection();
		} catch( \Aimeos\MW\DB\Exception $e ) {
			throw new \Aimeos\MW\MQueue\Exception( $e->getMessage() );
		}
	}


	/**
	 * Returns the queue for the given name
	 *
	 * @param string $name Queue name
	 * @return \Aimeos\MW\MQueue\Queue\Iface Message queue
	 */
	public function getQueue( $name )
	{
		if( !isset( $this->queues[$name] ) )
		{
			$sql = array(
				'insert' => $this->getConfig( 'sql/insert', 'INSERT INTO madmin_queue (queue, cname, rtime, message) VALUES (?, ?, ?, ?)' ),
				'reserve' => $this->getConfig( 'sql/reserve', 'UPDATE madmin_queue SET cname = ?, rtime = ? WHERE id IN ( SELECT * FROM ( SELECT id FROM madmin_queue WHERE queue = ? AND rtime < ? LIMIT 1 ) AS t )' ),
				'get' => $this->getConfig( 'sql/get', 'SELECT * FROM madmin_queue WHERE queue = ? AND cname = ? AND rtime = ? LIMIT 1' ),
				'delete' => $this->getConfig( 'sql/delete', 'DELETE FROM madmin_queue WHERE id = ? AND queue = ?' ),
			);
			$rtime = $this->getConfig( 'releasetime', 60 );

			$this->queues[$name] = new \Aimeos\MW\MQueue\Queue\Standard( $this->conn, $name, $sql, $rtime );
		}

		return $this->queues[$name];
	}


	/**
	 * Creates a new database connection.
	 *
	 * @return \Aimeos\MW\DB\Connection\Iface Database connection
	 */
	protected function createConnection()
	{
		$adapter = $this->getConfig( 'db/adapter', 'mysql' );
		$host = $this->getConfig( 'db/host' );
		$port = $this->getConfig( 'db/port' );
		$user = $this->getConfig( 'db/username', 'root' );
		$pass = $this->getConfig( 'db/password', '' );
		$sock = $this->getConfig( 'db/socket' );
		$dbase = $this->getConfig( 'db/database', 'aimeos' );

		$dsn = $adapter . ':dbname=' . $dbase;
		if( $sock == null )
		{
			$dsn .= isset( $host ) ? ';host=' . $host : '';
			$dsn .= isset( $port ) ? ';port=' . $port : '';
		}
		else
		{
			$dsn .= ';unix_socket=' . $sock;
		}

		$attr = array(
			\PDO::ATTR_PERSISTENT => $this->getConfig( 'db/opt-persistent', false ),
		);

		$pdo = new \PDO( $dsn, $user, $pass, $attr );
		$pdo->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
		$dbc = new \Aimeos\MW\DB\Connection\PDO( $pdo );

		foreach( (array) $this->getConfig( 'db/stmt', [] ) as $stmt ) {
			$dbc->create( $stmt )->execute()->finish();
		}

		return $dbc;
	}
}