<?php

$target = $this->config( 'controller/jsonapi/url/target' );
$cntl = $this->config( 'controller/jsonapi/url/controller', 'jsonapi' );
$action = $this->config( 'controller/jsonapi/url/action', 'get' );
$config = $this->config( 'controller/jsonapi/url/config', array() );

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
	,"errors": <?php echo $this->partial( 'controller/jsonapi/standard/template-errors', 'partials/errors-standard.php', array( 'errors' => $this->errors ) ); ?>
<?php endif; ?>

}
