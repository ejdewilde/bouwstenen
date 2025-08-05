<?php

ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 'Off');

class Bs_Quest
{
    public function __CONSTRUCT()
    {
        //ts('questionnaire');
        $WpDbQ = new BsDb();

        $this->verwerk($_POST);
        $this->mee = $_POST;

        //$this->mee["archief"] = $this->archief;
        $this->plugindir = aplugin_dir_url();

        $this->DataArr = array();
        if (array_key_exists('visual', $_POST))
        {
            $this->DataArr = $WpDbQ->haalData($_POST["visual"]);
        }
        //ts($this->DataArr);
        $this->ItemsArr = array();
        $this->data = array();
        $this->resp = aget_current_user_id();
        //$this->plugindir = plugin_dir_url(__FILE__);
        //ts($this->CatsArr);
        $tel = 0;
        switch ($GLOBALS["variant"])
        {
            case 1:
                $this->CatsArr = $WpDbQ->haalCats($this->leeftijd_in_maanden);
                foreach ($this->CatsArr as $cat)
                {

                    $this->ItemsArr[$cat] = $WpDbQ->haalItemsVoorCat($cat, $this->versie, $GLOBALS["variant"]);
                    $this->data[$tel]["label"] = $cat;
                    $this->data[$tel]["aantalitems"] = count($this->ItemsArr[$cat]);
                    $tel++;
                }
                break;
            case 2:
                $this->CatsArr = $WpDbQ->haalCatsBC();
                //$this->CatsArr[] = 'overig';
                //ts($this->CatsArr);
                foreach ($this->CatsArr as $cat)
                {
                    $this->versie = 0;

                    $this->ItemsArr[$cat] = $WpDbQ->haalItemsVoorCat($cat, 0, $GLOBALS["variant"]);
                    $this->data[$tel]["label"] = $cat;
                    $this->data[$tel]["aantalitems"] = count($this->ItemsArr[$cat]);
                    $tel++;
                }
                break;
        }
        //ts($this->data);

    }

    public function verwerk($aa)
    {
        //ts($aa);
        $this->ident = $aa["idkind"];
        //$this->variant = $aa["variant"];
        $this->soort = $aa["video"];
        $jaar = $aa["LeeftijdJaar"];
        $maand = $aa["LeeftijdMaand"];

        $tekst = $jaar . ' jaar';
        if ($maand > 1)
        {
            $tekst .= ' en ' . $maand . ' maanden';
        }
        elseif ($maand > 0)
        {
            $tekst .= ' en ' . $maand . ' maand';
        }
        $this->lft = $tekst;
        if (is_null($maand))
        {
            $mnd = 0;
        }
        else
        {
            $mnd = $maand;
        }
        $this->leeftijd_in_maanden = 12 * $jaar + $mnd;

        //kort of lang? 
        switch ($GLOBALS["variant"])
        {
            case 1:
                $this->versie = $aa["versie"];
                break;
            case 3:
                $this->versie = 1;
                break;
            case 4:
                $this->versie = 0;
                break;
        }

    }
    public function get_interface()
    {
        $site_url = aget_site_url();
        $output = $this->get_style();
        $output .= $this->get_biebs();

        $output .= $this->maak_Meet_div_header($GLOBALS["variant"]);
        $output .= $this->maak_Orbits();
        $output .= $this->maak_Meet_div_footer();
        $effe = 'var meta = ' . json_encode($this->mee) . ';';
        $effe .= 'var variant = ' . $GLOBALS["variant"] . ';';
        $output .= '
            <script>
            ' . $effe . '
                $(document).ready(function() {
                    $(document).foundation();
                })
            </script>';

        echo $output;
    }
    public function maak_Meet_div_header($variant)
    {
        $ShBS = new SharedBS();
        //$logo = $ShBS->maak_logo($variant);
        $logo = $ShBS->maak_logo_vis_2();
        //ts($variant);
        $term = "bouwsteen";
        if ($variant > 1)
        {
            $term = "aspect van basiscommunicatie";
        }

        $uit = '
        <div class="client_wrapper">
            <div id = "hoofder" class = "hoofder">' . $logo . '<h1>' . $this->ident . ' (leeftijd: ' . $this->lft . ')</h1></div>
            <div class="alg_toel">
                <p>Gebruik de knoppen per categorie om een ' . $term . ' te selecteren. Geef aan of de beschrijving links of rechts het meest overeenkomst met het gedrag van het kind. Navigeer met de pijlen door naar het volgende item.</p>
                <svg id = "knoppen"></svg>

                <div id = "kop"></div>
                <b>Het kind...</b>
                <form action="" method = "post"><input type = "hidden" name = "variant" value = ' . $variant . '>';
        return $uit;
    }
    public function maak_Meet_div_footer()
    {
        $uit = '</form>
            <div class="result"></div>
            <svg id = "resultaatknop"></svg>
            </div>
        </div>';
        $data = json_encode($this->data);
        $uit .= "<script>var data = " . $data . ";</script>";
        //$effe[0]["itemid"] = null;
        //$effe[0]["value"] = 999;
        if ($this->DataArr)
        {
            $open_data = json_encode($this->DataArr);
            $uit .= "<script>var open_data = " . $open_data . ";
                             var nieuwe_vragenlijst=false;</script>";
        }
        else
        {
            $uit .= "<script>var nieuwe_vragenlijst=true;</script>";
        }
        //ts($open_data);

        $uit .= "<script type='text/javascript' src='" . $this->plugindir . "js/bsq.js'></script>";

        return $uit;
    }
    public function maak_Orbits()
    {
        $uit = '';
        foreach ($this->CatsArr as $key => $val)
        {
            $uit .= $this->maak_Orbit($val, $key);
        }
        return $uit;
    }
    public function maak_Orbit($cat, $id)
    {
        //ts($this->ItemsArr[$cat]);
        $output = '

        <div id = "orb_' . $id . '" class = "orbit" role = "region" data-orbit data-use-m-u-i = "false" data-auto-play = "false" data-swipe = "true">
        <ul class = "orbit-container" >';

        foreach ($this->ItemsArr[$cat] as $val)
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

            $output .= '
                <li class="orbit-slide">
            		<div class="columns medium6WhiteboxLeft">
					    <div class = "item_formulering">' . $links . '</div>
					</div>
					<div class="columns medium6WhiteboxRight">
					    <div class = "item_formulering">' . $rechts . '</div>
                    </div>
                    <div id = "itemcatdiv">
				        <label class = "itemcat"><input id="imgId' . $val["itemid"] . '1" type="radio" class = "rad_' . $id . '" name="itemId' . $val["itemid"] . '" value="1"><img class = "rad_' . $id . '" src="' . $this->plugindir . 'images/meestal.svg" /></label>
                        <label class = "itemcat"><input id="imgId' . $val["itemid"] . '2" type="radio" class = "rad_' . $id . '" name="itemId' . $val["itemid"] . '" value="2"><img class = "rad_' . $id . '" src="' . $this->plugindir . 'images/vaak.svg" /></label>
                        <label class = "itemcat"><input id="imgId' . $val["itemid"] . '3" type="radio" class = "rad_' . $id . '" name="itemId' . $val["itemid"] . '" value="3"><img class = "rad_' . $id . '" src="' . $this->plugindir . 'images/soms.svg" /></label>
                        <label class = "itemcat"><input id="imgId' . $val["itemid"] . '4" type="radio" class = "rad_' . $id . '" name="itemId' . $val["itemid"] . '" value="4"><img class = "rad_' . $id . '" src="' . $this->plugindir . 'images/vaak.svg" /></label>
                        <label class = "itemcat"><input id="imgId' . $val["itemid"] . '5" type="radio" class = "rad_' . $id . '" name="itemId' . $val["itemid"] . '" value="5"><img class = "rad_' . $id . '" src="' . $this->plugindir . 'images/meestal.svg" /></label>
                    </div>
                </li>';
        }
        $output .= '
                <div>
                    <button class = "orbit-previous" aria-label = "previous"><span class = "show-for-sr">Vorige item</span>◀</button>
                    <button class = "orbit-next" aria-label = "next"><span class = "show-for-sr">Volgende Item</span>▶</button>
                </div>';
        $output .= ' </ul>';
        $output .= $this->maak_Nav($cat);

        $output .= '</div>';
        return $output;
    }

    public function maak_Nav($cat)
    {
        $aantal_items = count($this->ItemsArr[$cat]);

        $output = '<nav class = "orbit-bullets">
        <button class = "is-active" data-slide = "0">
           <span class = "show-for-sr">First slide</span>
           <span class = "show-for-sr">Current Slide</span>
        </button>';
        for ($i = 1; $i < $aantal_items; $i++)
        {
            $output .= '<button data-slide = "' . $i . '">
                <span class = "show-for-sr">slide ' . $i . '</span>
            </button>';
        }

        $output .= '</nav>';
        return $output;

    }
    public function get_style()
    {
        $output = '<link rel="stylesheet" id="bs-style-css" href="' . $this->plugindir . 'css/bs.css" type="text/css">';
        $output .= '<link rel="stylesheet" id="bsq-style-css" href="' . $this->plugindir . 'css/bsq.css" type="text/css">';
        $output .= '<link rel="stylesheet" id="bsp-style-css" href="' . $this->plugindir . 'css/print.css" type="text/css" media="print">';
        //$output .= '<link rel="stylesheet" id="bsp-found-css" href="' . $this->plugindir . 'css/foundation.css" type="text/css">';
        return $output;

    }
    public function get_biebs()
    {
        //$output = "<script type='text/javascript' src='" . $this->plugindir . "js/loader.js'></script>";
        $output = "<script type='text/javascript' src='" . $this->plugindir . "js/jquery-1.12.4.js'></script>";
        $output .= "<script type='text/javascript' src='" . $this->plugindir . "js/foundation.js'></script>";
        //$output .= "<script type='text/javascript' src='" . $this->plugindir . "js/foundation.orbit.js'></script>";
        $output .= "<script type='text/javascript' src='" . $this->plugindir . "js/d3.v4.min.js'></script>";
        return $output;
    }
}