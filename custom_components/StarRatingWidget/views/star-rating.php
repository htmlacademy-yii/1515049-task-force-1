<?php
/** @var string $wrapperClass */
/** @var int $fullStars */
/** @var string $filledClass */

?>
<div class="<?= $wrapperClass ?>">
    <?php
    for ($i = 1; $i <= $fullStars; $i++) {
        echo '<span class=" ' . $filledClass . ' "></span>';
    }
    ?>
</div>
