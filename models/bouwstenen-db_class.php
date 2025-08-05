<?php
/* ------------------------------------------------
Model class for database interaction
Author: Konsili / Ivar Hoekstra
Author url: http://www.konsili.nl
Edited by: Hansei / EJ de Wilde
ait plugin - version: 2.0
------------------------------------------------ */

class BsDb
{

    public function __construct()
    {
        /*DEBUG CHANGE*/
        $this->Db = new mysqli('localhost', MonitorAit_settings::getDBuid(), MonitorAit_settings::getDBpw(), MonitorAit_settings::getDBName());
        //lokaal
        //$this->Db = new mysqli('localhost', 'user', 'user', 'ait-nw');

        //echo 'hallo';
        // connect user on server

        if ($this->Db->connect_errno) {
            $this->errorOccured = 'db_connect_error';
        }
        $this->itemsTable = 'items';

        /*DEBUG CHANGE*/
        $this->plugindir = MonitorAit_settings::getPlugin_dir_url();
        //$this->plugin = substr(plugin_dir_url(__FILE__), 0, -7);
        //$this->surveyResponsesTable = 'scores';
        //$this->locatiesTable = 'locaties';
        //$this->groepenTable = 'groepen';
        //echo 'jaja3';

        //die;
    }

    /* ----------------------------------------- */
    /*!---    Query's  / getter functions   ---- */
    /* ----------------------------------------- */

    //! -- Items --
    public function get_if_open($resp, $cli)
    {
        $query = 'SELECT * from meten_response_data
        where zichtbaar=1 and archief is null and client_id = "' . $cli . '" and uid_respondent=' . $resp . ';';
        $lft = $this->getArray($query);
        $terug = false;
        if ($lft) {
            $terug = true;
        }

        return $terug;
    }
    public function removeSmallArrays(&$array, $waarde)
    {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                removeSmallArrays($value);
                if (count($value) < $waarde) {
                    unset($array[$key]);
                }
            }
        }
    }

    public function get_existing_data($resp, $cli)
    {
        //sleep(10);
        //ts('ja');
        $query_ontdubbel = 'DELETE t1 FROM meten_response_data t1 INNER JOIN meten_response_data t2
                            WHERE t1.id < t2.id AND t1.client_id=t2.client_id AND t1.lftcat=t2.lftcat AND t1.uid_respondent=t2.uid_respondent AND t1.werksoort=t2.werksoort AND t1.observatie_datum=t2.observatie_datum
                            AND t1.video = t2.video AND t1.versie=t2.versie AND t1.itemid=t2.itemid AND t1.client_id = "' . $cli . '" and t1.uid_respondent=' . $resp . ';';
        $query_bs = 'SELECT i.sub2kort as cat, observatie_datum as dag, round(avg(IF(i.richting=1, m.value, 6-m.value)),1) as gem, count(m.value) as aantal
                        from meten_response_data  m
                        inner join items i on m.itemid = i.itemid
                        where (i.variant = 1 or i.variant = 3) and m.zichtbaar=1 and m.observatie_datum is not null and m.client_id = "' . $cli . '" and m.uid_respondent=' . $resp . '
                        group by i.sub2kort, observatie_datum order by observatie_datum, i.sub2nr';
        //ts($query_bs);
        $query_bc = 'SELECT i.subcomm as cat, observatie_datum as dag, round(avg(IF(i.richting=1, m.value, 6-m.value)),1) as gem, count(m.value) as aantal
                        from meten_response_data  m
                        inner join items i on m.itemid = i.itemid
                        where (i.variant = 2 or i.variant = 3) and m.zichtbaar=1 and m.observatie_datum is not null and m.client_id = "' . $cli . '" and m.uid_respondent=' . $resp . ' and i.comm = 1
                        group by i.subcomm, observatie_datum order by observatie_datum, i.sub2nr';
        //ts($query_bc);
        $query_lft = 'SELECT DISTINCT lftcat, observatie_datum, observatie_datum as dag from meten_response_data  m
                        where m.zichtbaar=1 and m.observatie_datum is not null and m.client_id = "' . $cli . '" and m.uid_respondent=' . $resp . '
                        order by observatie_datum DESC';
        //ts($query_lft);
        $query_na = 'SELECT distinct archief as datum, naam, nabijheid as score
                        from nabijheids_data
                        where archief is not null and client_id = "' . $cli . '" and uid_respondent=' . $resp . '
                        order by archief DESC, naam';
        //ts($query_na);
        //ts($query_ontdubbel);
        $aa = $this->exesql($query_ontdubbel);
        $lft = $this->getArray($query_lft);
        $arr_bs = $this->getArray($query_bs);
        $arr_bc = $this->getArray($query_bc);
        $arr_na = $this->getArray($query_na);
        //ts($arr_bs);
        //nabijheid
        //ts($arr_na);
        $na_data = array();
        $na_data2 = array();

        if ($arr_na) {
            $tel = 0;
            foreach ($arr_na as $row) {
                $na_data[$row['datum']][$tel]['naam'] = $row['naam'];
                $na_data[$row['datum']][$tel]['score'] = $row['score'];
                $tel++;
            }
            $tel1 = 0;
            foreach ($na_data as $datum => $inh) {
                $tel2 = 0;
                foreach ($inh as $row) {
                    $na_data2[$tel1][$tel2]['naam'] = $row['naam'];
                    $na_data2[$tel1][$tel2]['score'] = $row['score'];
                    $tel2++;
                }
                $tel1++;
            }

        }
        //ts($na_data);
        //ts($na_data2);
        if ($arr_bs || $arr_bc) {

            $data_bs = array();
            $data_bs['kind_id'] = $cli;

            //bouwstenen
            $constructen = array('basisveiligheid', 'toevertrouwen', 'zelfvertrouwen', 'zelfstandigheid', 'creativiteit');
            foreach ($constructen as $construct) {
                foreach ($arr_bs as $row) {
                    if ($row['cat'] == $construct) {
                        //$data_bs['data'][$row['dag']]['lft'] = $row['lftcat'];
                        $data_bs['data'][$row['dag']]['bs'][$row['cat']]['v'] = $row['gem'];
                        $data_bs['data'][$row['dag']]['bs'][$row['cat']]['n'] = $row['aantal'];
                    }
                }
            }
            //ts($data_bs);
            //$constructen = array('basisveiligheid', 'toevertrouwen', 'zelfvertrouwen', 'zelfstandigheid', 'creativiteit');
            //ts($constructen);

            foreach ($data_bs['data'] as $datum => $val) {
                //ts($datum);
                if (key_exists('basisveiligheid', $val['bs'])) {
                    //ts($val);
                    if ($val['bs']['basisveiligheid']['n'] < 4) {
                        unset($data_bs['data'][$datum]);
                    }
                } else {
                    unset($data_bs['data'][$datum]);
                }
            }
            //ts($data_bs);
            $mm_bs = array_keys($data_bs['data']);

            foreach ($data_bs['data'] as $dag => $inhoud) {
                foreach ($lft as $row) {
                    if ($row['dag'] == $dag) {
                        $data_bs['data'][$dag]['leeftijd'] = $this->maak_leeftijd_woorden($row['lftcat']);
                    }
                }

            }
            //exit;
            $d3_data_bs = array();
            $dagtel = 0;

            foreach ($data_bs['data'] as $dag => $waarden) {
                $d3_data_bs[$dagtel]['datum'] = $dag;
                $d3_data_bs[$dagtel]['leeftijd'] = $waarden['leeftijd'];
                $tel = 0;
                foreach ($waarden['bs'] as $schaal => $gem) {
                    $d3_data_bs[$dagtel]['concepten'][$tel]['concept'] = $schaal;
                    $d3_data_bs[$dagtel]['concepten'][$tel]['score'] = round($gem['v'], 1);
                    $tel++;
                }
                $dagtel++;
            }

            //ts($d3_data_bs);
            /*
            //nabijheid toevoegen
            foreach ($d3_data_bs as $dagtel => $inhoud)
            {
            $tel = 0;
            if ($arr_na)
            {
            foreach ($na_data['data'] as $dagna => $arrinh)
            {
            foreach ($arrinh as $wie => $hoeveel)
            {
            if ($inhoud['datum'] == $dagna)
            {
            $d3_data_bs[$dagtel]['nabijheid'][$tel]['naam'] = $wie;
            $d3_data_bs[$dagtel]['nabijheid'][$tel]['score'] = $hoeveel;
            $tel++;
            }
            }
            }
            }
            }
             */
            //basiscommunicatie
            $data_bc = array();
            $data_bc['kind_id'] = $cli;

            //bouwstenen
            if ($arr_bc) {
                foreach ($arr_bc as $row) {
                    //$data_bc['data'][$row['dag']]['lft'] = $row['lft'];
                    $data_bc['data'][$row['dag']]['bc'][$row['cat']]['v'] = $row['gem'];
                    $data_bc['data'][$row['dag']]['bc'][$row['cat']]['n'] = $row['aantal'];

                }
                //ts($data_bc);

                foreach ($data_bc['data'] as $datum => $val) {
                    //ts($datum);
                    if (key_exists('initiatief en ontvangst', $val['bc']) && key_exists('uitwisseling in de kring', $val['bc']) && key_exists('overleg', $val['bc']) && key_exists('conflict hanteren', $val['bc'])) {
                    } else {
                        unset($data_bc['data'][$datum]);
                    }
                }

                $mm_bc = array_keys($data_bc['data']);

                //toevoegen leeftijd
                foreach ($data_bc['data'] as $dag => $inhoud) {
                    foreach ($lft as $row) {
                        if ($row['dag'] == $dag) {
                            $data_bc['data'][$dag]['leeftijd'] = $this->maak_leeftijd_woorden($row['lftcat']);
                        }
                    }

                }

                $d3_data_bc = array();
                $dagtel = 0;

                foreach ($data_bc['data'] as $dag => $waarden) {
                    $d3_data_bc[$dagtel]['datum'] = $dag;
                    $d3_data_bc[$dagtel]['leeftijd'] = $waarden['leeftijd'];
                    $tel = 0;
                    foreach ($waarden['bc'] as $schaal => $gem) {
                        $d3_data_bc[$dagtel]['concepten'][$tel]['concept'] = $schaal;
                        $d3_data_bc[$dagtel]['concepten'][$tel]['score'] = round($gem['v'], 1);
                        $tel++;
                    }
                    $dagtel++;
                }
            }
            /*
            //nabijheid toevoegen
            foreach ($d3_data_bc as $dagtel => $inhoud)
            {
            $tel = 0;
            if ($arr_na)
            {
            foreach ($na_data['data'] as $dagna => $arrinh)
            {
            foreach ($arrinh as $wie => $hoeveel)
            {
            if ($inhoud['datum'] == $dagna)
            {
            $d3_data_bc[$dagtel]['nabijheid'][$tel]['naam'] = $wie;
            $d3_data_bc[$dagtel]['nabijheid'][$tel]['score'] = $hoeveel;
            $tel++;
            }
            }
            }
            }
            }
             */
            $terug = array();

            $terug[0] = $data_bs;
            $terug[1] = $d3_data_bs;
            $terug[2] = $data_bc;
            $terug[3] = $d3_data_bc;
            $terug[4] = $mm_bs;
            $terug[5] = $mm_bc;
            $terug[6] = $na_data2;
            //ts($terug);
            return $terug;
        }

    }
    public function maak_leeftijd_woorden($mnd)
    {

        $effe = floor($mnd / 12);
        $effe2 = $mnd - 12 * $effe;
        $tekst = $effe . ' jaar';
        if ($effe2 > 1) {
            $tekst .= ' en ' . $effe2 . ' maanden.';
        } elseif ($effe2 > 0) {
            $tekst .= ' en ' . $effe2 . ' maand.';
        }
        return $tekst;
    }
    public function doe_aanpassing()
    {
        $queer = "update teksten set inhoud = '<p>Het kind kan in deze fase zijn leefwereld verbreden. Het experimenteert met afstand nemen en nabijheid zoeken. Het ervaart dat het eigen angst kan overwinnen en steun en troost kan vragen.</p><p>Voor het kind is dit de fase van actief eropuit gaan, handelen, uitproberen, zelf ontdekken wat je kan. </p> <p>Voor de ouders is het de fase van op bereikbare afstand blijven, aandacht en interesse tonen, ruimte en mogelijkheden scheppen,  zonodig hulp en steun bieden.</p>' where id=2";
        $result = $this->exesql($queer);

    }
    public function get_item_toelichtingen($resp, $cli, $soort)
    {

        if ($soort == 'bs') {
            $vava1 = 'sub2nr';
            $vava2 = 'sub2nr';
        } else {
            $vava1 = 'subcomm_nr-10';
            $vava2 = 'subcomm_nr';
        }

        $query = "SELECT i.itemid, i." . $vava1 . " as sub2nr, i.pos, i.neg,   m.`value`,i.richting, observatie_datum as dag, max(m.id)
                    FROM meten_response_data m
                    INNER JOIN items i ON m.itemid = i.itemid
                    WHERE m.client_id = '" . $cli . "'
                    AND m.zichtbaar=1
                    AND m.observatie_datum is not null
                    AND m.uid_respondent=" . $resp . "
                    GROUP BY i.itemid, i." . $vava2 . ", m.observatie_datum
                    ORDER BY i." . $vava2 . ", m.observatie_datum, i.itemid";

        $arr = $this->getArray($query);
        //ts($query);
        $terug = array();
        if ($arr) {
            foreach ($arr as $key => $val) {
                $addendum = '';
                if ($val['value'] == 1 || $val['value'] == 5) {
                    $addendum = 'meestal';
                } elseif ($val['value'] == 2 || $val['value'] == 4) {
                    $addendum = 'vaak';
                } elseif ($val['value'] == 3) {
                    $addendum = 'soms';
                }
                //ts($val['itemid'] . ':' . $val['value'] . ':' . $val['richting']);
                if ($val['richting'] < 1) {

                    switch ($val['value']) {
                        case 1:
                        case 2:
                            $terug[$val['dag']][$val['sub2nr'] - 1][0][] = $val['neg'] . ' (' . $addendum . ')';
                            break;
                        case 3:
                            $terug[$val['dag']][$val['sub2nr'] - 1][1][] = $val['pos'] . ' (soms) <b>en</b> ' . $val['neg'] . ' (soms)';
                            break;
                        case 4:
                        case 5:
                            $terug[$val['dag']][$val['sub2nr'] - 1][2][] = $val['pos'] . ' (' . $addendum . ')';
                            break;
                    }
                } else {
                    switch ($val['value']) {
                        case 1:
                        case 2:
                            $terug[$val['dag']][$val['sub2nr'] - 1][2][] = $val['pos'] . ' (' . $addendum . ')';
                            break;
                        case 3:
                            $terug[$val['dag']][$val['sub2nr'] - 1][1][] = $val['pos'] . ' (soms) <b>en</b> ' . $val['neg'] . ' (soms)';
                            break;
                        case 4:
                        case 5:
                            $terug[$val['dag']][$val['sub2nr'] - 1][0][] = $val['neg'] . ' (' . $addendum . ')';
                            break;
                    }

                }
                //if (!$terug[$val['dag']][$val['sub2nr'] - 1]['oranje'][0]) {$terug[$val['dag']][$val['sub2nr'] - 1]['oranje'] = 'niks';}
                //$terug[$val['dag']][$val['sub2nr'] - 1]['groen'] = 'niks';
                //$terug[$val['dag']][$val['sub2nr'] - 1]['rood'] = 'niks';
            }
        }
        //ts($terug);
        /*
        if ($arr) {
        foreach ($arr as $key => $val) {
        if  ($terug[$val['dag']][$val['sub2nr'] - 1]['groen'])
        }
        }
         */
        //ts($terug);

        $terug2 = array();
        $tel = 0;
        $teldag = 0;
        foreach ($terug as $key => $val) {
            foreach ($val as $key2 => $val2) {
                //ts('key2:');
                //ts($key2);
                foreach ($val2 as $kleur => $items) {

                    $inh = '<div class = "div_stoplicht"><ul>';
                    foreach ($items as $item) {
                        $inh .= '<li>' . $item . '</li>';
                    }
                    $inh .= '</ul></div>';

                    $terug2[$teldag][$tel][$kleur] = $inh;
                }
                $tel++;
            }
            $tel = 0;
            $teldag++;
            //ts('tel:');
            //ts($tel);
        }
        //ts($terug2);
        return $terug2;

    }

    public function get_toelichtingen()
    {
        $plugindir = aplugin_dir_url(__FILE__);

        $query = "select * from teksten order by id";
        $arr = $this->getArray($query);
        //$dest = "#bs_toel";
        $terug = array();
        if ($arr) {
            foreach ($arr as $key => $val) {
                if ($val['id'] > 9 && $val['id'] < 14) {
                    $terug[$val['id']]['itje'] = $itje = '<img class = "itje" onclick="muisKlik_bc(' . $key . ')" width = "50px" vertical_align = "top" align = "right" src = "' . $plugindir . 'images/i-tje.png">';

                    //ts('ja: ' . $val['id']);
                } else {
                    $terug[$val['id']]['itje'] = $itje = '<img class = "itje" onclick="muisKlik(' . $key . ')" width = "50px" vertical_align = "top" align = "right" src = "' . $plugindir . 'images/i-tje.png">';
                }
                //$terug[$val['id']]['itje'] = $itje = '<div id = "plek_voor_itje"></div>';
                $terug[$val['id']]['inhoud'] = $val['inhoud'];
                $terug[$val['id']]['titel'] = $val['titel'];
                $terug[$val['id']]['leeftijd'] = $val['leeftijd'];
            }
        }
        //ts($terug);
        return $terug;
    }

    public function get_open_data($kid)
    {
        $query = "select distinct lftcat, werksoort, versie, video, observatie_datum from meten_response_data where client_id = '" . $kid . "';";
        $arr = $this->getArray($query);
        //ts($arr);

        $terug = array();
        $terug["LeeftijdJaar"] = floor($arr[0]["lftcat"] / 12);
        $terug["LeeftijdMaand"] = $arr[0]["lftcat"] - (floor($arr[0]["lftcat"] / 12)) * 12;
        $terug["WerksoortSelect"] = $arr[0]["werksoort"];
        $terug["versie"] = $arr[0]["versie"];
        $terug["video"] = $arr[0]["video"];
        $terug["observatie_datum"] = $arr[0]["observatie_datum"];
        //$terug["archief"] = $arr[0]["archief"];
        $terug["idkind"] = $kid;
        return $terug;
    }

    public function get_ws()
    {
        $query = "select * from werksoorten order by naam";
        $arr = $this->getArray($query);

        $terug = array();
        if ($arr) {
            foreach ($arr as $key => $val) {
                $terug[$val['id']] = $val['naam'];
            }
        }
        return $terug;
    }
    public function sluitopen($resp)
    {
        $query = "update meten_response_data m, (select client_id, uid_respondent, max(timestamp) as archiefdat from meten_response_data where uid_respondent=" . $resp . " and archief is null group by client_id) a
                    set m.archief = a.archiefdat where m.uid_respondent = a.uid_respondent and a.client_id=m.client_id;";
        //ts($query);

        $result = $this->exesql($query);
    }

    public function get_clients($resp)
    {
        $query = "select distinct client_id, archief from meten_response_data where uid_respondent = " . $resp . " and zichtbaar = 1 order by client_id";
        $query = "select * from
                    (select count(id) as aantal, archief, client_id, 'bouwstenen' as wat from meten_response_data m
                    inner join items i on m.itemid=i.itemid
                    where m.uid_respondent =  " . $resp . "  and m.zichtbaar = 1 and i.sub2nr=1
                    group by client_id, archief order by client_id) bs
                    where bs.aantal>3
                    union
                    select * from
                    (select count(id) as aantal, archief, client_id, 'basiscommunicatie' as wat from meten_response_data m
                    inner join items i on m.itemid=i.itemid
                    where m.uid_respondent =  " . $resp . "  and m.zichtbaar = 1 and subcomm IS NOT NULL
                    group by client_id, archief order by client_id) bc
                    where bc.aantal>15";
        //ts($query);
        $arr = $this->getArray($query);
        //ts($arr);
        $terug = array();
        if ($arr) {
            //$teller = 0;
            foreach ($arr as $row) {
                $terug[$row['wat']][] = $row["client_id"];

            }

        }

        //ts($terug);
        //ts($terug2);
        //$terug = array_unique($terug);
        //ts($terug);
        return $terug;
    }

    public function get_lft()
    {
        $query = "select * from leeftijden order by id";
        $arr = $this->getArray($query);

        $terug = array();
        if ($arr) {
            foreach ($arr as $key => $val) {
                $terug[$val['id']] = $val['naam'];
            }
        }
        return $terug;
    }
    public function getAllItems()
    {
        $query = "select * from $this->itemsTable";
        return $this->getResultObjectForQuery($query);
    }

    public function getAllCategories()
    {
        $query = "select distinct sub3 as categorytitle, sub3nr from $this->itemsTable";
        //ts($query);
        return $this->getResultObjectForQuery($query);
    }

    public function getSubsForCategory($cat)
    {
        $query = "select distinct sub2kort, sub2nr, sub3nr from $this->itemsTable where sub3 = '$cat'";
        //ts($query);
        return $this->getResultObjectForQuery($query);
    }

    public function getItemsForSub($catTitle)
    {
        //ts($catTitle);
        $query = "select * from $this->itemsTable where sub2 = '$catTitle'";
        //ts($query);
        return $this->getResultObjectForQuery($query);
    }

    public function getAllItemsOrdered()
    {
        $categories = self::getAllCategories();
        //$categories[0] = 'bouwstenen';
        //ts("<b>");
        //ts($categories);
        //ts("</b>");
        $categoriesOrdered = array(false, false, false, false, false); // new array to store custom order
        foreach ($categories as $cat) {

            $items = self::getItemsForSub($cat->categorytitle);
            $cat->items = $items;

            //$cat->subcategories = $subs;
            // apply custom order
            if (strstr(strtolower($cat->categorytitle), 'bouw')) {
                $categoriesOrdered[0] = $cat;
            }
            /*
        if (strstr(strtolower($cat->categorytitle), 'toever')) {
        $categoriesOrdered[1] = $cat;
        }
        if (strstr(strtolower($cat->categorytitle), 'zelfve')) {
        $categoriesOrdered[2] = $cat;
        }
        if (strstr(strtolower($cat->categorytitle), 'zelfst')) {
        $categoriesOrdered[3] = $cat;
        }
        if (strstr(strtolower($cat->categorytitle), 'creati')) {
        $categoriesOrdered[4] = $cat;
        }
         */
        }
        return (object) $categoriesOrdered;
    }

    public function getAllCategoriesFilterForGroup($group)
    {
        $query = "select distinct sub3 as categorytitle, sub3nr from $this->itemsTable";
        return $this->getResultObjectForQuery($query);
    }

    public function getSubsForCategoryFilterForGroup($cat, $group)
    {
        $query = "select distinct sub2kort, sub2nr, sub3nr from $this->itemsTable where sub3 = '$cat'";
        return $this->getResultObjectForQuery($query);
    }

    public function getItemsForSubFilterForGroup($catTitle, $subnr, $group)
    {
        $query = "select * from $this->itemsTable where sub3 = '$catTitle' and sub2nr = $subnr";
        return $this->getResultObjectForQuery($query);
    }

    public function getAllCategoriesFilterForAgeCat($ageCatPart)
    {
        $query = "select distinct sub3 as categorytitle, sub3nr from $this->itemsTable where $ageCatPart";
        return $this->getResultObjectForQuery($query);
    }

    public function haalCats($lft)
    {

        $query = "select distinct sub2kort, sub2nr, sub3nr from items where lft < " . ($lft + 1) . ";";
        //ts($query);
        //die;
        $terug = array();
        $aa = $this->getArray($query);
        //die;
        foreach ($aa as $row) {
            $terug[] = $row["sub2kort"];
        }
        //ts($terug);
        //die;
        return $terug;
    }
    public function haalCatsBC()
    {

        $query = "select distinct subcomm from items where variant=2;";
        //ts($query);
        //die;
        $terug = array();
        $aa = $this->getArray($query);
        //die;
        foreach ($aa as $row) {
            $terug[] = $row["subcomm"];
        }
        //ts($terug);
        //die;
        return $terug;
    }
    public function haalDataCheck($data)
    {
        $terug = false;
        if ($data["idkind"] && $data["observatie_datum"]) {
            $zql = "select * from meten_response_data where
            client_id = '" . $data["idkind"] . "' and
            observatie_datum = '" . $data["observatie_datum"] . "';";
        }
        $aa = $this->getArray($zql);
        if ($aa) {
            return true;
        }
    }
    public function haalItemsVoorCat($cat, $versie, $variant)
    {
        $extra = '';

        if ($variant == 1) {
            $vava = 'sub2kort';
            $varisql = ' and (variant = 1 or variant = 3)';
        } elseif ($variant == 2) {
            $vava = 'subcomm';
            $varisql = ' and (variant = 2 or variant = 3)';
        } elseif ($variant > 3) {
            $vava = 'subcomm';
            $varisql = '';
        }

        if ($versie == 'kort' && $variant == 1) {
            $extra = ' and (verkort = 1)';
        }
        $query = "select * from items where " . $vava . " = '" . $cat . "'" . $extra . $varisql . ";";
        ts($query);
        return $this->getArray($query);
    }

    public function getSubsForCategoryFilterForAgeCat($cat, $ageCatPart)
    {
        $query = "select distinct sub2kort, sub2nr, sub3nr from $this->itemsTable where sub3 = '$cat' and ($ageCatPart)";
        //ts($query);
        return $this->getResultObjectForQuery($query);
    }
    public function haalData($kid)
    {
        $query = "select itemid, value from meten_response_data where client_id = '" . $kid . "' and archief is null order by itemid;";
        //ts($query);
        $terug = array();
        $arr = $this->getArray($query);

        return $arr;
    }

    public function getItemsForSubFilterForAgeCat($catTitle, $subnr, $ageCatPart)
    {
        $query = "select * from $this->itemsTable where sub3 = '$catTitle' and sub2nr = $subnr and ($ageCatPart)";
        //ts($query);
        return $this->getResultObjectForQuery($query);
    }

    public function getAllItemsOrderedFilterForAgeCat($age)
    {

        $ageCatPart = "lft <= $age";
        $categories = self::getAllCategoriesFilterForAgeCat($ageCatPart);
        $categoriesOrdered = array(false, false, false, false, false); // new array to store custom order
        foreach ($categories as $cat) {
            $subs = '';
            $subs = self::getSubsForCategoryFilterForAgeCat($cat->categorytitle, $ageCatPart);
            //ts($subs);
            foreach ($subs as $sub) {
                $items = '';
                $items = self::getItemsForSubFilterForAgeCat($cat->categorytitle, $sub->sub2nr, $ageCatPart);
                $sub->items = $items;

            }
            $cat->subcategories = $subs;
            //ts($cat);
            // apply custom order
            /*
        if (strstr(strtolower($cat->categorytitle), 'basisv')) {
        $categoriesOrdered[0] = $cat;
        }
        if (strstr(strtolower($cat->categorytitle), 'toever')) {
        $categoriesOrdered[1] = $cat;
        }
        if (strstr(strtolower($cat->categorytitle), 'zelfve')) {
        $categoriesOrdered[2] = $cat;
        }
        if (strstr(strtolower($cat->categorytitle), 'zelfst')) {
        $categoriesOrdered[3] = $cat;
        }
        if (strstr(strtolower($cat->categorytitle), 'creati')) {
        $categoriesOrdered[4] = $cat;
        }
         */
        }

        //ts($categoriesOrdered);
        return $cat;
    }

    //! -- responses --

    /* getSurveyResponseItem
     * - get a unique response item
     * - unique identifier: uid_respondent + locatie + groep + itemid
     */
    public function getSurveyResponseItem($uid, $itemId, $locatie, $groep)
    {
        $locatie = $this->escapeString($locatie);
        $groep = $this->escapeString($groep);
        $query = "select * from $this->surveyResponsesTable where uid_respondent = '$uid' and itemid = '$itemId' and locatie_id = '$locatie' and groep_id = '$groep'";
        return $this->getResultObjectForQuery($query);
    }

    /* getSurveyResponseItemTyperespondent
     * - get a unique response item
     * - unique identifier: type_respondent + locatie + groep + itemid
     */
    public function getSurveyResponseItemTypeRespondent($itemId, $locatie, $groep, $uidRespondent, $archiveIsPart)
    {
        $locatie = $this->escapeString($locatie);
        $groep = $this->escapeString($groep);
        $query = "select * from $this->surveyResponsesTable where uid_respondent = '$uidRespondent' and itemid = '$itemId' and locatie_id = '$locatie' and groep_id = '$groep' and archief $archiveIsPart;";
        return $this->getResultObjectForQuery($query);
    }

    /* getSurveyResponses
     * - get all responses from a single survey
     * - unique identifier: uid_respondent + locatie + groep
     */
    public function getSurveyResponses($uid, $kindid)
    {
        $query = "select * from $this->surveyResponsesTable where userid = '$uid' and respnr = '$kindid' and itemid is not null";
        //ts($query);
        return $this->getResultObjectForQuery($query);
    }

    /* getAllSurveyResponsesForLocationGroup
     * - get all responses from location/group
     * - unique identifier: locatie + groep
     */
    public function getAllSurveyResponsesForLocationGroup($locatie, $groep)
    {
        $query = "select * from $this->surveyResponsesTable where locatie_id = '$locatie' and groep_id = '$groep'";
        return $this->getResultObjectForQuery($query);
    }

    /* getAllSurveyResponsesForLocationGroupRespondentOccurence
     * - get all responses from location/group/respondent/survey instance
     * - unique identifier: locatie + groep
     */
    public function getAllSurveyResponsesForLocationGroupRespondentOccurence($locatie, $groep, $resp_uid, $archiveIsPart)
    {
        $query = "select * from $this->surveyResponsesTable where locatie_id = '$locatie' and groep_id = '$groep' and uid_respondent = '$resp_uid' and archief $archiveIsPart;"; //PpibUtilities::tst($query);
        return $this->getResultObjectForQuery($query);
    }

    /* getAllRespondentsBySurveyResponsesForLocationGroup
     * - get all users for a single survey
     * - unique identifier: locatie + groep
     */
    public function getAllRespondentsBySurveyResponsesForLocationGroup($locatie, $groep)
    {
        $query = "select distinct uid_respondent, archief from $this->surveyResponsesTable where locatie_id = '$locatie' and groep_id = '$groep'";
        return $this->getResultObjectForQuery($query);
    }

    /* getAllSurveyOccurrencesForLocation
     * - get all occurrences for a Location (single surveys)
     * - unique identifier: locatie
     */
    public function getAllSurveyOccurrencesForLocation($locatie)
    {
        $query = "select distinct r.archief, r.uid_respondent, r.groep_id, u.display_name from $this->surveyResponsesTable r left join wp_users u on r.uid_respondent = u.id where r.locatie_id = '$locatie'";
        return $this->getResultObjectForQuery($query);
    }

    /* getAllSurveyOccurrencesForGroup
     * - get all occurrences for a Location/Group (single surveys)
     * - unique identifier: locatie + groep
     */
    public function getAllSurveyOccurrencesForGroup($groep)
    {
        //$query = "select distinct archief from $this->surveyResponsesTable where groep_id = '$groep'";
        $query = "select distinct r.archief, r.uid_respondent, r.groep_id, u.display_name from $this->surveyResponsesTable r left join wp_users u on r.uid_respondent = u.id where r.groep_id = '$groep'";
        return $this->getResultObjectForQuery($query);
    }

    /* getAllSurveyOccurrencesForLocationGroup
     * - get all occurrences for a Location/Group (single surveys)
     * - unique identifier: locatie + groep
     */
    public function getAllSurveyOccurrencesForLocationGroup($locatie, $groep)
    {
        $query = "select distinct archief from $this->surveyResponsesTable where locatie_id = '$locatie' and groep_id = '$groep'";
        return $this->getResultObjectForQuery($query);
    }

    /* getAllSurveyOccurrencesForLocationGroupRespondent
     * - get all occurrences for a Location/Group (single surveys)
     * - unique identifier: locatie + groep
     */
    public function getAllSurveyOccurrencesForLocationGroupRespondent($locatie, $groep, $repondent)
    {
        $query = "select distinct archief from $this->surveyResponsesTable where locatie_id = '$locatie' and groep_id = '$groep' and uid_respondent = '$repondent'";
        return $this->getResultObjectForQuery($query);
    }

    /* getAllOpenSurveyOccurrencesForRespondent
     * - get all open occurrences for a respondent (single surveys)
     * - unique identifier: respondent uid
     */
    public function getAllOpenSurveyOccurrencesForRespondent($repondent)
    {
        $query = "select distinct r.groep_id, r.locatie_id, g.naam, l.naam as loc_naam from $this->surveyResponsesTable r left join $this->groepenTable g on r.groep_id = g.id left join $this->locatiesTable l on g.locatie_id = l.id where r.uid_respondent = '$repondent' and (r.archief is NULL or r.archief is false)";
        return $this->getArray($query);
    }

    /* getAllSurveyResponses
     * - get all responses from a single survey
     * - unique identifier: uid_respondent + locatie + groep
     */
    public function getAllSurveyResponses()
    {
        $query = "select * from $this->surveyResponsesTable";
        return $this->getResultObjectForQuery($query);
    }

    //! -- locaties / groepen --

    public function getLocaties()
    {
        $query = "select * from $this->locatiesTable order by locatie asc";
        return $this->getResultObjectForQuery($query);
    }

    public function getLocatiesForOrg($organisationId)
    {
        $query = "select * from $this->locatiesTable where organisatie_id = '" . $organisationId . "' order by naam asc"; //PpibUtilities::tst($query);
        return $this->getResultObjectForQuery($query);
    }

    public function getAllGroupTypes()
    {
        $query = "select distinct soort from $this->groepenTable";
        return $this->getResultObjectForQuery($query);
    }

    public function getGroupsForLocatie($locatieId)
    {
        $locatie_id = $this->escapeString($locatieId);
        $query2 = "select * from $this->groepenTable where locatie_id = '$locatie_id'";
        return $this->getResultObjectForQuery($query2);
    }

    public function getLocationName($locatieId)
    {
        $locatie_id = $this->escapeString($locatieId);
        $query2 = "select naam from $this->locatiesTable where id = '$locatie_id'";
        $qresult = $this->getResultObjectForQuery($query2);
        if ($qresult) {
            $qresult = $qresult->{0}->naam;
        }

        return $qresult;
    }

    public function getGroupName($groupId)
    {
        $locatie_id = $this->escapeString($groupId);
        $query2 = "select naam from $this->groepenTable where id = '$groupId'";
        $qresult = $this->getResultObjectForQuery($query2);
        if ($qresult) {
            $qresult = $qresult->{0}->naam;
        }

        return $qresult;
    }

    public function getIfLocationGroupsRecentlyChecked($locatieId)
    {
        $locatieId = $this->escapeString($locatieId);
        $queryGroups = "select * from $this->groepenTable where locatie_id = '$locatieId'";
        $groups = $this->getResultObjectForQuery($queryGroups);
        if ($groups) {
            //$timeNow = time();
            $query = "select groups_checked_at from $this->locatiesTable where id = '$locatieId'";
            //$result = $this->getResultObjectForQuery($query);
            //if(($timeNow - $result->{0}->groups_checked_at) < 15552000) return 'hasRecentCheck';
            //else return 'noRecentCheck';
        } else {
            return 'hasNoGroups';
        }

    }

    public function resetGroupsForLocatie($locatieId, $newGroups)
    {
        $this->deleteGroupsForLocatie($locatieId);
        if ($newGroups) {
            foreach ($newGroups as $group) {
                $group->naam = $this->escapeString($group->naam);
                $query = "insert into $this->groepenTable values ('', '$locatieId', '$group->naam', '$group->soort')";
                $this->getResultFor($query);
            }
        }
    }

    public function deleteGroupsForLocatie($locatieId)
    {
        $query = "delete from $this->groepenTable where locatie_id = '$locatieId'";
        $this->getResultFor($query);
    }

    public function markLocationAsGroupsChecked($locatieId)
    {
        $timeNow = time();
        $query = "update $this->locatiesTable set groups_checked_at = $timeNow where id = '$locatieId'";
        $this->getResultFor($query);
    }

    //---- logo's and names
    public function getNameAndLogoURLForSubSite($siteID = false)
    {
        $query = "SELECT wp_blogmeta.blog_id, substr(wp_blogs.path,2, length(wp_blogs.path)-2) as Site_name, wp_blogmeta.meta_value as Site_logo_url
                    FROM wp_blogs INNER JOIN wp_blogmeta ON wp_blogs.blog_id = wp_blogmeta.blog_id
                    WHERE wp_blogmeta.meta_key = 'logo'";
        $resultObj = $this->getResultObjectForQuery($query);
        if ($siteID) {
            foreach ($resultObj as $site) {
                if ($site->blog_id == $siteID) {
                    return $site;
                }

            }
        } else {
            return $resultObj;
        }
    }

    // etc...

    /* ----------------------------------------- */
    /*!---    Query's  / setter functions   ---- */
    /* ----------------------------------------- */

    /* setSurveyResponse
     * - store response in survey responses table: insert if new or update if existing
     * - unique identifier for existing: uid_respondent + locatie + groep + itemid
     */
    public function setSurveyResponse($data)
    {
        // process/add some data before storing in db
        $timeNow = time();
        $data->lftcat = $this->escapeString($data->leeftijdscat);

        $existingRecord = self::getSurveyResponseItem($data->ui_respondent, $data->client_id);
        if ($existingRecord) {
            $query = "update $this->surveyResponsesTable set client_id = '$data->client_id', lftcat = '$data->lftcat', uid_respondent = '$data->ui_respondent', werksoort = '$data->werksoort', itemid = '$data->itemid', value = '$data->value', timestamp = '$timeNow' where uid_respondent = '$data->ui_respondent' and itemid = '$data->itemid' and locatie_id = '$data->locatie'";
        } else {
            $query = "insert into meten_response_data (lftcat, client_id, uid_respondent, werksoort, itemid, value, timestamp) values ('$data->lftcat', '$data->client_id', '$data->ui_respondent', '$data->werksoort', '$data->itemid', '$data->value', '$timeNow')";
        }
        $plugindir = substr(plugin_dir_url(__FILE__), 0, -7);
        $file = $plugindir . '/querylog.txt';

        file_put_contents($file, $query);

        $qResult = $this->getResultFor($query);
        if (!$qResult) {
            // log error
            $query = $this->escapeString($query);
            $human_time = date("d-m-Y H:i:s");
            $input_data = $this->escapeString(print_r($data, true));
            $q2 = "insert into error_log_save_response (input_data, query, human_time, timestamp) values ('$input_data', '$query', '$human_time', '$timeNow')";
            $log2 = $this->getResultFor($q2);
        }
        return $qResult;
    }

    public function setSurveyAsClosed($locId, $groupId, $uidRespondent)
    {
        $locId = $this->escapeString($locId);
        $groupId = $this->escapeString($groupId);
        $uidRespondent = $this->escapeString($uidRespondent);
        $timeNow = time();
        $q = "update $this->surveyResponsesTable set archief = $timeNow where locatie_id = '$locId' and groep_id = '$groupId' and uid_respondent = '$uidRespondent'";
        return $this->getResultFor($q);
    }

    //! --- locaties / groepen ---
    public function setCurrentGroupInfoForGroup($groupId)
    {
        $query = "select * from $this->groepenTable where id = '$groupId'";
        $result = $this->getResultObjectForQuery($query);
        $this->currentGroupInfo = $result->{0};
    }

    /* ------------------------------ */
    /*!---    basic functions    ---- */
    /* ------------------------------ */

    /* escapeString, @param string: the string to escape
     * - real escape string for use in query
     */
    public function escapeString($string)
    {
        return $this->Db->real_escape_string($string);
    }

    /* getResultObjectForQuery
     * - combine query_db_XXX function and processDbQueryResult function
     */
    public function getResultObjectForQuery($query)
    {
        $db_result = $this->getResultFor($query);
        return $this->processDbQueryResult($db_result);
    }

    /* getResultFor, @param query: the query string
     * - get result from database for given query
     */
    protected function getResultFor($query)
    {
        $this->Db->set_charset("utf8");
        $db_result = $this->Db->query($query);
        return $db_result;
    }

    /* getResultFor, @param db_result: result from function getResultFor
     * - process the default result from mysqli->query, returns a standard object containing the result
     */
    private function processDbQueryResult($db_result)
    {
        if (!property_exists((object) $db_result, 'num_rows')) {
            return false;
        }

        if ($db_result->num_rows < 1) {
            $result_object = false;
            /*} elseif($db_result->num_rows == 1) {
        $result_object = $db_result->fetch_object();*/
        } else {
            $result_object = (object) [];
            for ($i = 0; $i < $db_result->num_rows; $i++) {
                $temp_obj = $db_result->fetch_object();
                $result_object->$i = $temp_obj;
            }
        }
        unset($result_object->scalar);
        return $result_object;
    }

    // -- EJ toevoeging --
    /* getArray en andere eerdere functies toegevoegd vanuit het idee van backwards compatibility
     *
     */

    public function exesql($sql)
    {
        $this->Db->set_charset("utf8");
        if (is_string($sql)) {
            $output = $this->Db->query($sql);
        } else {
            $output = false;
        }

        return $output;
    }

    // return an array
    public function getArray($sql)
    {
        //ts($sql);
        $output = array();
        $result = $this->exesql($sql);
        //ts($result);
        if ($result) {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $output[] = $row;
                }

                return $output;
            }
        }
    }

    // return single string from query
    public function getString($sql)
    {

        $this->Db->set_charset("utf8");
        $result = $this->exesql($sql);
        //PpibUtilities::tst($result);

        if ($result->num_rows > 0) {
            //return ( array_values($result->fetch_assoc())[0]);
            $thisa = $result->fetch_assoc();
            $tor = array_values($thisa);
            return $tor[0];
        }
    }

    public function closedb()
    {
        mysqli_close($this->Db);
    }

    // for debug/development only
    public function ts($test)
    {
        echo '<pre>';
        echo print_r($test, true);
        echo '</pre>';
    }

}
