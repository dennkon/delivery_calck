<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 23.11.2019
 * Time: 13:19
 */

/**
 * Storefront required widgets
 */

require 'class-storefront-widget-delivery-calculation.php';

/**
 * Storefront widget init
 */

function true_top_posts_widget_load() {
    register_widget( 'Delivery_Calc' );
}
add_action( 'widgets_init', 'true_top_posts_widget_load' );


