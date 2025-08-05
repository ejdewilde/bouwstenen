var margin = { top: 5, right: 5, bottom: 5, left: 5 };
var width = 1000 - margin.left - margin.right;
var height = 70 - margin.top - margin.bottom;
var aantalknoppen = data.length;
var knophoogte = 55;
var knopmarge = 5;
var knopbreedte = (width - (aantalknoppen + 1) * knopmarge) / aantalknoppen;
var animatiesnelheid = 500;
var hoogteoffset = 10;
var cirkelgrootte = 35;

var kleur = ["#058DC7", "#50B432", "#ED561B", "#DDDF00", "#24CBE5"];

function updateSliderInput(val) {
    document.getElementById('textInput').value = val;
}
var chart1 = d3
    .select("#knoppen")
    //.attr("width", width)
    //.attr("height", 55)
    .attr("viewBox", "0 0 " + width + " " + height)
    .attr("preserveAspectRatio", "xMidYMid meet");

var chart2 = d3
    .select("#resultaatknop")
    //.attr("width", width)
    //.attr("height", 55)
    .attr("viewBox", "0 0 " + width + " " + height)
    .attr("preserveAspectRatio", "xMidYMid meet");
//.attr("transform", "translate(" + margin.left + "," + margin.top + ")");

function teken() {
    koppie = d3.select("#kop")
        .append("h1")
        .html(data[0].label);
    //knoppen zelf:
    chart1
        .selectAll("rect")
        .data(data)
        .enter()
        .append("rect")
        .attr("id", "knop")
        .attr("height", knophoogte - knopmarge)
        .attr("width", knopbreedte)
        .attr("rx", 5)
        .attr("ry", 5)
        .style("fill", "#1a80b6")
        .style("opacity", 1)
        .attr("y", 2)
        .attr("x", function (d, i) {
            return i * (knopbreedte + knopmarge) + knopmarge;
        })
        .on("click", function (d, i) {
            laatZien(i);
        })
        .on("mouseover", function (d, i) {
            d3.select(this).style("cursor", "pointer");
            d3.select(this).style("stroke-width", "5px");
        })

        .on("mouseout", function (d) {
            d3.select(this).style("cursor", "default");
            d3.select(this).style("stroke-width", "0px");
        });

    //streepjes zelf.
    chart1
        .selectAll("rect#bm")
        .data(data)
        .enter()
        .append("rect")
        .attr("height", knophoogte - knopmarge)
        .attr("width", 0)
        .attr("id", "bm")
        .attr("rx", 5)
        .attr("ry", 5)
        .attr("x", function (d, i) {
            return i * (knopbreedte + knopmarge) + knopmarge;
        })
        .attr("y", 2)
        .style("fill", "#103960")
        .style("opacity", 1)
        .on("click", function (d, i) {
            laatZien(i);
        })
        .style("stroke-opacity", 1)
        .style("stroke", "yellow")
        .style("stroke-width", "0px")
        .style("stroke-alignment", "outer")
        .on("mouseover", function (d, i) {
            d3.select(this).style("cursor", "pointer");
            d3.select(this).style("stroke-width", "5px");
        })
        .on("mouseout", function (d) {
            d3.select(this).style("cursor", "default");
            d3.select(this).style("stroke-width", "0px");
        });

    //tekst op knoppen:
    chart1
        .selectAll("text#staven")
        .data(data)
        .enter()
        .append("text")
        .attr("pointer-events", "none")
        .attr("font-weight", "normal")
        .attr("id", "staven")
        .attr("stroke-width", "0")
        .attr("font-size", "20")
        .attr("stroke", "blue")
        .attr("y", 0.5 * knophoogte + knopmarge)
        .attr("x", function (d, i) {
            return i * (knopbreedte + knopmarge) + 0.5 * knopbreedte + knopmarge;
        })
        .attr("text-anchor", "middle")
        .text(function (d, i) {
            return d.label;
        })
        .attr("fill", "white");
    //checkmarks:

    chart1
        .selectAll("image")
        .data(data)
        .enter()
        .append("image")
        .attr("x", function (d, i) {
            return (i + 1) * (knopbreedte + knopmarge) - 5 * knopmarge;
        })
        .attr("y", -10)
        .attr("width", 40)
        .attr("height", 40)
        .attr(
            "xlink:href",
            "https://mijnait.konsili.dev/_additional_classes/_third_party/AitMonitor/bouwstenen/images/checkboxGreen.png"
        )
        .attr("opacity", 0);

    //resultaatknop
    //even uit gezet, moet verschijnen wanneer alles compleet...

    chart2
        .append("rect")
        .attr("id", "resultaatknop")
        .attr("height", knophoogte - knopmarge)
        .attr("width", width - 2 * knopmarge)
        .attr("rx", 5)
        .attr("ry", 5)
        .style("fill", "#1a80b6")
        .style("stroke", "yellow")
        .style("stroke-width", "0px")
        .style("stroke-opacity", 0)
        .style("stroke-alignment", "outer")
        .style("opacity", 0)
        .attr("y", knopmarge)
        .attr("x", knopmarge)
        .on("click", function (d, i) {
            //$("#naar_bsq").submit();      
            GaNaarVisual();
        })
        .on("mouseover", function (d, i) {
            d3.select(this).style("cursor", "pointer");
            d3.select(this).style("stroke-width", "5px");
        })
        .on("mouseout", function (d) {
            d3.select(this).style("cursor", "default");
            d3.select(this).style("stroke-width", "0px");
        });


    //tekst op resultaatknop:
    chart2
        .selectAll("text")
        .data(data)
        .enter()
        .append("text")
        .style("opacity", 0)
        .attr("pointer-events", "none")
        .attr("font-weight", "normal")
        .attr("id", "resultaatknop_tekst")
        .attr("stroke-width", "0")
        .attr("font-size", "20")
        .attr("stroke", "blue")
        .attr("y", 0.5 * knophoogte + 2 * knopmarge)
        .attr("x", width / 2)
        .attr("text-anchor", "middle")
        .text("bekijk resultaten")
        .attr("fill", "white");
}

function GaNaarVisual() {
    varpad = window.location.protocol + '//' + window.location.host + window.location.pathname;
    window.location.href = varpad + '?visual=' + meta.idkind + '&variant=' + variant;
}

function LaatResultaatKnopZien() {
    chart2
        .selectAll("rect")
        .transition()
        .duration(4 * animatiesnelheid)
        .style("opacity", 1)

    //tekst op resultaatknop:
    chart2
        .selectAll("text")
        .transition()
        .duration(5 * animatiesnelheid)
        .style("opacity", 1);

}

function laatZien(toon) {
    d3.select("#kop")
        .selectAll("h1")
        .remove();
    for (z = 0; z < 5; z++) {
        hier = "orb_" + z;
        var x = document.getElementById(hier);
        if (z == toon) {
            x.style.display = "block";
            d3.select("#kop")
                .append("h1")
                .html(data[z].label);
            d3.select("#kop")
                .transition()
                .duration(2500)
                .attr("color", "red");
        } else {
            if (x !== null) {
                x.style.display = "none";
            }
        }
    }
}

function berekenScore(knop) {
    var score = 0;
    var klasse = ".rad_" + knop;
    //var knop = itemid.substr(6, 1)-1;
    var notes = document.querySelectorAll(klasse);
    var aantal = data[knop].aantalitems;

    for (const vak of notes) {
        if (vak.checked) {
            score++;
        }
    }

    return Math.round(100 * (score / aantal));
}

function werkGrafiekbij(klasnaam) {
    //var xScale = d3.scale.linear().domain([0, 100]).range([0, knopbreedte + knoplinks]);
    var knop = klasnaam.substr(4, 1);

    //bijwerken knopvulling
    score = (berekenScore(knop) * knopbreedte) / 100;
    var chart = chart1.selectAll("rect#bm");
    d3.select(chart.nodes()[knop])
        .transition()
        .duration(animatiesnelheid)
        .attr("width", score);

    //als knop vol: vinkje.
    var imag = chart1.selectAll("image");
    if (score == knopbreedte) {
        d3.select(imag.nodes()[knop])
            .transition()
            .delay(animatiesnelheid)
            .duration(animatiesnelheid)
            .attr("opacity", 1);
        //als knop vol: door naar volgende knop
        var nwknop = parseInt(knop) + 1;
        //zat al in laatste knop: terug naar knop 0
        if ((nwknop > (aantalknoppen - 1))) {
            laatZien(0);
        } else {
            laatZien(nwknop);
        }
    }
}

function checkOpTotaal() {
    var aantalaangekruist = 0;
    var aantalmogelijk = 0;
    for (i = 0; i < aantalknoppen; i++) {
        var klasse = ".rad_" + i;
        var notes = document.querySelectorAll(klasse);
        aantalmogelijk = aantalmogelijk + notes.length / 10;
        for (const vak of notes) {
            if (vak.checked) {
                aantalaangekruist++;
            }
        }
    }

    if (aantalaangekruist == aantalmogelijk) {
        slaOpArchief();
        LaatResultaatKnopZien();
    }
    //alert(aantalmogelijk);
}

function slaOp($item, $waarde) {
    //var bijwerk = 'bewaar=nu&' + jQuery("form").serialize();
    var bijwerk = 'bewaar=item&itemid=' + $item + '&value=' + $waarde + '&client_id=' + meta.idkind + '&lftcat=' + (parseInt(12 * meta.LeeftijdJaar) + parseInt(meta.LeeftijdMaand)) + '&video=' + meta.video + '&versie=' + meta.versie + '&observatie_datum=' + meta.observatie_datum + '&werksoort=' + meta.WerksoortSelect + '&timestamp=' + Date.now();
    //bijwerk = 'bewaar=item&itemid=itemId102&value=1&client_id=bbbbbbbbbbbbb&lftcat=8&video=vht&versie=kort&observatie_datum=2022-08-16&werksoort=10&timestamp=1660677941561';
    //console.log(bijwerk);

    jQuery.ajax({
        type: "POST",
        url: window.location.origin + window.location.pathname,
        data: bijwerk,
        timeout: 1000,
        cache: false
    })
        .done(function (result) {
            console.log('okidoki. item sent response completed, received return result'); // dev/test only
        })
        .fail(function (result) {
            console.log('onee. item sent response failed');
        });
}

function slaOpArchief() {

    $.when(slaOp()).done(function (a1) {
        // the code here will be executed when all four ajax requests resolve.
        // a1, a2, a3 and a4 are lists of length 3 containing the response text,
        // status, and jqXHR object for each of the four ajax calls respectively.
        //var bijwerk = 'bewaar=nu&' + jQuery("form").serialize();
        var bijwerk = 'bewaar=archief&observatie_datum=' + Date.parse(meta.observatie_datum) + '&client_id=' + meta.idkind;
        //bijwerk = 'bewaar=archief&observatie_datum=1660608000000&client_id=bbbbbbbbbbbbb';
        console.log(bijwerk);

        jQuery.ajax({
            type: "POST",
            url: window.location.origin + window.location.pathname,
            data: bijwerk,
            timeout: 5000,
            cache: false
        })
            .done(function (result) {
                console.log('okidoki. bewaar=archief uitgevoerd'); // dev/test only
            })
            .fail(function (result) {
                console.log('onee. bewaar=archief niet uitgevoerd? sent response failed');
            });
    });


}


$("input[type=radio]").click(function () {
    //alert(this.className);
    werkGrafiekbij(this.className);
    slaOp(this.name, this.value);
    checkOpTotaal();
});

if (nieuwe_vragenlijst) {
    teken();
    laatZien(0);
} else {
    var aantaldata = open_data.length;
    for (z = 0; z < aantaldata; z++) {
        var selectstr = 'imgId' + open_data[z].itemid + open_data[z].value;
        var comp = document.getElementById(selectstr);
        comp.checked = true;
    }

    teken();
    laatZien(0);

    for (z = 0; z < aantalknoppen; z++) {
        var kla = "rad_" + z;
        werkGrafiekbij(kla);
    }
}