<?php

$target = $this->config( 'controller/jsonadm/url/target' );
$cntl = $this->config( 'controller/jsonadm/url/controller', 'jsonadm' );
$action = $this->config( 'controller/jsonadm/url/action', 'get' );
$config = $this->config( 'controller/jsonadm/url/config', array() );

$list = array();

foreach( $this->get( 'resources', array() ) as $resource ) {
	$list[$resource] = $this->url( $target, $cntl, $action, array( 'resource' => $resource ), array(), $config );
}

?>
{
	"meta": {
		"resources": <?php echo json_encode( $list ); ?>
	}
<?php if( isset( $this->errors ) ) : ?>
	,"errors": <?php echo $this->partial( 'controller/jsonadm/standard/template-errors', 'partials/errors-standard.php', array( 'errors' => $this->errors ) ); ?>
<?php endif; ?>

}
