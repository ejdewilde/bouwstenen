<?php

ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 'Off');
class Bs_Nabij
{
    public function __CONSTRUCT()
    {
        //ts('nabijheid');
        $WpDbQ = new BsDb();
        $this->verwerk($_POST);
        $this->plugindir = aplugin_dir_url();
        //ts($_POST);
        //$this->variant = 3;
        $this->variant = $GLOBALS["variant"];

    }
    public function verwerk($aa)
    {
        $this->idkind = $aa["idkind"];
        $this->video = $aa["video"];

        //$this->variant = $aa["variant"];
        $this->LeeftijdJaar = $aa["LeeftijdJaar"];
        $this->LeeftijdMaand = $aa["LeeftijdMaand"];
        $this->WerksoortSelect = $aa["WerksoortSelect"];
        $this->observatie_datum = $aa["observatie_datum"];
        if ($GLOBALS["variant"] < 2)
        {
            $this->versie = $aa["versie"];
        }
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
    }
    public function get_interface()
    {
        $site_url = aget_site_url();
        $output = $this->get_style();
        $output .= $this->get_biebs();

        $output .= $this->maak_Meet_div_header($this->variant);
        $output .= $this->maak_Meet_div_footer();
        $output .= '
            <script>
                $(document).ready(function() {
                    $(document).foundation();
                })
            </script>';

        echo $output;
    }
    public function maak_slider_regel($num)
    {
        $uit = '
        <div id = "regel_' . $num . '" class = "inputregel">
        <div class = "kolom1"><input oninput = "doerij(' . $num . ')" type="text"  placeholder = "naam" name = "naam_' . $num . '" id = "naam_' . $num . '"></div>
        <div class = "kolom2"><input type="range" min="1" max="100" value="50" class="slider" id="slide_' . $num . '" name="slide_' . $num . '"></div>
        </div>';
        return $uit;

    }
    public function maak_Meet_div_header()
    {
        $ShBS = new SharedBS();
        //$logo = $ShBS->maak_logo($this->variant);
        $logo = $ShBS->maak_logo_vis_2();
        $uit = '
        <div class="client_wrapper">
            <div id = "hoofder" class = "hoofder">' . $logo . '<h1>' . $this->idkind . ' (leeftijd: ' . $this->lft . ')</h1></div>
            <div class="alg_toel">
            <div id = "kop">Beleving van het contact met uw kind</div>
                <p>Kunt u aangeven hoe u het contact met uw kind(eren) ervaart?</p>
                <p>Geef in het linkerveld uw naam en geef daarna met de schuif aan in hoeverre u goed of minder goed contact maakt en in welke mate u uw kind (goed) begrijpt.
                Voeg hierna eventueel ook andere (gezins)leden toe en geef dit daarna ook voor hen aan. Wanneer u klaar bent, selecteer dan "ga verder" om door te gaan met de bouwstenenvragenlijst.</p>

                <form action="#" method = "post">';

        $uit .= '
                <table id = "nabij">
                <tr><th width = "100px"></th><th class = "thlinks">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;heel goed</th><th class = "threchts">niet goed&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th></tr>
                    <tr>
                        <td>
                            <input size = "20"  placeholder = "naam" type="text" name = "naam_0" id = "naam_0">
                        </td>
                        <td colspan = "2">
                            <input type="range" min="1" max="100" value="50" class="slider" id="slide_0" name="slide_0">
                        </td>
                        <td width = "35px">
                            <button type = "button" onclick = "voegrijtoe()" title = "voeg persoon toe" name = "nabij" class = "plusknop" value = "">+</button>
                        </td>
                    </tr>
                </table>';
        /*
        for ($x = 1; $x < 11; $x++) {
        $uit .= $this->maak_slider_regel($x);
        }
        */
        $uit .= '<input type="hidden" name="vragenlijst" value="ja">';
        $uit .= '<input type="hidden" name="variant" value="' . $this->variant . '">';
        $uit .= '<input type="hidden" name="idkind" value="' . $this->idkind . '">';
        $uit .= '<input type="hidden" name="video" value="' . $this->video . '">';
        $uit .= '<input type="hidden" name="LeeftijdJaar" value="' . $this->LeeftijdJaar . '">';
        $uit .= '<input type="hidden" name="LeeftijdMaand" value="' . $this->LeeftijdMaand . '">';
        if ($this->variant < 2)
        {
            $uit .= '<input type="hidden" name="versie" value="' . $this->versie . '">';
        }
        $uit .= '<input type="hidden" name="observatie_datum" value="' . $this->observatie_datum . '">';
        $uit .= '<input type="hidden" name="WerksoortSelect" value="' . $this->WerksoortSelect . '">';
        $uit .= '<div class = "knopregel"><button class = "verder_knop" value = "video">ga verder</button></div>';

        return $uit;
    }
    public function maak_Meet_div_footer()
    {
        $uit = '</form>
            </div>
        </div>';

        return $uit;
    }
    public function get_style()
    {
        $output = '<link rel="stylesheet" id="bs-style-css" href="' . $this->plugindir . 'css/bs.css" type="text/css">';
        $output .= '<link rel="stylesheet" id="bsq-style-css" href="' . $this->plugindir . 'css/bsq.css" type="text/css">';
        $output .= '<link rel="stylesheet" id="bsp-style-css" href="' . $this->plugindir . 'css/print.css" type="text/css" media="print">';
        return $output;

    }
    public function get_biebs()
    {
        $output = "<script type='text/javascript' src='" . $this->plugindir . "js/jquery-1.12.4.js'></script>";
        $output .= "<script type='text/javascript' src='" . $this->plugindir . "js/foundation.js'></script>";
        $output .= "<script type='text/javascript' src='" . $this->plugindir . "js/d3.v4.min.js'></script>";
        $output .= "<script type='text/javascript' src='" . $this->plugindir . "js/bsna.js'></script>";
        return $output;
    }
}