<?php

class BsSpin
{

    public function __CONSTRUCT()
    {

    }

    public function maak_spin($titel, $cats, $series)
    {
        $plek = 'spin';
        //$titel = 'reg12354, 7 oktober 2020';
        $cats = json_encode($cats);
        //ts($series);
        $series = json_encode($series);
        //ts($series);
        //$series = '[{"name":"stabiliteit algemeen","data":["0":50,"1":52,"2":50,"3":50,"4":56,"5":38,"6":53,"7":50,"8":56,"9":50]},{"name":"stabiliteit leeftijd","data":{"0":30,"1":53,"2":50,"3":50,"5":25,"6":50,"7":50,"8":67,"9":50}},{"name":"stabiliteit leeftijd jongens","data":{"0":50,"1":33,"8":67,"9":100}},{"name":"stabiliteit leeftijd meisjes","data":{"2":50,"3":50,"5":50,"6":38,"7":100}}]';
        //ts($cats);
        $uit = "
                    <script>
Highcharts.chart('" . $plek . "', {

    chart: {
        polar: true,
        type: 'area',
        margin: [50, 50, 50, 50],
        style: {fontFamily: 'Open Sans', color: 'black', fontSize:'12px'},

    },

    title: {
        text: '" . $titel . "'

    },
    
    pane: {
        size: '70%'
    },
    xAxis: {
        categories: " . $cats . ",
        tickmarkPlacement: 'on',
        lineWidth: 0,
        labels:
        {
            style:
            {
                fontSize:'14px'
            }
        }
    },

    yAxis: {
        gridLineInterpolation: 'polygon',
        lineWidth: 0,
        min: 0,
        max: 5.0001,
        tickInterval: 1,
        labels: {
                x: 0,
                y: 0
            }
    },
    plotOptions:{
                        line:
                        {
                                lineWidth: 3,
                                marker:
                                {
                                    radius: 8
                                }
                            }
                        },
                            credits:
                        {
                            text: '© HanSei/Konsili',
                            href: 'http://www.hansei.nl'
                        },
                        legend: {
                            layout: 'vertical',
                            align: 'left',
                            verticalAlign: 'bottom',
                            borderWidth: 0,
                            itemStyle: {
                                font: '9pt Open Sans',
                                color: '#444'
                            },
                            itemHiddenStyle: {
                                color: '#A0A0A0'
                            }
                        },

    series: " . $series . ",

    responsive: {
        rules: [{
            condition: {
                maxWidth: 500
            },
            chartOptions: {
                legend: {
                    align: 'center',
                    verticalAlign: 'bottom',
                    layout: 'horizontal'
                },
                pane: {
                    size: '70%'
                }
                }
            }]
        }

    });	</script>";
        return $uit;
    }
}
// end class Toegang
