//marges:
//breedte en hoogte
//console.log(databegin);
//var form = document.getElementById("zelfscan");
//var vier20 =document.getElementById('cb20').checked;
//var vier9=document.getElementById('cb9').checked;


var margin = {top: 1, right: 1, bottom: 1, left: 1},
    width = 900 - margin.left - margin.right,
    height = 50 - margin.top - margin.bottom;
//var pad = location.protocol + '//' + location.host + location.pathname + 'images/';

var	chart1 = d3.select("#kaart1")
	.append("svg")
	.attr("width", width + margin.left + margin.right)
	.attr("height", height + margin.top + margin.bottom)
	.attr("transform", "translate(" + margin.left + "," + margin.top + ")");

var chart2 = d3.select("#kaart2")
	.append("svg")
	.attr("width",120)
    .attr("height",360)
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

var chart3 = d3.select("#hulpknop")
	.append("svg")
	.attr("width",40)
    .attr("height",40)
   	.attr("transform", "translate(0, -10)");

var myTool = d3.select(".div5")
                  .append("div")
                  .attr("class", "mytooltip")
                  .style("opacity", "1")
                  .style("display", "block")
                  .html("Hoe gebruik ik deze tool? Lees <b><a href='../hoe-gebruik-ik-mijn-gezonde-kinderopvang'>hier</a></b> de instructies");

var kopje = ['', 'scholing', 'implementatie', 'communicatie']; 

//var hulp1 = "<div id='thumbnail'><span>jajaja</span></div>"

var aantalbalken = 7;
var balkhoogte = 45;
var balklinks = 5;
var balkbreedte = 150;
var animatiesnelheid = 2000;
var hoogteoffset = 10;
var balkrechts = balklinks + balkbreedte;
var cirkelgrootte = 35;
var balkmarge = 5;

//gradient voor balken
var balkkleur = chart1.append("defs").append("linearGradient").attr("id", "balkkleur");

balkkleur.selectAll("stop")
	.data([["0%", "#fdd16e"],  ["100%", "#c9e8fc"]])
	.enter().append("stop")
	.attr("offset", function(d) { return d[0]; })
	.attr("stop-color", function(d) { return d[1]; });
//andere kleuren

//bepalen of dit een themapagina is of niet
if (laag == 0) { thema = 'ja'; } else { thema = 'nee'; }
//console.log(thema);

if (thema == 'nee'){
 var knoptoel = 
['<h3>Coach</h3>Hoe begin je met <span class="gk">Gezonde Kinderopvang</span>?</br></br>Je bent eigenlijk al begonnen! (door coach te (gaan) worden). </br></br>Maak bekend bij alle belanghebbenden wat jij op jouw kinderopvang wilt gaan doen hiermee, en begin met het maken van een Plan van Aanpak.',
'<h3>Scholing</h3>Als coach <span class="gk">Gezonde Kinderopvang</span> ga je aan de slag met het de scholing Een Gezonde Start.</br></br>Hiermee vergroot je de kennis en vaardigheden van je collega’s op het gebied van een gezonde leefstijl. Deze stappen helpen je om de scholing zo goed mogelijk uit te voeren.', 
'<h3>Profiel</h3>Door het opstellen van een organisatieprofiel krijg je inzicht in de gezondheidsgegevens van kinderen, de wensen en behoeften van medewerkers en ouders, beleidsprioriteiten en mogelijke quick wins. </br></br>Dit helpt bij de keuze van de gezondheidsthema’s waarmee je aan de slag gaat. Maar hoe stel je zo’n organisatieprofiel op? Deze stappen helpen je op weg. ', 
'<h3>Thema kiezen</h3>Welk thema past bij jouw organisatie/locatie?</br></br>Dat hangt af van alle voorgaande stappen, en in het bijzonder van het profiel. Wat zijn onderwerpen die belangrijk zijn voor ouders, kinderen, collega’s of gemeente?</br></br>Kies een thema om verdere vervolgstappen voor dat onderwerp te zien...', 
'<h3>Borgen</h3>Werken aan een gezonde leefstijl is nooit echt klaar. Blijvende aandacht is nodig. Zo kan beleid na enige tijd toe zijn aan een update, raken gemaakte afspraken op de achtergrond, zijn er andere thema’s waar winst te behalen is. </br></br>Onderstaande stappen helpen je om een gezonde leefstijl op de agenda te houden. '];  
}else{
 var knoptoel = [
'<h3>Opstellen</h3>Hier vind je tips voor het opstellen van een aanpak over dit specifieke onderwerp', 
'<h3>Uitvoeren</h3>Hoe doe je het? Hier vind je tips voor het uitvoeren van acties in je plan van aanpak', 
'<h3>Evalueren</h3>Gaat het goed? Hier vind je tips voor het evalueren van je aanpak'];   
}

var colors = ['#fdd16e', '#dcbfdd', '#cf8bb2', '#e7e24a', '#c9e8fc', '#a3cbee']; 
teken();

werkGrafiekbij(100);

LaatZien(0);
               

//LaatAlgemeenKnopZien(laag);
//ShowThemaKnoppen(1);

function teken(){       
	//knoppen zelf:
    chart1
		.selectAll("rect")
		.data(datastring)
		.enter()
		.append("rect")
        .attr("id", "knop")
		.attr("height", balkhoogte - balkmarge)
		.attr("width", balkbreedte)
		.attr("rx", 5)
		.attr("ry", 5)
		.style("fill", "#2b98ff")
		.style("stroke", "yellow")
		.style("stroke-width", "0px")
        .style("stroke-opacity", 1)
        .style("stroke-alignment", "outer")
		.style("opacity", 1)
		.attr("y", balkmarge)
		.attr("x", function (d, i) { return i * (balkbreedte + balkmarge) + balklinks })
        .on('click', function (d, i) { LaatZien(i); })
		.on('mouseover',
		function (d, i) {
		    d3.select(this).style("cursor", "pointer");
		    d3.select(this).style("stroke-width", "5px");
		})

        .on('mouseout', function (d) {
            d3.select(this).style("cursor", "default");
            d3.select(this).style("stroke-width", "0px");

        });

    d3.select("#kop")
    .append("h3")
    .html(kopje[0])
        ;	
	
    //streepjes zelf.
    chart1
		.selectAll("rect#bm")
		.data(datastring)
		.enter()
		.append("rect")
		.attr("height", balkhoogte - balkmarge)
		.attr("width", 0)
		.attr("id", "bm")
		.attr("rx", 5)
		.attr("ry", 5)
		.attr("x", function(d ,i) {return i * (balkbreedte+balkmarge)+balklinks})
		.attr("y", balkmarge)
		.style("fill", "#103960")
		.style("opacity", 1)
        .on('click', function(d ,i) {LaatZien(i);})
        .style("stroke-opacity", 1)
		.style("stroke", "yellow")
		.style("stroke-width", "0px")
        .style("stroke-alignment", "outer")
        .on('mouseover',
		function (d, i) {
		    d3.select(this).style("cursor", "pointer");
		    d3.select(this).style("stroke-width", "5px");   
		})

        .on('mouseout', function (d) {
            d3.select(this).style("cursor", "default");
            d3.select(this).style("stroke-width", "0px");
         });


	//tekst op knoppen:
    chart1
		.selectAll("text#staven")
		.data(datastring)
		.enter()
		.append("text")
        .attr("pointer-events","none")
		.attr("font-weight", "normal")
		.attr("id", "staven")
		.attr("stroke-width", "0")
		.attr("font-size", "15")
		.attr("stroke", "blue")
		.attr("y", 0.5*balkhoogte+balkmarge)
		.attr("x", function(d ,i) {return (i * (balkbreedte+balkmarge))+0.5*balkbreedte + balklinks})
		.attr("text-anchor", "middle")
		.text(function(d,i) {return d.knoptekst})
		.attr("fill","white")
        ;

	// benchmarkstreepjes
	// uitleg bij benchmarktreepjes
    var toeltip = d3.tip()
		.attr('class', 'toeltip')
		.offset([-10, 10])
		.html(function(d, i) {return "jajaja</span>";  });

//THEMA's KIEZEN
//achtergrond ikonen: gekleurde vierkantje er omheen

    var ikonenAsArray = Object.keys(ikonen).map(function(key) {
        return [Number(key), ikonen[key]];
    });

    chart2
		.selectAll("rect")
		.data(ikonenAsArray)
		.enter()
		.append("rect")
		.attr("height", 50)
		.attr("width", 50)
		.attr("rx", 5)
		.attr("ry", 5)
		.style("stroke", "white")
		.style("stroke-width", "5px")
        .style("stroke-opacity", 1)
        .style("stroke-alignment", "inner")
        .attr("id", "a-ikonen")
		.style("fill", "white")
        .style("opacity", "0.5")
        //.attr("type", "submit")
        .on('click', function (d, i) { GaNaarThema(i); })
        .attr("x", function (d, i) { if (isEven(i)) { return 5; } else { return 65; } })
        .attr("y", function (d, i) { return 5 + Math.floor(i / 2) * 60; })
        .on('mouseover',
		function (d, i) {
		    d3.select(this).style("cursor", "pointer");
		    toeltip.show;
		})
        .on('mouseout', function (d) {
            d3.select(this).style("cursor", "default");
            toeltip.hide;
        })
        ;
        
        //chart2.call(toeltip);      
     
    //ikonen zelf: images
    chart2
    .selectAll("themaknops")
    .data(ikonenAsArray)
    .enter()
    .append('image')
    .attr("title", "jaja")
    //.attr("type", "submit")
    //.attr("id", "ikonen")
    .attr("x", function (d, i) {
        if (isEven(i)) {
            return 6;
        } else {
            return 66;
        }
    })
    .attr("y", function (d, i) {
        return 5+Math.floor(i / 2) * 60;
    })
    .on('click', function(d ,i) {GaNaarThema(i);})
    .attr("height", 47)
    .attr("width", 47)
    .attr("xlink:href", function (d, i) { return d[1].figuur })
    
    .on('mouseover',
		function (d, i)
		{
		    d3.select(this).style("cursor", "pointer");
        })
    .on('mouseout', function (d) 
        { 
            d3.select(this).style("cursor", "default");
        })
    
     ;

    //kleur ikonen randje aanpassen aan opgehaalde scores.
     chart2
		.selectAll("rect#a-ikonen")
		.transition().duration(animatiesnelheid / 2)
   		.style("opacity", "0.8")
        .style("stroke", function (d, i) { 
            if (d[1].score < 1)
            { return "white"; }
            else {
                if (d[1].score < 17) {
                    return "orange";
                } else
                {return "green";}
            }
        })
	    ;

}


function isEven(n) {
   return n % 2 == 0;
}

async function playAudio() {
  var audio = new Audio('https://www.monitorgezondekinderopvang.nl/wp-content/plugins/mgko-d3/js/cheer.mp3');  
  audio.type = 'audio/mp3';

  try {
    await audio.play();
    console.log('Playing...');
  } catch (err) {
    console.log('Failed to play...' + error);
  }
}

function tip(hier){
//    console.log(hier);
    //hier = hier - 1;
    if (itemstring[hier].tip !=null){
        tekst = "<H2>Tip(s):</h2>" + itemstring[hier].tip;
        d3.select("#tips").attr("fill", '' ); //colour based on the data
    }
    else
    {
        tekst = ""; 
        d3.select("#tips").attr("fill", 'white' ); //colour based on the data
    }
    d3.select("#tips").html(tekst);
}

function getUrlVars() {
    var zwadi = window.location.href;
    var terug = zwadi.substring(zwadi.indexOf('thema=')+6); 
    return terug;
}


function GaNaarThema(toon) {  
	
	// get thema id from url
	var themaId = getUrlVars(); 
    //overschakelen naar themapagina
	varpad = window.location.protocol + '//' + window.location.host + window.location.pathname;
    window.location.href = varpad + '?thema=' + toon;

} 



function LaatZien(toon) {
   
    d3.select("#kop").selectAll("h3").remove();
    d3.select("#tips").html(knoptoel[toon]);
    for (z=0;z<5;z++)
    {
        hier = 'fase_' + z;
        var x = document.getElementById(hier);
        if (z == toon) 
        { 
            x.style.display = "block";        
            d3.select("#kop").append("h3").html(datastring[z].doeltekst);
        }else
        {
            if (x !== null){
                x.style.display = "none";             
            }
        }
    }
    
}
function berekenScore(i){
    //i = i - 1;

    var score = 0;
    var startitem = datastring[i].startitem;            
    var aantal = datastring[i].aantalitems;
    var einditem = parseInt(startitem) + parseInt(aantal);


    for (z = startitem; z < einditem; z++) {
        if (z < 200) {
            var elem = "cb" + z;
            if (document.getElementById(elem).checked) {
                score++;
            }
        }
    }
    return Math.round(100*(score/aantal));
}
function GaNaarTabProfiel(){
    LaatZien(2);
}
function PasIkoonScoreAan(thema){
        var score = 0;
        for (z=0;z<24;z++)
        {
            var elem = "cb" + z;
            if (document.getElementById(elem))
            {
                if (document.getElementById(elem).checked)
                {
                    score++;
                }
            }
        }

        return score;
}

function anibal(tog){
    var x= document.getElementById('ballon');
    var y= document.getElementById('melding');
    if (tog > 0 )
       {
           playAudio();          
           x.className = 'joehoe';
           x.style.display = "block";      
           y.className = 'joehoe';
           y.style.display = "block";      
       } 
       else 
       { 
           x.className = 'nope';
           x.style.display = "none";  
           y.className = 'nope';
           y.style.display = "none";  
       }
}

function checkVier(z){

  if (z == 9){elem='cb9';}
  else if (z==20){elem='cb20';} 
  else {return;}

  if (document.getElementById(elem).checked) {anibal(1);}
  else{anibal(0);}
    
  } 

function werkGrafiekbij(element) {
    checkVier(element);
    xScale = d3.scale.linear().domain([0, 100]).range([0, balkbreedte + balklinks]);

    chart1
		.selectAll("rect#bm")
		.transition().duration(animatiesnelheid)
   		//.style("opacity", function (d, i) { return berekenScore(i) / 100; })
		.attr("width", function (d, i) { return (xScale(berekenScore(i)) - balklinks); });

        var str = window.location.href;
        var n = str.indexOf("?");
        thema = parseInt(str.substr(n+7));    
        ikonen[thema].score = PasIkoonScoreAan();


    chart2
		.selectAll("rect#a-ikonen")
		.transition().duration(animatiesnelheid / 2)
   		.style("opacity", "0.8")
        .style("stroke", function (d, i)
        {
            //ikonen[i].score=
            if (i < 11)
            {
                if (ikonen[i].score < 1)
                {
                    return "white";
                }
                else
                {
                    if (ikonen[i].score < 21)
                    {
                        return "orange";
                    }
                    else
                    {
                        return "green";
                    }
                }
            } else
            { 
                if (ikonen[i].score < 1)
                {
                    return "white";
                }
                else
                {
                    if (ikonen[i].score < 24)
                    {
                        return "orange";
                    }
                    else
                    {
                        return "green";
                    }
                }

            }
        })
	    ;
}

var elements = document.getElementsByClassName("regular-checkbox");

var myFunction = function() {
    //var attribute = this.getAttribute("id");
    //alert(attribute);

    var themaId = getUrlVars();
    var bijwerk = 'bewaar=nu&thema=' + themaId + '&' + jQuery('form').serialize();
    //opslaan data 	
  	jQuery.ajax({
		type: "POST",
		url: window.location.origin + window.location.pathname,
		data: bijwerk,
		timeout: 5000,
		cache: false
	})
	.done(function(result) {
	    console.log('okidoki. sent response completed, received return result'); // dev/test only
	})
	.fail(function(result) {
		console.log('onee. sent response failed');
	});
};

for (var i = 0; i < elements.length; i++) {
    elements[i].addEventListener('click', myFunction, false);
}