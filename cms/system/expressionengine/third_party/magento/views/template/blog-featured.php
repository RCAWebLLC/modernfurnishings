<?php

$EE =&get_instance();
?>

<div class="blog-featured-products">
        <?php foreach($products AS $product): ?>
        <?php
                echo $EE->magento_helper->getBlock(
                    'catalog/product',
                    array(
                        'product_sku' => $product,
                        'template' => 'catalog/product/view/ee/' . $block . '.phtml'
                    )
                )->toHtml(); ?>
        
        <?php endforeach; ?>
</div>