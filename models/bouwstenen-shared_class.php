<?php
/* 
------------------------------------------------
Model class for shared components
ait bouwstenen plugin
------------------------------------------------ 
*/

class SharedBS
{

    public function checkvarianten($client_id)
    {
        $dbz = new BsDb();
        $bs_bestaat = $dbz->getArray("SELECT * FROM meten_response_data m inner join items i on m.itemid = i.itemid WHERE client_id = '" . $client_id . "' AND variant=1;");
        $bc_bestaat = $dbz->getArray("SELECT * FROM meten_response_data m inner join items i on m.itemid = i.itemid WHERE client_id = '" . $client_id . "' AND variant=2;");
        //die;
        $terug = array();
        if ($bs_bestaat)
        {
            $terug[] = 1;
        }
        if ($bc_bestaat)
        {
            $terug[] = 2;
        }
        return $terug;
    }

    public function maak_logo_vis($clid)
    {
        $uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
        $plugindir = aplugin_dir_url();
        $link = 'https://' . $_SERVER["HTTP_HOST"] . $uri_parts[0];
        $pdfnaam = 'ait-monitor';
        $this->beschikbare_varianten = $this->checkvarianten($clid);
        //ts($this->beschikbare_varianten);
        //ts($clid);

        switch ($GLOBALS['variant'])
        {
            case 1:
                $effe = "Bouwstenen voor de hechting";
                $logo_bestand = 'logo_bs.png';
                if (in_array(2, $this->beschikbare_varianten))
                {
                    $logo2_bestand = 'logo_bc.png';
                    $linkanders = $link . "?variant=2&visual=" . $clid;
                }
                else
                {
                    $linkanders = null;
                }
                break;
            case 2:
                $effe = "Basiscommunicatie";
                $logo_bestand = 'logo_bc.png';
                if (in_array(1, $this->beschikbare_varianten))
                {
                    $logo2_bestand = 'logo_bs.png';
                    $linkanders = $link . "?variant=1&visual=" . $clid;
                }
                else
                {
                    $linkanders = null;
                }
                break;
        }
        $uit = '
        <table><tr><td width = "80px">
        <img width = "50px" src = "' . $plugindir . 'images/' . $logo_bestand . '">
        </td><td><h1>' . $effe . '</h1>
        </td><td>
        <img style="float:right;display:table-cell;" onmouseover="" onclick="window.print()" width = "30" src="' . $plugindir . 'images/print.png" />
        </td>
        </td><td><a href="' . $link . '">
        <img style="float:right;display:table-cell;" onmouseover="" width = "30" src="' . $plugindir . 'images/kidsmenu.png" /></a>
        </td>';
        if ($linkanders)
        {
            $uit .= '<td><a href="' . $linkanders . '">
            <img style="float:right" width = "30px" src = "' . $plugindir . 'images/' . $logo2_bestand . '">
        </a>
        </td>';
        }

        $uit .= '</tr></table>';
        //ts($uri_parts);
        return $uit;
    }
    public function maak_logo_vis_2()
    {
        $uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
        $plugindir = aplugin_dir_url();
        $link = 'https://' . $_SERVER["HTTP_HOST"] . $uri_parts[0];

        switch ($GLOBALS['variant'])
        {
            case 1:
                $effe = "Bouwstenen voor de hechting";
                $logo_bestand = 'logo_bs.png';
                break;
            case 2:
                $effe = "Basiscommunicatie";
                $logo_bestand = 'logo_bc.png';
                break;
        }
        $uit = '
        <table><tr><td width = "80px">
        <img width = "50px" src = "' . $plugindir . 'images/' . $logo_bestand . '">
        </td><td><h1>' . $effe . '</h1>
        </td><td>
        
        </td>
        </td><td><a href="' . $link . '">
        <img style="float:right;display:table-cell;" onmouseover="" width = "30" src="' . $plugindir . 'images/kidsmenu.png" /></a>
        </td>';

        $uit .= '</tr></table>';
        //ts($uri_parts);
        return $uit;
    }
    public function maak_logo()
    {
        $uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
        $plugindir = aplugin_dir_url();
        //$alink = 'https://' . $_SERVER["HTTP_HOST"];
        $link = 'https://' . $_SERVER["HTTP_HOST"] . $uri_parts[0];
        //ts($variant);
        $vari = $GLOBALS["variant"];
        $logo_bestand = '<img width = "50px" src = "' . $plugindir . 'images/kidsmenu.png">';
        $effe = "Basiscommunicatie en </br>Bouwstenen voor de hechting";
        $wijd = "80px";

        switch ($vari)
        {
            case 1:
                $effe = "Bouwstenen voor de hechting";
                $logo_bestand = '<img width = "50px" src = "' . $plugindir . 'images/logo_bs.png">';
                $wijd = "80px";
                break;
            case 2:
                $effe = "Basiscommunicatie";
                $logo_bestand = '<img width = "50px" src = "' . $plugindir . 'images/logo_bc.png">';
                $wijd = "80px";

                break;

        }
        $uit = '
        <table>
        <tr>
        <td width = "' . $wijd . '">' . $logo_bestand . ' </td>
        <td><h1>' . $effe . '</h1>
        </td>
        </tr></table>';
        //ts($uri_parts);
        return $uit;
    }

    public function log_het($wat, $bestand)
    {
        $wie = 'user id: ' . aget_current_user_id();
        $waar = 'log_' . $bestand . '.log';
        $wanneer = date("j.n.Y h:m");
        file_put_contents($waar, $wie . ', ' . $wanneer . PHP_EOL, FILE_APPEND);
        file_put_contents($waar, $wat . PHP_EOL, FILE_APPEND);
    }

    public function get_style()
    {
        $plugindir = aplugin_dir_url();
        $output = '<link rel="stylesheet" id="sm-style-css" href="' . $plugindir . 'css/bs.css" type="text/css">';
        $output .= '<link rel="stylesheet" id="smp-style-css" href="' . $plugindir . 'css/print.css" type="text/css" media="print">';
        $output .= '<link rel="stylesheet" id="smp-style-css2" href="' . $plugindir . 'css/print2.css" type="text/css">';
        return $output;

    }
    public function publiceer_toelichting($wat)
    {
        $dat = $this->haal($wat);
        $plugindir = plugin_dir_url();
        $logo = $this->maak_logo($GLOBALS['variant']);
        $output = '<link rel="stylesheet" type="text/css" href="' . $plugindir . 'css/bs.css">';
        $output .= '    <div class="client_wrapper">';
        $output .= '        <div id = "hoofder" class = "hoofder">' . $logo . '&nbsp;&nbsp;<h1>' . $wat . '</h1></div>';
        $output .= '        <div id = "alg_toel" class = "alg_toel">' . $dat . '</div>';
        $output .= '    </div>';

        return $output;

    }
    public function haal($wat)
    {

        $this->plugindir = plugin_dir_url();

        switch ($wat)
        {
            case 'zichtbaarheid':
                $uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
                $link = 'https://' . $_SERVER["HTTP_HOST"] . $uri_parts[0] . 'images/zichtbaarheid.png';
                //ts($link);
                $output = '<p>Niet alle bouwstenen of onderdelen van de basiscommunicatie zijn voor elk kind zichtbaar. Een bouwsteen als "creativiteit" wordt pas uitgevraagd over kinderen van 5 jaar en ouder en zijn niet zichtbaar bij meetmomenten waarop het kind nog geen vijf jaar is.</p>';
                $output .= '<p>Zie onderstaande figuur voor een overzicht van wat op welke leeftijd te zien is:</p>';
                $output .= '<img class  = "toelichting_fig" src = "' . $this->plugindir . 'images/zichtbaarheid.svg"></>';
                break;
            case 'vragenlijst':
                $uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
                $link = 'https://' . $_SERVER["HTTP_HOST"] . $uri_parts[0] . 'images/zichtbaarheid.png';
                //ts($link);

                $WpDbQ = new BsDb();
                $CatsArr = $WpDbQ->haalCats(200);
                $ItemsArr = array();
                foreach ($CatsArr as $cat)
                {
                    $ItemsArr[$cat] = $WpDbQ->haalItemsVoorCat($cat, 'lang');
                }
                $output = "";
                $output .= '<table class = "item-tabel">';
                $output .= "<tr><td colspan = '2'>Beoordeel elk item door aan te geven of het antwoord links 'vaak' of 'meestal' van toepassing is, of het antwoord rechts 'vaak' of 'meestal' van toepassing is, of geef door 'soms' aan dat beide beschrijvingen ongeveer in dezelfde mate van toepassing zijn.</td></tr>";
                $output .= '</table>';
                foreach ($CatsArr as $cat)
                {

                    $output .= '<H2>' . $cat . '</H2>';
                    $output .= '<div style="overflow-x:auto;">';
                    $output .= '<table class = "item-tabel">';
                    $output .= "<tr><th colspan = '2'>Het kind...</th></tr>";
                    foreach ($ItemsArr[$cat] as $val)
                    {
                        if ($val["richting"] == 1)
                        {
                            $links = $val["pos"];
                            $rechts = $val["neg"];
                        }
                        else
                        {
                            $links = $val["neg"];
                            $rechts = $val["pos"];
                        }
                        $output .= "<tr><td>" . $links . "</td><td>" . $rechts . "</td></tr>";
                    }
                    $output .= '</table></div>';
                    $plugindir = plugin_dir_url();
                    $output .= '<div style="overflow-x:auto;">';
                    $output .= '<table class = "item-tabel-antwoorden">';
                    $output .= '<tr>
                    <td><img class = "tab_5" src="' . $plugindir . 'images/meestal.svg" /></td>
                    <td><img class = "tab_5" src="' . $plugindir . 'images/vaak.svg" /></td>
                    <td><img class = "tab_5" src="' . $plugindir . 'images/soms.svg" /></td>
                    <td><img class = "tab_5" src="' . $plugindir . 'images/vaak.svg" /></td>
                    <td><img class = "tab_5" src="' . $plugindir . 'images/meestal.svg" /></td></tr>';
                    $output .= '</table></div>';

                }
                //ts($this->ItemsArr);
                //$output .= '<p>Zie onderstaande figuur voor een overzicht van wat op welke leeftijd te zien is:</p>';
                //$output .= '<img class  = "toelichting_fig" src = "' . $this->plugindir . 'images/zichtbaarheid.svg"></>';
                break;
        }
        return $output;
    }
}