Number of files:
<?php
$quantity = ( isset( $this->quantity ) ? $this->quantity : 0 );
echo $quantity . ' ' . $this->translate( 'test', 'File', 'Files', $quantity );
?>
