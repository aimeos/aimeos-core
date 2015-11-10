{
<?php if( isset( $this->errors ) ) : ?>
	"errors": <?php echo $this->partial( 'controller/jsonadm/standard/template-errors', 'partials/errors-standard.php', array( 'errors' => $this->errors ) ); ?>
<?php endif; ?>

}
