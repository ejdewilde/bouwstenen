if (variant > 1) {
    var data_filt = data_bc[0].concepten;
    var toel_data_filt = item_toel_bc[0];
} else {
    var data_filt = data_bs[0].concepten
    var toel_data_filt = item_toel_bs[0];
}
var mmstart = 0;
var na_filt = conv_nabij(data_na[mmstart]);
var fase = 0;
//var nabijheid = true;

//var item_toelichting = item_toel[0];
var bs_kleur = ["#058DC7", "#50B432", "#ED561B", "#DDDF00", "#24CBE5"];
var bc_kleur = ["#782121", "#a02c2c", "#c83737", "#d35f5f"];
var bc_labels = [
    "initiatief en ontvangst",
    "uitwisseling in de kring",
    "overleg",
    "conflict hanteren"
];
var bs_labels = [
    "basisveiligheid",
    "toevertrouwen",
    "zelfvertrouwen",
    "zelfstandigheid",
    "creativiteit"
];
var maanden = [
    "fake",
    "januari",
    "februari",
    "maart",
    "april",
    "mei",
    "juni",
    "juli",
    "augustus",
    "september",
    "oktober",
    "november",
    "december"
];
var lft_comm = [0, 72, 144, 192];

var aantalmetingen = meetmomenten.length;
var blokhoogte = 65;
var blokmarge = 4;
var breedte = 465;
var hoogte_bs = 5 * blokhoogte + 4 * blokmarge;
var hoogte_bc = 4 * blokhoogte + 3 * blokmarge;
var dezepag = window.location.href;
var nax = 204;
var nay = 158;
var nasch = 5;
var nar = 180;
//data_filt = filterVoorCom(data_filt, fase);
//console.log(data_filt);
var aantalblokken_bs = data_filt.length;
var aantalblokken_bc = data_filt.length;

var blokbreedte_bs = breedte - 50;
var blokbreedte_bc = 0.6 * blokbreedte_bs;
var bloklinks = (breedte - blokbreedte_bs) / 2;

var animatiesnelheid = 2000;
/*
function filterVoorCom(hier, reeks) {
    //filtert uit databegin de waarden die gecheckt zijn.

    var terug = Array();
    var norigineel = Object.keys(hier).length;
    //var nclusters = Object.keys(clusters).length;

    for (i = 0; i < norigineel; i++) {
        //if (parseInt(data_bc[reeks]scommunicatie[i].lft_item) < parseInt(data[reeks].lft_maand)) {
        terug.push(hier[i]);
        //}
    }
    //var stuur = TransformeerString(terug);
    return terug;
}
*/

function zet_teksten() {
    d3.select("#kind_id").html("<h2>" + kind_id + "</h2>");
    if (variant > 1) { d3.select("#alg_toel").html(toel[105].inhoud); } else { d3.select("#alg_toel").html(toel[100].inhoud); }
    d3.select("#kop_bc_toel").html("<h2>" + toel[102].titel + "</h2>");
    d3.select("#text_bc_toel").html(toel[102].inhoud);
    d3.select("#kop_bs_toel").html("<h2>" + toel[101].titel + "</h2>");
    d3.select("#text_bs_toel").html(toel[101].inhoud);
    d3.select("#na_toel").html(
        "<h2>" + toel[104].titel + "</h2></br>" + toel[104].inhoud
    );
}

function computeDimensions(selection) {
    var dimensions = null;
    var node = selection.node();

    if (node instanceof SVGElement) {
        dimensions = node.getBBox();
    } else {
        dimensions = node.getBoundingClientRect();
    }
    //console.clear();
    //console.log(dimensions);
    return dimensions;
}
/*data voor nabijheid tunen*/
function conv_nabij(data) {

    const pi = Math.PI;
    const terug = [];
    if (data) {
        for (i = 0; i < data.length; i++) {
            var graden = i * 360 / data.length;
            var rad = graden * pi / 180;
            var x = Math.sin(rad) * data[i].score;
            var y = Math.cos(rad) * data[i].score;
            var z = data[i].score;
            var w = data[i].naam;
            const coord = [x, y, z, w];
            terug.push(coord);
        }
    }
    return terug;
}


var svg = d3
    .select("svg#bouwstenen")
    .attr("viewBox", "0 0 " + breedte + " " + hoogte_bs)
    .attr("preserveAspectRatio", "xMidYMid meet");

var svg2 = d3
    .select("svg#basiscommunicatie")
    .attr("viewBox", "0 0 " + breedte + " " + hoogte_bc)
    .attr("preserveAspectRatio", "xMidYMid meet");

var svg3 = d3
    .select("svg#datumknoppen")
    .attr("width", breedte)
    .attr("height", 50);

var svg4 = d3
    .select("svg#nabijheid")
    .attr("viewBox", "0 0 " + breedte + " " + breedte)
    .attr("preserveAspectRatio", "xMidYMid meet");
var svg5 = d3
    .select("svg#stop_bs")
    //.attr("viewBox", "0 0 120 30")
    .attr("width", 150)
    .attr("height", 50)
    .attr("preserveAspectRatio", "xMidYMid meet");
var svg6 = d3
    .select("svg#stop_bc")
    //.attr("viewBox", "0 0 120 30")
    .attr("width", 150)
    .attr("height", 50)
    .attr("preserveAspectRatio", "xMidYMid meet");

function teken_achtergrond() {
    //nabijheid
    svg4
        .append('circle')
        .attr('cx', 235)
        .attr('cy', 220)
        .style("opacity", 0.5)
        .attr('r', nar)
        .attr('stroke', bs_kleur[0])
        .attr('stroke-width', "10px")
        .attr('fill', "white");
    /*
      svg4
        .append('circle')
        .attr('cx', 235)
        .attr('cy', 220)
        .attr('r', 10)
        .attr('stroke', "red")
        .attr('stroke-width', "10px")
        .attr('fill', "red");
    */

    //bouwstenen
    svg
        .selectAll("rect#a")
        .data(bs_kleur)
        .enter()
        .append("rect")
        .attr("id", "a")
        .attr("height", blokhoogte)
        .attr("width", breedte)
        .attr("rx", 1)
        .attr("ry", 1)
        .attr("cursor", "pointer")
        .style("fill", function (d, i) {
            return bs_kleur[i];
        })
        .style("stroke", "red")
        .style("stroke-width", "0px")
        .style("opacity", 0.4)
        .attr("y", function (d, i) {
            return (4 - i) * (blokhoogte + blokmarge);
        })
        .attr("x", 0)
        .on("mouseover", function (d, i) {
            muisOver(i, "bs_toel");
        })
        /*
        .on("mouseout", function () {
          muisUit(101, "bs_toel");
        })
        */
        ;

    //bouwstenen tekst
    svg
        .selectAll("text#at")
        .data(bs_kleur)
        .enter()
        .append("text")
        .attr("font-weight", "normal")
        .attr("id", "at")
        .attr("stroke-width", "0")
        .attr("font-size", "14")
        .style("opacity", 0.5)
        .attr("stroke", "blue")
        .attr("y", function (d, i) {
            return (
                5 + blokhoogte / 2 + (4 - i) * (blokhoogte + blokmarge)
            );
        })
        .attr("x", function (d, i) {
            return breedte / 2;
        })
        .attr("text-anchor", "middle")
        .text(function (d, i) {
            return bs_labels[i];
        })
        .attr("cursor", "pointer")
        .attr("fill", "black")
        .on("mouseover", function (d, i) {
            muisOver(i, "bs_toel");
        })
        /*
    .on("mouseout", function () {
      muisUit(101, "bs_toel");
    })
*/
        ;

    //basiscommunicatie
    svg2
        .selectAll("rect#a")
        .data(bc_kleur)
        .enter()
        .append("rect")
        .attr("cursor", "pointer")
        .attr("id", "a")
        .attr("height", blokhoogte)
        .attr("width", blokbreedte_bc)
        .attr("rx", 5)
        .attr("ry", 5)
        .style("fill", function (d, i) {
            return bc_kleur[i];
        })
        .style("stroke", "blue")
        .style("stroke-width", "0px")
        .style("stroke-opacity", 1)
        .style("opacity", 0.2)
        .attr("y", function (d, i) {
            return (
                (i) * (blokhoogte + blokmarge)
            );
        })
        .attr("x", breedte - blokbreedte_bc)
        .on("mouseover", function (d, i) {
            muisOver(10 + i, "bc_toel");
        })
        /*
    .on("mouseout", function () {
      muisUit(102, "bc_toel");
    })
*/
        ;

    //basiscommunicatie tekst
    svg2
        .selectAll("text#at")
        .data(bc_kleur)
        .enter()
        .append("text")
        .attr("cursor", "pointer")
        .attr("font-weight", "normal")
        .attr("id", "at")
        .style("opacity", 0.5)
        .attr("font-size", "14")
        .attr("y", function (d, i) {
            return (
                5 + blokhoogte / 2 + (i) * (blokhoogte + blokmarge)
            );
        })
        .attr("x", breedte - blokbreedte_bc - 10)
        .attr("text-anchor", "end")
        .text(function (d, i) {
            return bc_labels[i];
        })
        .attr("fill", "black")
        .on("mouseover", function (d, i) {
            muisOver(10 + i, "bc_toel");
        })
        /*
        .on("mouseout", function () {
          muisUit(102, "bc_toel");
        })
        */
        ;


}

function woord_datum(datum) {
    //datum = '2021-06-25'
    var dag = parseInt(datum.substring(8, 10));
    var maand = parseInt(datum.substring(6, 7));
    var jaar = datum.substring(0, 4);
    var terug = dag + " " + maanden[maand] + " " + jaar;
    /*
    var dag = datum.substring(0, 2);
    var maand = parseInt(datum.substring(3, 5));
    var jaar = datum.substring(6, 10);
    var terug = dag + " " + maanden[maand] + " " + jaar;
    */
    return terug;
}

function teken() {

    //eerste tekening van alle beschikbare schalen op deze knop, zonder waarden
    //basiscommunicatie
    var bolletjes = ["green", "orange", "red"];
    svg5
        .selectAll("circle#circ_stop_bs")
        .data(bolletjes)
        .enter()
        .append("circle")
        .attr("id", "circ_stop_bs")
        .attr("cursor", "pointer")
        .attr("cy", 20)
        .attr("cx", function (d, i) { return 20 + i * 35 })
        .style("fill", function (d, i) { return bolletjes[i] })
        .style("stroke", "black")
        .style("opacity", 0)
        .style("stroke-width", "3px")
        .style("stroke-opacity", 0)
        .attr("r", 15)
        .on("click",
            function (d, i) {
                togglezichtSL(i, 'bs');
            })
    svg6
        .selectAll("circle#circ_stop_bc")
        .data(bolletjes)
        .enter()
        .append("circle")
        .attr("cursor", "pointer")
        .attr("id", "circ_stop_bc")
        .attr("cy", 20)
        .attr("cx", function (d, i) { return 20 + i * 35 })
        .style("fill", function (d, i) { return bolletjes[i] })
        .style("stroke", "black")
        .style("opacity", 0)
        .style("stroke-width", "3px")
        .style("stroke-opacity", 0)
        .attr("r", 15)
        .on("click",
            function (d, i) {
                togglezichtSL(i, 'bc');
            })
    //basiscommunicatie
    if (typeof data_filt !== 'undefined' && data_filt.length > 0) {
        svg2
            .selectAll("rect#v")
            .data(data_filt)
            .enter()
            .append("rect")
            .attr("id", "v")
            .attr("height", blokhoogte)
            .attr("width", 0)
            .attr("rx", 5)
            .attr("ry", 5)
            .attr("cursor", "pointer")
            .style("fill", function (d, i) {
                return bc_kleur[i];
            })
            .style("stroke", "blue")
            .style("stroke-width", "0px")
            .style("opacity", 0.9)
            .attr("y", function (d, i) {
                return (i) * (blokhoogte + blokmarge);
            })
            .attr("x", breedte - blokbreedte_bc)
            .on("mouseover", function (d, i) {
                muisOver(10 + i, "bc_toel");
            })
            .on("mouseout", function () {
                muisUit(101, "bc_toel");
            });

        //basiscommunicatie %
        svg2
            .selectAll("text#p")
            .data(data_filt)
            .enter()
            .append("text")
            //.attr("font-weight", "bold")
            .attr("id", "p")
            .attr("cursor", "pointer")
            .attr("stroke-width", "0")
            .attr("font-size", "16")
            .attr("stroke", "blue")
            .attr("y", function (d, i) {
                return (
                    5 + blokhoogte / 2 + (i) * (blokhoogte + blokmarge)
                );
            })
            .attr("x", blokbreedte_bc - 20)
            .attr("text-anchor", "start")
            .text(function (d, i) {

                var labje = Math.round(100 * (data_filt[i].score - 1) * 25) / 100 + "%";
                //var labje = data_filt[i].score;
                //console.log(data_filt[i].score + ' ' + labje);
                return labje;
            })
            .attr("fill", "white")
            .attr("opacity", 1)
            .on("mouseover", function (d, i) {
                muisOver(10 + i, "bc_toel");
            })
            .on("mouseout", function () {
                muisUit(101, "bc_toel");
            });

        //basiscommunicatie tekst
        svg2
            .selectAll("text#t")
            .data(data_filt)
            .enter()
            .append("text")
            .attr("cursor", "pointer")
            .attr("font-weight", "normal")
            .attr("id", "t")
            .style("opacity", 1)
            .attr("font-size", "14")
            .attr("y", function (d, i) {
                return (
                    5 + blokhoogte / 2 + (i) * (blokhoogte + blokmarge)
                );
            })
            .attr("x", breedte - blokbreedte_bc - 10)
            .attr("text-anchor", "end")
            .text(function (d, i) {
                return d.omschrijving;
            })
            .attr("fill", "black")
            .on("mouseover", function (d, i) {
                muisOver(i, "bc_toel");
            })
            .on("mouseout", function () {
                muisUit(102, "bc_toel");
            });
    }
    //bouwstenen
    if (typeof data_filt !== 'undefined' && data_filt.length > 0) {
        svg
            .selectAll("rect#v")
            .data(data_filt)
            .enter()
            .append("rect")
            .attr("id", "v")
            .attr("cursor", "pointer")
            .attr("height", blokhoogte)
            .attr("width", function (d, i) {
                return (data_filt[i].score * blokbreedte_bs) / 5;
            })
            .attr("rx", 5)
            .attr("ry", 5)
            .style("fill", function (d, i) {
                return bs_kleur[i];
            })
            .style("stroke", "red")
            .style("stroke-width", "0px")
            .style("opacity", 1)
            .attr("y", -100)
            .attr("x", function (d, i) {
                return (breedte - (data_filt[i].score * blokbreedte_bs) / 5) / 2;
            })
            .on("mouseover", function (d, i) {
                muisOver(i, "bs_toel");
            })
            .on("mouseout", function () {
                muisUit(101, "bs_toel");
            });

        //bouwstenen tekst
        svg
            .selectAll("text#t")
            .data(data_filt)
            .enter()
            .append("text")
            .attr("font-weight", "normal")
            .attr("id", "t")
            .attr("stroke-width", "0")
            .attr("font-size", "14")
            .style("opacity", 1)
            .attr("stroke", "blue")
            .attr("y", -100)
            .attr("x", function (d, i) {
                return breedte / 2;
            })
            .attr("text-anchor", "middle")
            .text(function (d, i) {
                return bs_labels[i];
            })
            .attr("cursor", "pointer")
            .attr("fill", "black")
            .on("mouseover", function (d, i) {
                muisOver(i, "bs_toel");
            })
            .on("mouseout", function () {
                muisUit(101, "bs_toel");
            });
        //bouwsteenscore rechts
        svg
            .selectAll("text#st")
            .data(data_filt)
            .enter()
            .append("text")
            .attr("font-weight", "normal")
            .attr("id", "st")
            .attr("stroke-width", "0")
            .attr("font-size", "14")
            .style("opacity", 0)
            .attr("stroke", "blue")
            .attr("y", function (d, i) {
                return (
                    5 + blokhoogte / 2 + (4 - i) * (blokhoogte + blokmarge)
                );
            })
            .attr("x", function (d, i) {
                return breedte - 40;
            })
            .attr("text-anchor", "right")
            .text(function (d, i) {
                var labje = Math.round(10 * (data_filt[i].score)) / 10;
                return labje;
            })
            .attr("cursor", "pointer")
            .attr("fill", "black")
            .on("mouseover", function (d, i) {
                muisOver(i, "bs_toel");
            })
            .on("mouseout", function () {
                muisUit(101, "bs_toel");
            });
    }
    if (na_filt) {
        tekenNabij();
    }
}

function tekenNabij() {

    svg4
        .selectAll('path#ouder')
        .data(na_filt)
        .enter()
        .append('path')
        .attr('d', "M 4.8740307,9.3800251 C 3.7736078,10.563827 2.783283,11.754589 2.6046056,12.355124 c -0.076642,0.257592 0.2643523,0.8044 0.6171238,0.512601 0.3527716,-0.291799 1.4260629,-1.642839 1.4260629,-1.642839 0,0 0.040952,6.195222 0.106795,6.819108 0.065845,0.623886 1.2033525,0.421724 1.2143412,0.09046 0.010989,-0.331264 -0.00453,-3.6843 0.053051,-3.766748 0.066662,-0.09548 0.3446917,-0.142807 0.4091586,0.0024 0.065253,0.147017 0.091307,3.073543 0.1048134,3.668906 0.013507,0.595363 1.0990927,0.531499 1.1766199,-0.109248 0.077528,-0.640746 0.056461,-6.695707 0.056461,-6.695707 0,0 1.1690474,1.554556 1.4248696,1.583374 0.4309393,0.04858 0.702418,-0.201208 0.6370313,-0.48241 -0.087629,-0.376856 -1.3451086,-2.160974 -2.289332,-2.9549965 -0.1768689,-0.1487332 -2.4902623,-0.1907412 -2.6675695,0 z M 7.5714626,7.4301314 A 1.4233675,1.4372942 0 0 1 6.1480951,8.8674257 1.4233675,1.4372942 0 0 1 4.7247276,7.4301314 1.4233675,1.4372942 0 0 1 6.1480951,5.9928372 1.4233675,1.4372942 0 0 1 7.5714626,7.4301314 Z")
        .attr('id', 'ouder')
        .attr('stroke-width', 0)
        .style("fill", function (d, i) {
            return bs_kleur[i];
        })
        .attr('left', 100)
        .attr('top', 100)
        .attr("transform", "translate(" + nax + " " + (nay - 30) + ") scale(" + nasch + " " + nasch * 6 / 4 + ")");

    svg4
        .selectAll('text#ouders')
        .data(na_filt)
        .enter()
        .append("text")
        .style("opacity", 1)
        .attr("pointer-events", "none")
        .attr("font-weight", "normal")
        .attr("id", "ouders")
        .attr("stroke-width", "1")
        .attr("font-size", "12")
        .attr("y", 0)
        .attr("x", 0)
        .attr("text-anchor", "middle")
        .text(function (d, i) {
            return d[3];
        })
        .attr("fill", "red")
        .attr("transform", "translate(" + nax + " " + nay + ") scale(" + nasch + " " + nasch * 6 / 4 + ")");

    svg4
        .append('path')
        .attr('d', "M 4.8740307,9.3800251 C 3.7736078,10.563827 2.783283,11.754589 2.6046056,12.355124 c -0.076642,0.257592 0.2643523,0.8044 0.6171238,0.512601 0.3527716,-0.291799 1.4260629,-1.642839 1.4260629,-1.642839 0,0 0.040952,6.195222 0.106795,6.819108 0.065845,0.623886 1.2033525,0.421724 1.2143412,0.09046 0.010989,-0.331264 -0.00453,-3.6843 0.053051,-3.766748 0.066662,-0.09548 0.3446917,-0.142807 0.4091586,0.0024 0.065253,0.147017 0.091307,3.073543 0.1048134,3.668906 0.013507,0.595363 1.0990927,0.531499 1.1766199,-0.109248 0.077528,-0.640746 0.056461,-6.695707 0.056461,-6.695707 0,0 1.1690474,1.554556 1.4248696,1.583374 0.4309393,0.04858 0.702418,-0.201208 0.6370313,-0.48241 -0.087629,-0.376856 -1.3451086,-2.160974 -2.289332,-2.9549965 -0.1768689,-0.1487332 -2.4902623,-0.1907412 -2.6675695,0 z M 7.5714626,7.4301314 A 1.4233675,1.4372942 0 0 1 6.1480951,8.8674257 1.4233675,1.4372942 0 0 1 4.7247276,7.4301314 1.4233675,1.4372942 0 0 1 6.1480951,5.9928372 1.4233675,1.4372942 0 0 1 7.5714626,7.4301314 Z")
        .attr('stroke', "#80003c")
        .attr('id', 'kind')
        .attr('stroke-width', 0)
        .attr('fill', "#80003c")
        .attr('left', 100)
        .attr('top', 100)
        .attr('z', 100)
        .attr("transform", "translate(" + nax + " " + nay + ") scale(" + nasch + " " + nasch + ")");

}

function gooiWeg() {
    svg.selectAll("rect#v").remove();
    //svg.selectAll("rect#a").remove();
    svg.selectAll("text#t").remove();
    svg.selectAll("text#st").remove();
    svg2.selectAll("rect#v").remove();
    //svg2.selectAll("rect#a").remove();
    svg2.selectAll("text#t").remove();
    svg2.selectAll("text#p").remove();
}

function werkBij(moment) {
    if (variant > 1) {
        d3.select("#kind_id").html(
            "<h2>" +
            kind_id +
            "</h2><p>meetmoment: " +
            woord_datum(data_bc[moment].datum) +
            "</br>leeftijd bij meetmoment: " +
            data_bc[moment].leeftijd +
            "</p>"
        );
    } else {
        d3.select("#kind_id").html(
            "<h2>" +
            kind_id +
            "</h2><p>meetmoment: " +
            woord_datum(data_bs[moment].datum) +
            "</br>leeftijd bij meetmoment: " +
            data_bs[moment].leeftijd +
            "</p>"
        );
    }

    svg
        .selectAll("rect#v")
        .transition()
        .ease(d3.easeBounce)
        .duration(animatiesnelheid)
        .attr("y", function (d, i) {
            return (4 - i) * (blokhoogte + blokmarge);
        });

    svg
        .selectAll("text#t")
        .transition()
        .ease(d3.easeBounce)
        .duration(animatiesnelheid)
        .attr("y", function (d, i) {
            return (
                5 + blokhoogte / 2 + (4 - i) * (blokhoogte + blokmarge)
            );
        })

    svg
        .selectAll("text#st")
        .transition()
        .duration(5 * animatiesnelheid)
        .style("opacity", 100)
        .text(function (d, i) {
            var labje = Math.round(10 * (data_filt[i].score)) / 10;
            return labje;
        })

    svg2
        .selectAll("rect#v")
        .transition()
        .duration(animatiesnelheid)
        .attr("width", function (d, i) {
            return (blokbreedte_bc * (data_filt[i].score - 1) * 25) / 100;
        });

    svg2
        .selectAll("text#p")
        .transition()
        .delay(animatiesnelheid)
        .duration(animatiesnelheid)
        .attr("opacity", 100)
        .text(function (d, i) {
            var labje = Math.round(100 * (data_filt[i].score - 1) * 25) / 100 + "%";
            //var labje = data_filt[i].score;
            return labje;
        });

    svg2
        .selectAll("text#t")
        .transition()
        .delay(animatiesnelheid)
        .duration(animatiesnelheid)
        .style("opacity", 100);

    werkBijNabij(moment);

}

function werkBijNabij(moment) {
    na_filt = conv_nabij(data_na[moment]);
    svg4.selectAll("path#ouder").remove();
    svg4.selectAll("text#ouders").remove();

    svg4
        .selectAll('path#ouder')
        .data(na_filt)
        .enter()
        .append('path')
        .attr('d', "M 4.8740307,9.3800251 C 3.7736078,10.563827 2.783283,11.754589 2.6046056,12.355124 c -0.076642,0.257592 0.2643523,0.8044 0.6171238,0.512601 0.3527716,-0.291799 1.4260629,-1.642839 1.4260629,-1.642839 0,0 0.040952,6.195222 0.106795,6.819108 0.065845,0.623886 1.2033525,0.421724 1.2143412,0.09046 0.010989,-0.331264 -0.00453,-3.6843 0.053051,-3.766748 0.066662,-0.09548 0.3446917,-0.142807 0.4091586,0.0024 0.065253,0.147017 0.091307,3.073543 0.1048134,3.668906 0.013507,0.595363 1.0990927,0.531499 1.1766199,-0.109248 0.077528,-0.640746 0.056461,-6.695707 0.056461,-6.695707 0,0 1.1690474,1.554556 1.4248696,1.583374 0.4309393,0.04858 0.702418,-0.201208 0.6370313,-0.48241 -0.087629,-0.376856 -1.3451086,-2.160974 -2.289332,-2.9549965 -0.1768689,-0.1487332 -2.4902623,-0.1907412 -2.6675695,0 z M 7.5714626,7.4301314 A 1.4233675,1.4372942 0 0 1 6.1480951,8.8674257 1.4233675,1.4372942 0 0 1 4.7247276,7.4301314 1.4233675,1.4372942 0 0 1 6.1480951,5.9928372 1.4233675,1.4372942 0 0 1 7.5714626,7.4301314 Z")
        .attr('id', 'ouder')
        .attr('stroke-width', 0)
        .style("fill", function (d, i) {
            return bs_kleur[i];
        })
        .attr('left', 100)
        .attr('top', 100)
        .attr("transform", "translate(" + nax + " " + (nay - 30) + ") scale(" + nasch + " " + nasch * 6 / 4 + ")");

    svg4
        .selectAll('text#ouders')
        .data(na_filt)
        .enter()
        .append("text")
        .style("opacity", 0)
        .attr("pointer-events", "none")
        .attr("font-weight", "normal")
        .attr("id", "ouders")
        .attr("stroke-width", "1")
        .attr("font-size", "12")
        .attr("y", 0)
        .attr("x", 0)
        .attr("text-anchor", "middle")
        .text(function (d, i) {
            return d[3];
        })
        .attr("fill", "red")
        .attr("transform", "translate(" + nax + " " + nay + ")");

    svg4
        .selectAll('path#ouder')
        //.data(na_filt)
        //.enter()
        //.append('path')
        //.attr('d', "M 4.8740307,9.3800251 C 3.7736078,10.563827 2.783283,11.754589 2.6046056,12.355124 c -0.076642,0.257592 0.2643523,0.8044 0.6171238,0.512601 0.3527716,-0.291799 1.4260629,-1.642839 1.4260629,-1.642839 0,0 0.040952,6.195222 0.106795,6.819108 0.065845,0.623886 1.2033525,0.421724 1.2143412,0.09046 0.010989,-0.331264 -0.00453,-3.6843 0.053051,-3.766748 0.066662,-0.09548 0.3446917,-0.142807 0.4091586,0.0024 0.065253,0.147017 0.091307,3.073543 0.1048134,3.668906 0.013507,0.595363 1.0990927,0.531499 1.1766199,-0.109248 0.077528,-0.640746 0.056461,-6.695707 0.056461,-6.695707 0,0 1.1690474,1.554556 1.4248696,1.583374 0.4309393,0.04858 0.702418,-0.201208 0.6370313,-0.48241 -0.087629,-0.376856 -1.3451086,-2.160974 -2.289332,-2.9549965 -0.1768689,-0.1487332 -2.4902623,-0.1907412 -2.6675695,0 z M 7.5714626,7.4301314 A 1.4233675,1.4372942 0 0 1 6.1480951,8.8674257 1.4233675,1.4372942 0 0 1 4.7247276,7.4301314 1.4233675,1.4372942 0 0 1 6.1480951,5.9928372 1.4233675,1.4372942 0 0 1 7.5714626,7.4301314 Z")
        //.attr('id', 'ouder')
        //.attr('stroke-width', 0)
        //.style("fill", function (d, i) {
        //    return bs_kleur[i];
        //})
        //.attr('left', 100)
        //.attr('top', 100)
        .transition()
        //.delay(animatiesnelheid)
        .duration(animatiesnelheid)
        .attr("transform", function (d, i) {
            var effe = d3.select(this);
            var wat = computeDimensions(effe);
            //var schaal = 50 / d[2] * nasch;
            var schaal = 5;

            var x = 2 * d[0] + 190;
            var y = 2 * d[1] + 140;
            if (x < 0) {
                x = x - wat.width / 2;
            } else {
                x = x + wat.width / 2;
            }
            if (y < 0) {
                y = y + wat.height / 2;
            } else {
                y = y - wat.height / 2
            }
            return "translate(" + x + " " + y + ") scale(" + schaal + " " + schaal * 5 / 4 + ")";
        });

    svg4
        .selectAll('text#ouders')
        //.data(na_filt)
        //.enter()
        //.append("text")

        //.attr("pointer-events", "none")
        //.attr("font-weight", "normal")
        //.attr("id", "ouders")
        //.attr("stroke-width", "0")
        //.attr("font-size", "12")
        //.attr("y", 0)
        //.attr("x", 0)
        //.attr("text-anchor", "middle")
        //.text(function (d, i) {
        //    return d[3];
        //})
        //.attr("fill", "red")
        .transition()
        //.delay(animatiesnelheid)
        .duration(animatiesnelheid)
        .style("opacity", 1)
        .attr("transform", function (d, i) {
            var effe = d3.select(this);
            var wat = computeDimensions(effe);

            var x = 2 * d[0] + 205;
            var y = 2 * d[1] + 265;
            if (x < 0) {
                x = x - wat.width / 2;
            } else {
                x = x + wat.width / 2;
            }
            if (y < 0) {
                y = y + wat.height / 2;
            } else {
                y = y - wat.height / 2
            }
            return "translate(" + x + " " + y + ") ";
        });
    /*
    svg4
        .selectAll('path#ouder')
        .transition()
        //.delay(animatiesnelheid)
        .duration(animatiesnelheid)
        .attr("transform", function (d, i) {
            var effe = d3.select(this);
            var wat = computeDimensions(effe);
            //var schaal = 50 / d[2] * nasch;
            var schaal = 5;

            var x = 2 * d[0] + 190;
            var y = 2 * d[1] + 140;
            if (x < 0) {
                x = x - wat.width / 2;
            } else {
                x = x + wat.width / 2;
            }
            if (y < 0) {
                y = y + wat.height / 2;
            } else {
                y = y - wat.height / 2
            }
            return "translate(" + x + " " + y + ") scale(" + schaal + " " + schaal * 5 / 4 + ")";
        });

    svg4
        .selectAll('text#ouders')
        .transition()
        .style("opacity", 1)
        //.delay(animatiesnelheid)
        .duration(animatiesnelheid)
        .attr("transform", function (d, i) {
            var effe = d3.select(this);
            var wat = computeDimensions(effe);

            var x = 2 * d[0] + 205;
            var y = 2 * d[1] + 265;
            if (x < 0) {
                x = x - wat.width / 2;
            } else {
                x = x + wat.width / 2;
            }
            if (y < 0) {
                y = y + wat.height / 2;
            } else {
                y = y - wat.height / 2
            }
            return "translate(" + x + " " + y + ") ";
        });
        */
}

function selecteer(reeks) {
    if (variant > 1) {
        data_filt = data_bc[reeks].concepten;
        toel_data_filt = item_toel_bc[reeks];
        var toel_bc_filt = item_toel_bc[reeks];
    } else {
        data_filt = data_bs[reeks].concepten;
        var toel_bs_filt = item_toel_bs[reeks];
        toel_data_filt = item_toel_bs[reeks];
    }

    var na_filt = conv_nabij(data_na[reeks]);


    zet_teksten();
    gooiWeg();
    teken();
    werkBij(reeks);
    werkBijNabij(reeks);
}

function GaNaarInvoer() {
    varpad =
        window.location.protocol +
        "//" +
        window.location.host +
        window.location.pathname;
    window.location.href = varpad + "?variant=" + variant + "&" + "client_id=" + kind_id;
}

function muisOver(k, dest) {
    fase = k;
    console.log(k);
    dest1 = '#kop_' + dest;
    dest3 = '#text_' + dest;

    d3.select(dest1).html("<h2>" + toel[k].titel + "</h2>");
    d3.select(dest3).html(toel[k].inhoud);
    if (dest == 'bs_toel') {
        if (data_filt[k]) {
            svg5.selectAll("circle#circ_stop_bs").style("opacity", 1).style("stroke-opacity", 0);

        } else { svg5.selectAll("circle#circ_stop_bs").style("opacity", 0).style("stroke-opacity", 0); }
    }
    if (dest == 'bc_toel') {
        k = k - 10;
        if (data_filt[k]) {
            svg6.selectAll("circle#circ_stop_bc").style("opacity", 1).style("stroke-opacity", 0);

        } else { svg6.selectAll("circle#circ_stop_bc").style("opacity", 0).style("stroke-opacity", 0); }
    }
}


function togglezichtSL(kleur, welk) {
    /* 
      kleur = 0,1,2 groen, oranje, rood
      welk = bs of bs
      fase = begrip binnen welk
    */

    //bestemming
    var wat = '#text_' + welk + '_toel';
    d3.select(wat).html('&nbsp;');
    //circeltjes markeren
    for (i = 0; i < 3; i++) {
        var item = 'circle:nth-child(' + (i + 1) + ')';
        if (welk == 'bs') {
            if (i == kleur) {
                svg5.select(item).style("stroke-opacity", 1);
            } else {
                svg5.select(item).style("stroke-opacity", 0);
            }
            d3.select(wat).html(function () {
                //console.log('fase: ' + fase);
                var zzz = item_toel_selectie('bs', fase)[kleur];
                return zzz;
            })
        }
        if (welk == 'bc') {
            if (i == kleur) {
                svg6.select(item).style("stroke-opacity", 1);
            } else {
                svg6.select(item).style("stroke-opacity", 0);
            }
            d3.select(wat).html(function () {
                //console.log('fase: ' + fase);
                var zzz = item_toel_selectie('bc', fase)[kleur];
                return zzz;
            })
        }
    }
};
//tekst aanpassen



function togglezicht(kleur) {

    var keuze = ["groen", "oranje", "rood"];
    var a;
    //eerst allemaal onzichtbaar maken
    for (a = 0; a < 3; a++) {
        var bal = keuze[a] + "bal";
        var plus = bal + "_met";
        var dif = "div_" + keuze[a];
        var y = document.getElementById(bal);
        var z = document.getElementById(plus);
        var x = document.getElementById(dif);

        z.style.display = "none";
        y.style.display = "none";
        x.style.display = "none";

    }

    for (a = 0; a < 3; a++) {
        var bal = keuze[a] + "bal";
        var plus = bal + "_met";
        var dif = "div_" + keuze[a];
        var y = document.getElementById(bal);
        var z = document.getElementById(plus);
        var x = document.getElementById(dif);

        if (kleur == a) {
            z.style.display = "block";
            y.style.display = "none";
            if (x) {
                x.style.display = "block";
            }
        } else {
            z.style.display = "none";
            y.style.display = "block";
            if (x) {
                x.style.display = "none";
            }
        }
    }

}
//togglezicht(0);
/*
function muisKlik(i) {
  d3.select("#bs_toel").html(
    "<h2>" +
    toel[i].titel + "</h2></br>" +
    item_toel_filt[i]
  );

  togglezicht(0);
  muisOver(10, "bc_toel");
}
function muisKlik_bc(h) {
  d3.select("#bc_toel").html(
    "<h2>" +
    toel[h + 5].titel + "</h2></br>" +
    item_toel_filt_bc[h + 4]
  );
  togglezicht(0);
  muisOver(0, "bs_toel");
}
*/
function muisUit(i, dest) {
    /*
    d3.select(dest)
      .transition()
      .delay("100")
      .duration("200")
      .style("opacity", 0)
      ;
    d3.select(dest)
      .style("stroke-width", "0px")
      .html(
        "<h2>" +
        toel[i].titel +
        "</h2></br>" +
        toel[i].inhoud
      );

    d3.select(dest)
      .transition()
      .delay("100")
      .duration("200")
      .style("opacity", 1)
      ;
      */
}

function tekenKnoppen() {
    var knopgrootte = 80;
    var knophoogte = 40;
    var links = 0;

    svg3
        .selectAll("rect")
        .data(meetmomenten)
        .enter()
        .append("rect")
        .attr("id", "knoppen")
        .attr("height", knophoogte)
        .attr("width", knopgrootte)
        .attr("rx", 5)
        .attr("ry", 5)
        .style("fill", "white")
        .style("stroke", "black")
        .style("stroke-width", "2px")
        .style("stroke-opacity", 1)
        .style("stroke-alignment", "outer")
        .style("opacity", 1)
        .attr("x", function (d, i) {
            return links + i * (knopgrootte + 5) + 5;
        })
        .attr("y", 5)
        .attr("cursor", "pointer")
        /*.on("mouseover", function (d, i) {
          muisOver(this);
        })
        */
        .on("mousedown", function (d, i) {
            d3.select(this)
            //.transition()
            //.duration("500")
            //.style("stroke", "red")
            //.style("stroke-width", "4px");
            //LaatZien(i);
            selecteer(i);
        })
        /*.on("mouseout", function (d, i) {
          d3.select(this)
            .transition()
            .delay("100")
            .duration("200")
            .style("stroke", "black")
            .style("stroke-width", "2px");
        })
        */
        ;

    svg3
        .selectAll("text#knoppen")
        .data(meetmomenten)
        .enter()
        .append("text")
        .attr("pointer-events", "none")
        .attr("font-weight", "normal")
        .attr("id", "staven")
        .attr("stroke-width", "0")
        .attr("font-size", "12")
        .attr("stroke", "blue")
        .attr("y", 0.5 * knophoogte + 10)
        .attr("x", function (d, i) {
            return links + knopgrootte / 2 + i * (knopgrootte + 5) + 5;
        })
        .attr("text-anchor", "middle")
        .text(function (d, i) {
            return d;
        })
        .attr("fill", "blue");
    //extra knop voor nieuwe meting zelfde client
    svg3
        .append("rect")
        .attr("id", "knoppen_ex")
        .attr("height", knophoogte)
        .attr("width", knopgrootte)
        .attr("rx", 5)
        .attr("ry", 5)
        .style("fill", "#80003c")
        .style("stroke", "#80003c")
        .style("stroke-width", "2px")
        .style("stroke-opacity", 1)
        .style("stroke-alignment", "outer")
        .style("opacity", 1)
        .attr("x", function () {
            return links + aantalmetingen * (knopgrootte + 5) + 5;
        })
        .attr("y", 5)
        .attr("cursor", "pointer")
        .on("click", function () {
            GaNaarInvoer();
        })
        /*
        .on("mouseover", function () {
          muisOver(this);
        })
        */
        .on("mousedown", function () {
            d3.select(this)
                .transition()
                .duration("500")
                .style("stroke", "red")
                .style("stroke-width", "4px");
            //LaatZien(i);
            selecteer(i);
        })
        /*
        .on("mouseout", function (d, i) {
          d3.select(this)
            .transition()
            .delay("100")
            .duration("200")
            .style("stroke", "black")
            .style("stroke-width", "2px");
        })
        */
        ;

    svg3
        .append("text")
        .attr("pointer-events", "none")
        .attr("font-weight", "normal")
        .attr("id", "staven_ex")
        .attr("stroke-width", "0")
        .attr("font-size", "15")
        .attr("stroke", "white")
        .attr("y", 0.5 * knophoogte + 10)
        .attr("x", function (d, i) {
            return links + knopgrootte / 2 + aantalmetingen * (knopgrootte + 5) + 5;
        })
        .attr("text-anchor", "middle")
        .text("nieuw")
        .attr("fill", "white");
}

function tekenKnoppenNabij() {
    var knopgrootte = 60;
    var knophoogte = 30;
    var links = 0;

    svg4
        .selectAll("rect")
        .data(meetmomenten)
        .enter()
        .append("rect")
        .attr("id", "knoppen")
        .attr("height", knophoogte)
        .attr("width", knopgrootte)
        .attr("rx", 5)
        .attr("ry", 5)
        .style("fill", "white")
        .style("stroke", "black")
        .style("stroke-width", "2px")
        .style("stroke-opacity", 1)
        .style("stroke-alignment", "outer")
        .style("opacity", 1)
        .attr("x", function (d, i) {
            return links + i * (knopgrootte + 5) + 5;
        })
        .attr("y", 1)
        .attr("cursor", "pointer")
        /*
        .on("mouseover", function (d, i) {
          muisOver(this);
        })
        */
        .on("mousedown", function (d, i) {
            selecteer(i);
        });

    svg4
        .selectAll("text#knoppen")
        .data(meetmomenten)
        .enter()
        .append("text")
        .attr("pointer-events", "none")
        .attr("font-weight", "normal")
        .attr("id", "staven")
        .attr("stroke-width", "0")
        .attr("font-size", "10")
        .attr("stroke", "blue")
        .attr("y", 0.5 * knophoogte + 5)
        .attr("x", function (d, i) {
            return links + knopgrootte / 2 + i * (knopgrootte + 5) + 5;
        })
        .attr("text-anchor", "middle")
        .text(function (d) {
            return d;
        })
        .attr("fill", "blue");
}

function item_toel_selectie(soort, fase) {
    if (soort == 'bs') {
        return toel_data_filt[fase];
    }
    if (soort == 'bc') {
        fase = fase - 10;
        return toel_data_filt[fase];
    }
}


function demoFromHTML() {
    var pdf = new jsPDF('p', 'pt', 'letter');
    // source can be HTML-formatted string, or a reference
    // to an actual DOM element from which the text will be scraped.
    source = $('#printstuk')[0];

    // we support special element handlers. Register them with jQuery-style 
    // ID selector for either ID or node name. ("#iAmID", "div", "span" etc.)
    // There is no support for any other type of selectors 
    // (class, of compound) at this time.
    specialElementHandlers = {
        // element with id of "bypass" - jQuery style selector
        '#bypassme': function (element, renderer) {
            // true = "handled elsewhere, bypass text extraction"
            return true
        }
    };
    margins = {
        top: 80,
        bottom: 60,
        left: 40,
        width: 522
    };
    // all coords and widths are in jsPDF instance's declared units
    // 'inches' in this case
    pdf.fromHTML(
        source, // HTML string or DOM elem ref.
        margins.left, // x coord
        margins.top, { // y coord
        'width': margins.width, // max width of content on PDF
        'elementHandlers': specialElementHandlers
    },

        function (dispose) {
            // dispose: object with X, Y of the last line add to the PDF 
            //          this allow the insertion of new lines after html
            pdf.save('Test.pdf');
        }, margins
    );
}

zet_teksten();
tekenKnoppen();
teken_achtergrond();
tekenKnoppenNabij();
teken(0);
werkBij(0);