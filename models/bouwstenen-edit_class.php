<?php
/* ------------------------------------------------
Editen clientgegevens
ait bouwstenen plugin
------------------------------------------------ */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class BS_edit
{

    public function __CONSTRUCT()
    {
        //echo plugins_url();
        $this->plugindir = aplugin_dir_url();
        $this->resp = aget_current_user_id();
        $this->haal_clienten();
        $this->maak_client_knopjes();
        //ts($this->clienten);
        //ts($_POST);
        if ($_POST)
        {
            //verwijderen
            if (array_key_exists("verwijder_id", $_POST))
            {
                $this->client = $_POST["nieuw_id"];
                $this->uitput = $this->verwijderClient($_POST);
            }
            elseif (array_key_exists("verwijder_id_open", $_POST))
            {
                $this->client = $_POST["nieuw_id"];
                //ts('joehoe');
                $this->uitput = $this->verwijderClient_open($_POST);
            }

            //wijzigen
            elseif (array_key_exists("wijzig_client", $_POST))
            {
                //id wijzigen
                if ($_POST["wijzig_client"] == "id")
                {
                    $this->client_nw = $_POST["nieuw_id"];
                    $this->client_oud = $_POST["oud_id"];
                    $this->uitput = $this->wijzigIdClient($_POST);
                }
                //lijst clienten om te wijzigen weergeven
                else
                {
                    $this->client = $_POST["wijzig_client"];
                    $this->uitput = $this->wijzigClient($_POST);
                }
            }
            else
            {
                $this->uitput = $this->start();
            }

        }
    }
    public function verwijderClient()
    {
        $db = new BsDb();
        $ShBS = new SharedBS();

        $zql = 'update meten_response_data set zichtbaar = 0 where uid_respondent = "' . $this->resp . '" and client_id = "' . $this->client . '";';

        $db->exesql($zql);
        $ShBS->log_het($zql, 'edit');
        //na wijzigen nieuwe clientenlijst
        $this->haal_clienten();
        $this->maak_client_knopjes();
        $output = '        <div id = "instructie" class = "instructie">gegevens van <b>' . $this->client . '</b> zijn verwijderd.</div>';
        $output .= '       <div id = "alg_toel" class = "alg_toel">' . $this->knoppen . '</div>';

        return $output;
    }
    public function verwijderClient_open()
    {
        $db = new BsDb();
        $ShBS = new SharedBS();

        $zql = 'delete from meten_response_data where archief is null and uid_respondent = "' . $this->resp . '" and client_id = "' . $this->client . '";';

        $db->exesql($zql);
        $ShBS->log_het($zql, 'edit');
        //na wijzigen nieuwe clientenlijst
        $this->haal_clienten();
        $this->maak_client_knopjes();
        $output = '        <div id = "instructie" class = "instructie">openstaande meting van <b>' . $this->client . '</b> is verwijderd.</div>';
        $output .= '       <div id = "alg_toel" class = "alg_toel">' . $this->knoppen . '</div>';

        return $output;
    }

    public function wijzigIdClient()
    {
        $db = new BsDb();
        $ShBS = new SharedBS();
        $zql = 'update meten_response_data set client_id = "' . $this->client_nw . '" where uid_respondent = "' . $this->resp . '" and client_id = "' . $this->client_oud . '";';
        $zqlna = 'update nabijheids_data set client_id = "' . $this->client_nw . '" where uid_respondent = "' . $this->resp . '" and client_id = "' . $this->client_oud . '";';

        $db->exesql($zql);
        $ShBS->log_het($zql, 'edit');
        $db->exesql($zqlna);
        $ShBS->log_het($zqlna, 'edit');

        //na wijzigen nieuwe clientenlijst
        $this->haal_clienten();
        $this->maak_client_knopjes();
        $output = '        <div id = "instructie" class = "instructie">identificatie van: <b>' . $this->client_oud . '</b> is gewijzigd in <b>' . $this->client_nw . '</b></div>';
        $output .= '       <div id = "alg_toel" class = "alg_toel">' . $this->knoppen . '</div>';
        return $output;
    }
    public function wijzigClient()
    {
        $output = '        <div id = "instructie" class = "instructie">Verander de identificatie van: <b>' . $this->client . '</b></div>';
        $output .= '       <div id = "alg_toel" class = "alg_toel">' . $this->MaakFormulier() . '</div>';
        return $output;
    }
    public function start()
    {
        $output = '        <div id = "instructie" class = "instructie">Selecteer één van je cliënten:</div>';
        $output .= '       <div id = "alg_toel" class = "alg_toel">' . $this->knoppen . '</div>';
        return $output;
    }
    public function haal_clienten()
    {
        $db = new BsDb();
        $db->sluitopen($this->resp);
        $this->clienten = $db->get_clients($this->resp);
    }
    public function get_interface()
    {
        $ShBS = new SharedBS();
        //$logo = $ShBS->maak_logo($GLOBALS["variant"]);
        $logo = $ShBS->maak_logo_vis_2();
        $output = $ShBS->get_style();
        //$output .= $this->get_biebs();
        $output .= '<form action="#" method="post">';
        $output .= '    <div class="edit_wrapper">';
        $output .= '        <div id = "hoofder" class = "hoofder">' . $logo . '<h1>wijzigen van cliëntgegevens</h1></div>';
        $output .= $this->uitput;
        $output .= '    </div>';
        $output .= '</form';

        echo $output;
        //$this->haal_L_en_G($resp);
    }
    public function check_of_ook_open()
    {
        $db = new BsDb();
        return $db->get_if_open($this->resp, $this->client);
    }
    public function MaakFormulier()
    {
        $uit = '<form action="#" method="post">';
        $uit .= '<label for="idkind" class = "ag_onderwerp">Identificatie kind: </label>
                <input required value = "' . $this->client . '" type="text" id="idkind" name="nieuw_id">';
        $uit .= '<input type = "hidden" name = "oud_id" value = "' . $this->client . '"> ';
        $uit .= '<input type="hidden" id="variant" name="variant" value=' . $GLOBALS['variant'] . '>';
        $uit .= '   <button name = "wijzig_client" value = "id">wijzig de identificatie van het kind</button>';

        if ($this->check_of_ook_open($this->client))
        {
            $uit .= '   <button name = "verwijder_id_open">verwijder openstaande meting</button>';
            $uit .= '   <button name = "verwijder_id">verwijder alle data van dit kind</button>';
        }
        else
        {
            $uit .= '   <button name = "verwijder_id">verwijder data van dit kind</button>';
        }

        $uit .= '</form>';

        return $uit;
    }
    public function maak_client_knopjes()
    {
        $knoppen = '';
        if ($GLOBALS['variant'] > 1)
        {
            if (key_exists('basiscommunicatie', $this->clienten))
            {

                foreach ($this->clienten['basiscommunicatie'] as $val)
                {

                    $knoppen .= '<button class = "kind_knop" name = "wijzig_client" value = "' . $val . '">' . $val . '</button>';

                }
            }
        }
        else
        {
            if (key_exists('bouwstenen', $this->clienten))
            {

                foreach ($this->clienten['bouwstenen'] as $val)
                {

                    $knoppen .= '<button class = "kind_knop" name = "wijzig_client" value = "' . $val . '">' . $val . '</button>';

                }
            }
        }
        $uit = '<form action="#" method="post">';
        $uit .= '<input type="hidden" id="variant" name="variant" value=' . $GLOBALS['variant'] . '>';
        $this->knoppen = $uit . $knoppen . '</form>';
    }
    public function maak_client_knopjesoud()
    {
        // //ts($this->clienten);
        $knoppen = '';
        if ($this->clienten)
        {
            //
            foreach ($this->clienten as $key => $vals)
            {
                $gehad[$key] = false;
                foreach ($vals as $datum)
                {

                    if ($datum != 'open')
                    {
                        if (!$gehad[$key])
                        {
                            $knoppen .= '<button class = "kind_knop" name = "wijzig_client" value = "' . $key . '">' . $key . '</button>';
                        }
                        $gehad[$key] = true;
                    }
                    else
                    {
                        $knoppen .= '<button class = "kind_knop_open" name = "wijzig_client" value = "' . $key . '">' . $key . ' (open)</button>';
                    }
                }
            }
        }
        $this->knoppen = $knoppen;
    }

}