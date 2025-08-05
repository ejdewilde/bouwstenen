<?php

/*
Plugin Name: Bouwstenen voor de hechting
Description: Vragenlijst, visualisatie en rapportage voor bouwstenen voor de hechting
Version:     1.0
Authors:     HanSei: Erik Jan de Wilde / Konsili: Ivar Hoekstra
*/

ini_set('display_errors', 'On');

function bs_shortcode($atts)
{
    /* als rapportage wordt aangeroepen:
    $a = shortcode_atts(
    array(
    'variant' => 'beiden',
    ), $atts, 'fig2');
    */
    $plugin_root = plugin_dir_path(__FILE__) . "/";
    include_once $plugin_root . "additional/monitorAit_settings.php";
    include_once $plugin_root . "models/bouwstenen-db_class.php";
    include_once $plugin_root . "models/bouwstenen-nabijheid_class.php";
    include_once $plugin_root . "models/bouwstenen-edit_class.php";
    include_once $plugin_root . "models/bouwstenen-menu_class.php";
    include_once $plugin_root . "models/bouwstenen-questionnaire_class.php";
    //include_once $plugin_root . "models/bouwstenen-spin_class.php";
    include_once $plugin_root . "models/bouwstenen-trend_class.php";
    include_once $plugin_root . "models/bouwstenen-visual_class.php";
    include_once $plugin_root . "models/bouwstenen-shared_class.php";
    //echo '<pre>';
    //echo print_r($atts);
    //echo '</pre>';
    $wat = new Bs_Menu();
    $ta  = $wat->get_interface();
    //exit;
}

function AIT_ajax()
{
    $plugin_root = plugin_dir_path(__FILE__) . "/";
    include_once $plugin_root . "models/bouwstenen-ajax.php";
}

function bs_register_shortcode()
{
    add_shortcode('bouwstenen', 'bs_shortcode');
    add_shortcode('AIT-ajax', 'AIT_ajax');
}

add_action('init', 'bs_register_shortcode');
