<?php $quantity = ( isset( $this->quantity ) ? $this->quantity : 0 ); ?>
Number of files: <?php echo $quantity . ' ' . $this->translate( 'test', 'File', 'Files', $quantity );
