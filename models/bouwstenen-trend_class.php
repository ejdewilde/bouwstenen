<?php

class BsTrend
{

    public function __CONSTRUCT()
    {

    }

    public function maak_trend_figuur($series, $cats, $plek, $yas, $ymax, $ymin)
    {
        //ts($series);
        //ts($cats);
        $cats = json_encode($cats);
        $series = json_encode($series, JSON_NUMERIC_CHECK);
        $uit = "
                    <script>
                    Highcharts.setOptions({colors: ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5']});

Highcharts.chart('" . $plek . "', {
    backgroundColor: '#36949a',
    title: {text:''},
    subtitle: {text: ''},
    
    xAxis: {
            type: 'category', 
            categories: " . $cats . "
        },
    yAxis: {
        title: {
            text: '" . $yas . "'
        },
        min:" . $ymin . ",
        max:" . $ymax . "
       
       
    },
        legend: {
            align: 'center',
            layout: 'vertical',     
            verticalAlign: 'bottom',
            x: 0,
            y: 0
        },
        credits: {
            enabled: false
        },
    plotOptions: {
        series: {
            marker: {
                lineWidth: 2,
                radius: 8,
                symbol: 'circle',               
            }
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
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                }
            }
        }]
    }

});
	</script>";
        return $uit;
    }
}
// end class Toegang

?>