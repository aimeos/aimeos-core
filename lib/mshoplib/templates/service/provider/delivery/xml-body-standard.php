<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */

/* Available data:
 * - orderItems : List of order items
 * - baseItems : List of order base items
 */
?>
<?= '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>' ?>


<orders>
<?= $this->partial( 'service/provider/delivery/xml-item-standard', ['orderItems' => $this->orderItems, 'baseItems' => $this->baseItems] ) ?>

</orders>