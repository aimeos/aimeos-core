{
<?php if( isset( $this->errors ) ) : ?>
	"errors": <?php echo $this->partial( $this->config( 'controller/jsonadm/standard/template-errors', 'partials/errors-standard.php' ), array( 'errors' => $this->errors ) ); ?>,
<?php elseif( isset( $this->data ) ) : ?>
	"data": <?php echo $this->partial( $this->config( 'controller/jsonadm/standard/template-data', 'partials/data-standard.php' ), array( 'data' => $this->get( 'data' ) ) ); ?>,
<?php endif; ?>

	"meta": {
		"total": <?php echo $this->get( 'total', 0 ); ?>

	}
}
