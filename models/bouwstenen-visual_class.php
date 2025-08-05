<?php

//ini_set('display_errors', 0);
error_reporting(E_ERROR);
ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
/*
bouwstenen visualisatie
 */

function ts($test)
{ // for debug/development only
    echo '<pre>';
    echo print_r($test);
    echo '</pre>';
} //include_once("interfaceDB.php");

class Bs_Visual
{
    public function __CONSTRUCT()
    {
        //ts($_POST);
        //exit;

        if ($_POST) {
            if ($_POST["visual"]) {
                $cli = $_POST["visual"];
            }
        } elseif ($_GET) {
            if ($_GET["visual"]) {
                $cli = $_GET["visual"];
            }
        } else {
            exit;
        }
        //ts( $GLOBALS['variant']);
        $db = new BsDb();
        $resp = aget_current_user_id();
        $this->plugindir = aplugin_dir_url();
        //ts($resp);
        //$resp = 2170;
        //$this->data = $this->test_array();
        //$this->oude_data = $this->test_array_oud();
        $effe = $db->get_existing_data($resp, $cli);
        //ts($effe);
        //exit;
        $this->data_bs = $effe[0];
        $this->d3_data_bs = $effe[1];
        $this->data_bc = $effe[2];
        $this->d3_data_bc = $effe[3];
        $this->data_na = $effe[6];

        if ($GLOBALS["variant"] > 1) {
            $this->meetmomenten = $effe[5];
        } else {
            $this->meetmomenten = $effe[4];
        }

        $this->toelichtingen = $db->get_toelichtingen();
        $this->item_toelichtingen_bs = $db->get_item_toelichtingen($resp, $cli, 'bs');
        $this->item_toelichtingen_bc = $db->get_item_toelichtingen($resp, $cli, 'bc');
        $this->maak_trend_data();

        //berekenen
        $post = true;
        $this->kind_id = $cli;
        $this->uitput = $this->publiceer_uitkomst();
    }

    public function maak_trend_data()
    {
        if ($GLOBALS['variant'] > 1) {
            $data = $this->data_bc;
            $cats = ['initiatief en ontvangst', 'uitwisseling in de kring', 'overleg', 'conflict hanteren'];
            $ncats = 4;
            $tussen = 'bc';
        } else {
            $data = $this->data_bs;
            $cats = ['basisveiligheid', 'toevertrouwen', 'zelfvertrouwen', 'zelfstandigheid', 'creativiteit'];
            $ncats = 5;
            $tussen = 'bs';
        }

        $serie = array();
        $dags = array();
        $titel = $data['kind_id'];
        $tel1 = 0;
        //ts($GLOBALS['variant']);
        //ts($cats);
        //ts($this->data_bs);
        //ts($tussen);
        //ts($data['data']['2023-04-24'][$tussen]);
        $series = array();

        foreach ($data['data'] as $dag => $waarde) {
            $dagen[] = $dag;
        }
        //ts($dagen);
        //ts($cats);

        $tel1 = 0;
        foreach ($cats as $cat) {
            $effe = array();
            foreach ($dagen as $dag) {
                if (key_exists($cat, $data['data'][$dag][$tussen])) {
                    if ($GLOBALS['variant'] > 1) {
                        //ts('ok');
                        $val = ($data['data'][$dag][$tussen][$cat]['v'] - 1) * 25;
                    } else {
                        $val = $data['data'][$dag][$tussen][$cat]['v'];
                    }
                    array_push($effe, $val);
                } else {
                    array_push($effe, null);
                }
            }
            //ts($effe);
            $serie[$tel1]['name'] = $cats[$tel1];
            $serie[$tel1]['data'] = $effe;
            $tel1++;
        }

        //round(100 * (bc_filt[i].score - 1) * 20) / 100 + "%";

        $this->series = $serie;
        $this->cats = $dagen;
        //ts($data['data']);
        //ts($serie);
        //ts($dags);
        //ts($data);
    }

    public function maak_hc_data()
    {
        //ts($this->data);
        $cats = ['basisveiligheid', 'toevertrouwen', 'zelfvertrouwen', 'zelfstandigheid', 'creativiteit'];
        $kleur = ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5'];
        $this->series = array();
        //$this->titel = $this->data['kind_id'];
        //$this->subtitel = 'gescoord door: ' . $this->data['observator'];
        $this->cats = $cats;
        $serie = array();
        foreach ($this->data['data'] as $dag => $meting) {
            $serie['name'] = $dag;
            $serie['visible'] = false;
            $serie['lineWidth'] = 0;
            $serie['pointPlacement'] = 'on';
            $data = array();
            for ($m = 0; $m < 5; $m++) {
                foreach ($meting as $schaal => $waarde) {
                    if ($cats[$m] == $schaal) {
                        $data[$m]['y'] = round($waarde, 1);
                        $data[$m]['color'] = $kleur[$m];
                        $data[$m]['cat'] = $cats[$m];
                    }
                }
            }
            $serie['data'] = $data;
            $this->series[] = $serie;
        }
        $this->series[0]['visible'] = true;
    }

    public function get_interface()
    {
        $site_url = aget_site_url();
        $output = $this->get_style();
        $output .= $this->get_biebs();
        $output .= $this->uitput;
        echo $output;

    }
    public function publiceer_uitkomst()
    {
        $output = $this->publiceer_blokken();
        $output .= $this->publiceer_trend();
        return $output;
    }
    public function publiceer_blokken()
    {
        $data_bs = json_encode($this->d3_data_bs);
        $data_bc = json_encode($this->d3_data_bc);
        $data_na = json_encode($this->data_na);
        $kind = json_encode($this->kind_id);
        $toel = json_encode($this->toelichtingen);
        $item_toel_bs = json_encode($this->item_toelichtingen_bs);
        $item_toel_bc = json_encode($this->item_toelichtingen_bc);

        $ShBS = new SharedBS();
        $logo = $ShBS->maak_logo_vis($this->kind_id);

        $output = "<div id = 'printstuk' class = 'vis_wrapper'>";
        switch ($GLOBALS['variant']) {
            case 1:
                $output .= "    <div id = 'printhoofder' class = 'hoofder'><h1>Bouwstenen voor de hechting</h1></div>";
                break;
            case 2:
                $output .= "    <div id = 'printhoofder' class = 'hoofder'><h1>Basiscommunicatie</h1></div>";
                break;
        }

        $output .= "    <div id = 'hoofder' class = 'hoofder'>" . $logo . "</div>";
        $output .= "    <div id = 'alg_toel' class = 'alg_toel'></br></div>";

        $output .= "    <div id = 'kind_balk' class = 'kind_balk'>";
        $output .= "        <div id = 'kind_id'></div>";
        $output .= "    </div>";

        $output .= "    <div id = 'datums' class = 'datums'>
                            <h2>meetmomenten</h2>
                            <svg id = 'datumknoppen'></svg>
                        </div>";

        if ($GLOBALS['variant'] == 1) {
            $output .= "    <div class = 'blok_bs'>";
            $output .= "        <div>
                                <h3>bouwstenen</h3>
                                <svg id = 'bouwstenen'></svg>
                            </div>";
            $output .= "    </div>";
            $output .= "    <div id = 'bs_Ztoel' class = 'bs_toel'>
                            <table>
                                <tr>
                                    <td width = '50%'><div id = 'kop_bs_toel'></div></td>
                                    <td align = 'right'><svg id = 'stop_bs'></svg></td>
                                </tr>
                            </table>
                            <div id = 'text_bs_toel'></div>
                        </div>";
        }
        if ($GLOBALS['variant'] == 2) {
            $output .= "    <div class = 'blok_bc'>";
            $output .= "        <div>
                                <h3>basiscommunicatie</h3></br>
                                <svg id = 'basiscommunicatie'></svg>
                            </div>";
            $output .= "    </div>";
            $output .= "    <div id = 'bc_Ztoel' class = 'bc_toel'>
                            <table>
                                <tr>
                                    <td width = '50%'><div id = 'kop_bc_toel'></div></td>
                                    <td align = 'right'><svg id = 'stop_bc'></svg></td>
                                </tr>
                            </table>
                            <div id = 'text_bc_toel'></div>
                        </div>";
        }
        //if (array_key_exists('nabijheid', $this->d3_data_bs[0]))
        //{
        $output .= "    <div class = 'blok_na'>";
        $output .= "        <div>
                                <h3>contact</h3></br>
                                <svg id = 'nabijheid'></svg>
                                </div>";
        $output .= "    </div>";
        $output .= "    <div id = 'na_toel' class = 'na_toel'></div>";
        //}
        //ts($data);
        //$output .= "</div>";
        $output .= "<script>";

        $output .= "var meetmomenten = " . json_encode($this->meetmomenten) . ";";
        $output .= "var data_bs = " . $data_bs . ";";
        $output .= "var data_bc = " . $data_bc . ";";
        $output .= "var data_na = " . $data_na . ";";
        $output .= "var variant = " . $GLOBALS['variant'] . ";";
        $output .= "var kind_id = " . $kind . ";";
        $output .= "var toel = " . $toel . ";";
        $output .= "var item_toel_bs = " . $item_toel_bs . ";";
        $output .= "var item_toel_bc = " . $item_toel_bc . ";";
        $output .= "</script>";
        $output .= "<script type='text/javascript' src='" . $this->plugindir . "js/bs.js'></script>";
        return $output;
    }
    public function publiceer_spin()
    {
        $chart = new BsSpin();
        $output = "    <div class = 'blok_hc'>";
        $output .= "        <div>
                                <h3>bouwstenen door de tijd</h3>
                                <div id = 'spin'></div>
                            </div>";
        $output .= "    </div>";
        $output .= "    <div id = 'hc_toel' class = 'hc_toel'></div>";
        $output .= "</div>";
        $output .= '<script>d3.select("#hc_toel").html("<h2>" + toel[103].titel + "</h2></br>" + toel[103].inhoud);</script>';
        //ts($this->series);
        //ts($this->cats);

        //$output .= $chart->maak_sm_bar('stab_per_kind', $ki, $this->cats, $this->groep_naam);
        $output .= $chart->maak_spin($this->titel, $this->cats, $this->series);
        return $output;
    }
    public function publiceer_trend()
    {
        include_once "bouwstenen-trend_class.php";
        $chart = new BsTrend();
        if ($GLOBALS["variant"] > 1) {
            $titel = "basiscommunicatie";
            $textid = 106;
            $yas = 'percentage';
            $ymax = 100;
            $ymin = 0;
        } else {
            $titel = "bouwstenen";
            $textid = 103;
            $yas = 'gemiddelde score';
            $ymax = 5;
            $ymin = 1;
        }
        $output = "    <div class = 'blok_hc'>";
        $output .= "        <div>
                                <h3>" . $titel . " door de tijd</h3>
                                <div id = 'trendfig'></div>
                            </div>";
        $output .= "    </div>";
        $output .= "    <div id = 'hc_toel' class = 'hc_toel'></div>";
        $output .= "</div>";
        $output .= '<script>d3.select("#hc_toel").html("<h2>" + toel[' . $textid . '].titel + "</h2></br>" + toel[' . $textid . '].inhoud);</script>';
        //ts($this->series);
        //ts($this->cats);

        //$output .= $chart->maak_sm_bar('stab_per_kind', $ki, $this->cats, $this->groep_naam);
        //maak_trend_figuur($series, $cats, $plek, $org)
        $output .= $chart->maak_trend_figuur($this->series, $this->cats, 'trendfig', $yas, $ymax, $ymin);
        return $output;
    }
    public function filter_data($array, $wie)
    {
        $terug = array();
        switch ($wie) {
            case "ki":
                foreach ($array as $key => $value) {
                    if ($key < 100) {
                        $terug[$key] = $value;
                    }
                }
                break;
            case "pm":
                foreach ($array as $key => $value) {
                    if ($key > 99) {
                        $terug[$key] = $value;
                    }
                }
                break;

        }
        return $terug;
    }
    public function schoon($hier)
    {
        $daar = str_replace("é", "&egrave;", $hier);
        return $daar;
    }
    public function get_style()
    {
        $output = '<link rel="stylesheet" id="sm-style-css" href="' . $this->plugindir . 'css/bs.css" type="text/css">';
        $output .= '<link rel="stylesheet" id="smp-style-css" href="' . $this->plugindir . 'css/print.css" type="text/css" media="print">';
        $output .= '<link rel="stylesheet" id="smp-style-css2" href="' . $this->plugindir . 'css/print2.css" type="text/css">';
        return $output;

    }
    public function get_biebs()
    {
        $output = "<script type='text/javascript' src='" . $this->plugindir . "js/jquery-1.12.4.js'></script>";
        $output .= "<script type='text/javascript' src='" . $this->plugindir . "js/d3.v4.min.js'></script>";
        $output .= '<script src="https://code.highcharts.com/highcharts.js"></script>';
        $output .= '<script src="https://code.highcharts.com/highcharts-more.js"></script>';
        $output .= "<script type='text/javascript' src='" . $this->plugindir . "js/print.js'></script>";

        return $output;
    }
}
