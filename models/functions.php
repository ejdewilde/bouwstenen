<?php
// additional functions etc. e.g to replace native wordpress functions

function aplugin_dir_url($var = false)
{
    //return 'https://mijnait.konsili.dev/_additional_classes/_third_party/AitMonitor/bouwstenen/';

    return 'https://localhost/hansei/ait-wp/wp-content/plugins/bouwstenen/';
}

function aget_site_url()
{
    //return 'https://mijnait.konsili.dev';
    return 'https://localhost/hansei/ait-wp/wp-content/plugins/';
}

function aget_current_user_id()
{
    global $userData;
    return 1401;
    return $userData->ait_id;
}
