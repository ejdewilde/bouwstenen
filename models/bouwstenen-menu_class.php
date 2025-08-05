<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
class Bs_Menu
{
    public function __CONSTRUCT()
    {
        //ts('menu');
        if (array_key_exists("aanpassen", $_GET)) {
            if ($_GET["aanpassen"]) {
                $this->pasaan();
                exit;
            }
        }
        /*DEBUG*/
        //ts('GET:                        ');
        //ts($_GET);
        //ts('POST:                       ');
        //ts($_POST);

        include 'functions.php';

        /*varianten:
        1: alleen bouwstenen
        2: alleen basiscommunicatie
        3: zowel basiscommunicatie als bouwstenen
        standaard: basiscommunicatie
         */
        $GLOBALS['variant'] = 3;

        if (array_key_exists("variant", $_GET)) {
            $GLOBALS['variant'] = $_GET["variant"];
        }
        if (array_key_exists("variant", $_POST)) {
            $GLOBALS['variant'] = $_POST["variant"];
        }
        /*
        if (array_key_exists("ookbouwstenen", $_POST))
        {
        if ($_POST["ookbouwstenen"] == 'jakort')
        {
        $GLOBALS['variant'] = 3;
        }
        elseif ($_POST["ookbouwstenen"] == 'javoll')
        {
        $GLOBALS['variant'] = 4;
        }
        }
         */
        //ts('VARIANT:                    ');
        //ts($GLOBALS['variant'] . '                           ');

        $this->resp      = aget_current_user_id();
        $this->plugindir = aplugin_dir_url();
        global $userData;

        //ts($this->clienten);
        if (! $this->resp) {
            echo "je moet eerst inloggen om de monitor te kunnen gebruiken.";
            exit;
        }

        // en even voor EJ lokaal
        if ($_SERVER["SERVER_NAME"] == "localhost") {
            $this->resp = 3;
        }
        //$this->resp = 2170;

        $this->haal_clienten($this->resp);

        if ($_POST) {
            //ajax call
            if (array_key_exists("bewaar", $_POST)) {
                //$effe = serialize($_POST);
                //item
                if ($_POST["bewaar"] == 'item') {
                    $this->schrijfItemBij($_POST);
                }
                if ($_POST["bewaar"] == 'archief') {
                    //archiveren
                    $this->archiveer($_POST);
                }
            }

            //clientgegevens wijzigen
            elseif (array_key_exists("wijzig", $_POST) | array_key_exists("wijzig_client", $_POST) | array_key_exists("verwijder_id", $_POST) | array_key_exists("verwijder_id_open", $_POST)) {
                $wat = new Bs_Edit($_POST);
                //die;
                $ta = $wat->get_interface();
                exit;
            }

            //nabijheidvragenlijst openen
            elseif (array_key_exists("nabijheid", $_POST)) {
                $wat = new Bs_Nabij($_POST);
                $ta  = $wat->get_interface();
                exit;
            }

            //vragenlijst
            elseif (array_key_exists("vragenlijst", $_POST)) {
                $this->sla_nabijheid_op($_POST);
                /*$Wpdcheck = new BsDb();

                if ($Wpdcheck->haalDataCheck($_POST))
                {
                $postdata = $this->haal_post_data_open($_POST["idkind"]);
                //ts($postdata);
                $_POST = $postdata;
                $_POST["visual"] = $_POST["idkind"];
                }
                 */
                $wat = new Bs_Quest($_POST);
                $ta  = $wat->get_interface();
                exit;
            }

            //meta-formulier openen
            elseif (array_key_exists("client_id", $_POST)) {
                //ts($GLOBALS['variant']);
                $this->haal_Ws_en_Lft();
                $this->selectBoxes = $this->MaakSelectBoxes("");
                $this->Middenstuk  = $this->Maak_keuze_menu($GLOBALS['variant']);
                $uit               = $this->Middenstuk;
                $uit .= $this->MaakFooter();
                echo $uit;
            }
            //staat nog open
            elseif (array_key_exists("visual_open", $_POST)) {
                $kid             = $_POST["visual_open"];
                $postdata        = $this->haal_post_data_open($_POST["visual_open"]);
                $_POST           = $postdata;
                $_POST["visual"] = $kid;
                //ts($_POST);
                $wat = new Bs_Quest($_POST);
                $ta  = $wat->get_interface();
            }
            //afgesloten: visualisatie
            elseif (array_key_exists("visual", $_POST)) {
                $wat = new Bs_Visual($_POST);
                $ta  = $wat->get_interface();
            }
        } else {
            if ($_GET) {
                //formulier voor specifiek kind openen
                if (array_key_exists("client_id", $_GET)) {

                    //ts($aa);
                    $this->haal_Ws_en_Lft();
                    $this->selectBoxes = $this->MaakSelectBoxes($_GET["client_id"]);
                    $this->Middenstuk  = $this->Maak_keuze_menu($GLOBALS['variant']);
                    $uit               = $this->Middenstuk;
                    $uit .= $this->MaakFooter();
                    echo $uit;
                    exit;
                } elseif (array_key_exists("visual", $_GET)) {
                    $wat = new Bs_Visual();
                    $ta  = $wat->get_interface();
                    exit;
                } elseif (array_key_exists("toelichting", $_GET)) {
                    $uit = "";
                    if ($_GET["toelichting"] == "zichtbaarheid") {
                        $WpDbQ = new BsDb();
                        $ShBS  = new SharedBS();
                        $uit   = $ShBS->publiceer_toelichting('zichtbaarheid');
                    }
                    echo $uit;
                    exit;
                } elseif (array_key_exists("vragenlijst", $_GET)) {
                    $WpDbQ = new BsDb();
                    $ShBS  = new SharedBS();
                    $uit   = $ShBS->publiceer_toelichting('vragenlijst');
                    echo $uit;
                    exit;
                }
            }
            //kinderlijst
            $uit = $this->maak_client_knopjes();
            echo $uit;
        }
    }

    public function pasaan()
    {
        $dbz = new BsDb();
        $dbz->doe_aanpassing();
        echo '<p>okidoki. aangepast.';
    }

    public function sla_nabijheid_op($data)
    {
        $WpDbQ = new BsDb();
        $ShBS  = new SharedBS();
        //ts($data);

        $nab = [];
        //ts($data);

        //nabijheidsdata
        for ($x = 0; $x < 11; $x++) {
            $naam = 'naam_' . $x;

            if (array_key_exists($naam, $data)) {
                if ($data[$naam] !== '') {
                    $nabsco = 'slide_' . $x;
                    $nab[]  = 'insert into nabijheids_data (client_id, uid_respondent, archief, naam, nabijheid) values ("'
                    . $data["idkind"] . '", "' . $this->resp . '", "' . $data["observatie_datum"] . '", "' . $data[$naam] . '", "' . $data[$nabsco] . '");';
                }
            }
        }
        //ts($nab);
        foreach ($nab as $zql) {
            $WpDbQ->exesql($zql);
            $ShBS->log_het($zql, 'nabijheidsopslag');
        }

    }

    public function archiveer($data)
    {
        $WpDbQ = new BsDb();
        $ShBS  = new SharedBS();
        //oudere weggooien
        $weg = 'delete m from meten_response_data m
                inner join (select max(id) as lastId, itemid from meten_response_data
                where archief is null  group by itemid having count(*) > 1)
                duplic on duplic.itemid = m.itemid
                where m.archief is null and m.id < duplic.lastId and client_id = "' . $data["client_id"] . '";';
        //archief updaten

        $zql1 = 'update meten_response_data set archief = "' . $data["observatie_datum"] . '", zichtbaar=1 where client_id = "';
        $zql1 .= $data["client_id"] . '" AND uid_respondent = ' . $this->resp . ' and archief is null;';

        //$zql2 = 'update meten_response_data set zichtbaar = 1 where client_id = "';
        //$zql2 .= $data["client_id"] . '" AND uid_respondent = ' . $this->resp . ';';

        $WpDbQ->exesql($weg);
        $WpDbQ->exesql($zql1);
        //$WpDbQ->exesql($zql2);
        //ts($zql1);

        $ShBS->log_het($zql1, 'opslag');
    }

    public function schrijfItemBij($data)
    {
        $WpDbQ = new BsDb();
        $sqla  = 'insert into meten_response_data ';
        $sqlb  = ' values ';
        $kols  = "(client_id, lftcat, uid_respondent, werksoort, video, versie, observatie_datum, itemid, `value`, `timestamp`)";
        $vals  = '("' . $data["client_id"] . '",';
        $vals .= $data["lftcat"] . ',';
        $vals .= $this->resp . ',';
        $vals .= $data["werksoort"] . ',';
        $vals .= '"' . $data["video"] . '",';
        $vals .= '"' . $data["versie"] . '",';
        $vals .= '"' . $data["observatie_datum"] . '",';
        $vals .= substr($data["itemid"], 6) . ',';
        $vals .= $data["value"] . ',';
        $vals .= $data["timestamp"] . ');';
        //file_put_contents('./log_'.date("j.n.Y").'.log', $sqla.$kols.$sqlb.$vals.'\n', FILE_APPEND);
        $WpDbQ->exesql($sqla . $kols . $sqlb . $vals);

    }
    public function haal_post_data_open($kid)
    {
        $db = new BsDb();
        return $db->get_open_data($kid);
    }
    public function maak_client_knopjes()
    {
        $ShBS       = new SharedBS();
        $logo       = $ShBS->maak_logo(3);
        $output     = '<link rel="stylesheet" type="text/css" href="' . $this->plugindir . 'css/bs.css">';
        $knoppen_bc = '';
        $knoppen_bs = '';
        $gehadbc    = [];
        $gehadbs    = [];
        if (key_exists('basiscommunicatie', $this->clienten)) {
            if ($this->clienten['basiscommunicatie']) {
                //
                foreach ($this->clienten['basiscommunicatie'] as $val) {

                    if (! array_key_exists($val, $gehadbc)) {
                        $knoppen_bc .= '<button class = "kind_knop" name = "visual" value = "' . $val . '">' . $val . '</button>';
                    }
                    $gehadbc[$val] = true;
                }
            }
            //$knoppen_bc .= '<button name = "client_id" class = "kind_knop_nw" value = "client_id">nieuwe analyse</button>';
        }
        if (key_exists('bouwstenen', $this->clienten)) {

            if ($this->clienten['bouwstenen']) {
                //
                foreach ($this->clienten['bouwstenen'] as $val) {
                    if (! array_key_exists($val, $gehadbs)) {
                        $knoppen_bs .= '<button class = "kind_knop" name = "visual" value = "' . $val . '">' . $val . '</button>';
                    }
                    $gehadbs[$val] = true;
                }
            }
        }
        //$knoppen_bs .= '';
        $plugindir = aplugin_dir_url();
        $knoppen2  = '<button name = "wijzig" class = "kind_knop_nw" value = "' . $this->resp . '">kindgegevens wijzigen</button>';

        $output .= '    <div class="client_wrapper"><div id = "hoofder" class = "hoofder">' . $logo . '</div></div>';
        $output .= '<form action="#" method="post">';
        $output .= '    <div class="client_wrapper"><input type = "hidden" name = "variant" value = 2>';
        $output .= '        <div id = "hoofder" class = "hoofder"><table>
                            <tr rowspan = "2">
                                <td width = "80px"><img width = "80px" src = "' . $plugindir . 'images/logo_bc.png"></td>
                                <td><h1 class = "soorth1">basiscommunicatie</h1><td width = "130px">
                                <button title = "analyse basiscommunicatie voor nieuw kind toevoegen" name = "client_id" class = "plusknop" value = "client_id">+</button>
                                <button title = "kindgegevens wijzigen" name = "wijzig" class = "plusknop" value = "bc">&#x1F589;</button></td>
                            </tr>
                            <tr><td colspan = "3">' . $knoppen_bc . '</td></tr></table>
                            </div>';
        $output .= '    </div>';
        $output .= '</form>';
        $output .= '<form action="#" method="post">';
        $output .= '    <div class="client_wrapper"><input type = "hidden" name = "variant" value = 1>';
        $output .= '        <div id = "hoofder" class = "hoofder"><table>
                            <tr rowspan = "2">
                                <td width = "80px"><img width = "80px" src = "' . $plugindir . 'images/logo_bs.png"></td>
                                <td><h1 class = "soorth1">bouwstenen voor de hechting</h1>
                                <td width = "130px"><button title = "analyse bouwstenen voor nieuw kind toevoegen" name = "client_id" class = "plusknop" value = "client_id">+</button>
                                <button title = "kindgegevens wijzigen" name = "wijzig" class = "plusknop" value = "bs">&#x1F589;</button></td>
                            </tr>
                            <tr><td colspan = "3">' . $knoppen_bs . '</td></tr></table>
                            </div>';
        $output .= '    </div>';
        $output .= '</form>';
        $output .= '</br></br></br>';

        return $output;
        //$this->haal_L_en_G($resp);
    }

    public function get_interface()
    {
    }
    public function haal_clienten()
    {
        $db = new BsDb();
        $db->sluitopen($this->resp);
        $this->clienten = $db->get_clients($this->resp);
    }

    public function maakFooter()
    {
        //return '</html>';
    }
    public function MaakSelectBoxes($client_id)
    {
        //ts($GLOBALS['variant']);
        if ($client_id) {
            $this->prefildat = $this->haal_data($_GET["client_id"]);

            //ts($this->prefildat);
            //ts($_GET["client_id"]);
            $frasecl = ' value="' . $client_id . '" readonly ';

        } else {
            $this->prefildat = '';
            $frasecl         = ' placeholder="geef hier bijvoorbeeld het registratienummer uit je systeem" ';

        }
        $uit = '<h2>Je gaat de vragenlijst invullen voor:</h2></br>
                <form id="naar_bsq" action="#" method="post">';

        //identificatie kind
        $uit .= '   <label for="idkind" class = "ag_onderwerp">Identificatie kind: </label>
                    <input required type="text" id="idkind" name="idkind"' . $frasecl . '>';

        //observatiedatum
        $uit .= '   <label for="observatie_datum" class = "ag_onderwerp">Observatiedatum:</label>
        <input placeholder = "dd-mm-yyyy" value = "' . date('Y-m-d') . '" type="date" id="observatie_datum" name="observatie_datum">';

        //leeftijd (in maanden)
        $uit .= '<div class = "ag_onderwerp"></br>Leeftijd kind:</div>
                    <table><tr><td><input required type="number" min=0 max=18 id="LeeftijdJaar" name="LeeftijdJaar" placeholder="jaren">';
        $uit .= '   </td><td><input required type="number" min=0 max=11 id="LeeftijdMaand" name="LeeftijdMaand" placeholder="maanden"></td></tr></table>';

        //type ondersteuning
        $uit .= '<div class = "ag_onderwerp">Type video-ondersteuning:</div>
                        <input required type="radio" id="vib" name="video">
                        <label for="vib" class = "ag_label">VIB</label>
                        <input type="radio" id="vht" name="video" value="vht">
                        <label for="vib" class = "ag_label">VHT of K-VHT</label>';

        //werksector
        $uit .= '<div class = "ag_onderwerp"></br>Werksector:</div>
                        <select required id="WerksoortSelect" name = "WerksoortSelect">
                            <option value="" class="rhth">-- selecteer werksector --</option>';

        foreach ($this->werksoorten as $id => $naam) {
            $uit .= '<option value="' . $id . '" class = "c' . $id . '">' . $naam . '</option>';
        }
        $uit .= '</select>';

        //versie
        if ($GLOBALS['variant'] < 2) {
            $uit .= '   <div class = "ag_onderwerp"></br>Versie vragenlijst:</div>
                    <input required type="radio" id="versie" name="versie" value="normaal" checked="checked">
                    <label for="vib" class = "ag_label">normale lengte</label>
                    <input type="radio" id="versie" name="versie" value="kort">
                    <label for="vib" class = "ag_label">verkort</label>';

        }
        // door naar nabijheid
        $uit .= '<input type="hidden" id="nabijheid" name="nabijheid" value="ja">';
        $uit .= '<input type="hidden" id="variant" name="variant" value=' . $GLOBALS['variant'] . '>';

        //knop
        $uit .= '   </br><button typevalue = "vragenlijst" id="startSm">vul de vragenlijst in</button></p>
                    </form>';
        //ts($uit);
        return $uit;
    }
    public function Maak_keuze_menu($vari)
    {
        //ts($vari);
        //$output = '<link rel="stylesheet" type="text/css" href="' . $this->plugindir . 'css/ppibstyles.css">';
        $output = '<link rel="stylesheet" type="text/css" href="' . $this->plugindir . 'css/bs.css">';
        //$output = $this->get_style();
        //ts($vari);
        //$output .=
        $output .= '<script>var prefildat = ' . json_encode($this->prefildat) . ';';
        $output .= '</script>';

        $ShBS = new SharedBS();
        //$logo = $ShBS->maak_logo($vari);
        $logo = $ShBS->maak_logo_vis_2();
        $output .= '<div class="form_wrapper">';
        $output .= '    <div class="hoofder">';
        $output .= $logo;
        $output .= '    </div>';
        $output .= '    <div class = "alg_toel"">';
        $output .= $this->selectBoxes;
        $output .= '    </div>';
        $output .= '</div>';
        $output .= $this->get_biebs();
        return $output;
        //$this->haal_L_en_G($resp);
    }
    public function haal_eerdere_metadata()
    {
        $db             = new BsDb();
        $this->metadata = $db->get_open_data();
    }
    public function haal_Ws_en_Lft()
    {
        $db                = new BsDb();
        $this->werksoorten = $db->get_ws();
        //$this->leeftijden = $db->get_lft();
    }
    public function haal_data($a)
    {
        $db = new BsDb();
        return $db->get_open_data($a);
    }
    public function get_biebs()
    {
        //$this->plugindir = plugin_dir_url();

        $output = "<script type='text/javascript' src='" . $this->plugindir . "js/prefilform.js'></script>";
        return $output;
    }

}
