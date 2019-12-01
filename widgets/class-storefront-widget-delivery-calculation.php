<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 23.11.2019
 * Time: 12:49
 */

/**
 * Widget code Delivery_Calc
 **/

class Delivery_Calc extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
            'Delivery_Calc', // Base ID
            esc_html__( 'Виджет доставки', 'Расчитать доставку' ), // Name
            array( 'description' => esc_html__( 'Виджет, позволяющий расчетать стоимость доставки для компаний «деловые линии» и «ратэк» по средствам функционала api, учитывающий вес товара и его габариты.' ), ) // Args
        );

        // стили скрипты виджета, только если он активен
        if ( is_active_widget( false, false, $this->id_base ) || is_customize_preview() ) {
            add_action('wp_enqueue_scripts', array( $this, 'add_delivery_calc_scripts' ), 1100);
            add_action('wp_head', array( $this, 'add_delivery_calc_style' ) );
        }
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {

        $page = get_permalink();

        if(strripos($page,'/product/')):

            echo $args['before_widget'];
            if ( ! empty( $instance['title'] ) ) {
                echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
            }


            global $product;
            $attributes = $product->list_attributes();
            $weight = $product->get_weight();


            ?>

            <div>
                <div>
                    <p>
                        <label for="">Откуда</label>
                        <input id="delivery-calc-in" class="ui-autocomplete-input" type="text" placeholder="Индекс, город, код кладр" autocomplete="off">
                    </p>
                    <p>
                        <label for="">Куда</label>
                        <input id="delivery-calc-from" class="ui-autocomplete-input" type="text" placeholder="Индекс, город, код кладр" autocomplete="off">
                    </p>
                    <input type="hidden" id="delivery-weight" value="<?=$weight ?>">
                </div>
                <button id="delivery-calc-sand">Рассчитать</button>
            </div>

            <script>

            </script>

            <?php
        endif;
        echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {

        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'New title!', 'text_domain' );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'text_domain' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';

        return $instance;
    }

    function add_delivery_calc_scripts() {
        // фильтр чтобы можно было отключить скрипты
        if( ! apply_filters( 'show_delivery_calc_script', true, $this->id_base ) )
            return;

        $theme_url = get_stylesheet_directory_uri();


        wp_enqueue_script('delivery_calc_script', $theme_url .'/delivery_calc_script.js', 'in_footer' );
        wp_enqueue_script( 'jquery-ui-autocomplete', 'in_footer' );
        wp_localize_script( 'jquery', 'MyAjax', array(
                                                        'ajaxurl'   => admin_url( 'admin-ajax.php' ),
                                                        'nonce'     => wp_create_nonce('MyAjax-nonce')
                                                ) );
    }

    // стили виджета
    function add_delivery_calc_style() {
        // фильтр чтобы можно было отключить стили
        if( ! apply_filters( 'show_delivery_calc_style', true, $this->id_base ) )
            return;

        $wp_scripts = wp_scripts();
        wp_enqueue_style(
            'jquery-ui-theme-smoothness',
            sprintf(
                '//ajax.googleapis.com/ajax/libs/jqueryui/%s/themes/smoothness/jquery-ui.css', // working for https as well now
                $wp_scripts->registered['jquery-ui-core']->ver
            )
        );

        ?>
        <style type="text/css">
            .widget_delivery_calc {
                border: 1px solid #ccc;
                max-width: 240px;
                text-align: center;
                padding: 15px;
                color: black;
                font-family: inherit;
            }
            .widget_delivery_calc .widget-title{
                font-weight: 500;
            }

            .widget_delivery_calc input[type='text']{
                border: 1px solid #ddd !important;
                -webkit-border-radius: 2px !important;
                -moz-border-radius: 2px !important;
                border-radius: 2px !important;
                padding: 4px 10px !important;
                width: 100% !important;
                height: 29px !important;
            }

            .widget_delivery_calc button{
                color: white;
                width: 100%;
                background-color: #1e97d0;
                margin-top: 15px;
            }

            .widget_delivery_calc button:hover{
                background: #59add4;
            }

            .ui-menu{
                font-size: 11px !important;
                max-width: 240px !important;
            }
            .ui-menu:hover {
                font-size: 11px !important;
            }

        </style>
        <?php
    }

}