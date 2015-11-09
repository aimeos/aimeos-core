{
<?php if( isset( $this->errors ) ) : ?>
	"errors": <?php echo $this->partial( 'controller/jsonapi/standard/template-errors', 'partials/errors-standard.php', array( 'errors' => $this->errors ) ); ?>
<?php endif; ?>

}
