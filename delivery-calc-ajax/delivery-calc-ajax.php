<?php
/**
 * Plugin Name: delivery calc
 */

add_action('wp_ajax_deliverycallback', 'deliveycallbackajax');
add_action('wp_ajax_nopriv_deliverycallback', 'deliveycallbackajax');

define('DELLIN_TOKEN', '0E5F6A35-5D74-441A-85C8-F5F260033DAD');
define('DELLIN_EMAIL', 'dennkon@mail.ru');
define('DELLIN_PASS', 'Denqwer1234');

//Подключение классов

require 'class/Dellin_class.php';

function deliveycallbackajax(){
    //Проверка nonce код

    if( ! wp_verify_nonce( esc_attr($_POST['nonce_code']), 'MyAjax-nonce' ) ) die( 'Stop!');

    $errors = [];

    $delivery_in = empty(esc_attr($_POST['delivery_in']))
        ? $errors[] = 'Поле откуда, пустое!'
        : esc_attr($_POST['delivery_in']);
    $delivery_from = empty(esc_attr($_POST['delivery_from']))
        ? $errors[] = 'Поле куда, пустое!'
        : esc_attr($_POST['delivery_from']);
    $weight = empty(esc_attr($_POST['weight']))
        ? $errors[] = 'Некорректный вес товара!'
        : (float)esc_attr($_POST['weight']);

    if($delivery_in == $delivery_from) $errors[] = 'Поля не могут быть одинаковыми!';
    $volume = 1;

    if (!empty($errors)) echo $errors[count($errors)-1];
    else {
        $delivery_in_explode_array = explode(',',$delivery_in);
        $delivery_from_explode_array = explode(',',$delivery_from);

        // Валидность, массив не может быть меньше 2
        if(count($delivery_in_explode_array)<2 or count($delivery_from_explode_array)<2) {
            echo 'Доставка невозможна';
            wp_die();
        }

        //Получение кода КЛАДР если адрес - 2 разряда
        if(count($delivery_in_explode_array)==2) $code_cladr_in = $delivery_in_explode_array[1];
        if(count($delivery_from_explode_array)==2) $code_cladr_from = $delivery_from_explode_array[1];

        //Получение кода КЛАДР если адрес - 3 разряда и нет значения
        if(count($delivery_in_explode_array)==3 and isset($code_cladr_in)) $code_cladr_in = $delivery_in_explode_array[1];
        if(count($delivery_from_explode_array)==3 and isset($code_cladr_from)) $code_cladr_from = $delivery_from_explode_array[1];

        //######################################DELLIN-API-PART############################################################//

        $dellin = new Dellin_class(DELLIN_TOKEN,DELLIN_EMAIL,DELLIN_PASS);

        $response = $dellin -> curl_session([
            "derivalPoint" => (int)$code_cladr_in.'000000000000',
            "arrivalPoint" => (int)$code_cladr_from.'000000000000',
            "sizedVolume" => 1,
            "sizedWeight" => 2.6

        ],
            "https://api.dellin.ru/v1/public/calculator.json"
        );

        if(!isset($response->price)) echo 'Доставка неосуществляется';
        else {
            echo 'ТК "Деловые линии" '.$response->price." руб. От ".$response->time->value." дней";
        }


    }

    wp_die();
}