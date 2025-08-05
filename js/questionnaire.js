
var knoppen = document.getElementsByClassName("categoryButton");
var containers = document.getElementsByClassName("itemsContainerSub");
//var volg = document.getElementsByClassName("orbit-next");
//var vori = document.getElementsByClassName("orbit-previous");
for (var i = 0; i < knoppen.length; i++) {
    knoppen[i].addEventListener("click", myFunction, false);
}

//volg[0].addEventListener("click", myFunction2, false);

function myFunction(e) {
    var ss = this.id.substr(-1, 1) - 1;
    for (var i = 0; i < containers.length; i++) {
        containers[i].style.display = "none";
    }
    containers[ss].style.display = "block";

    //alert(ss);
}

function myFunction2(e) {}

//$(document).ready(function() {
$(document).foundation();
//});
containers[0].style.display = "block";

//var elem1 = new Foundation.Orbit($("#itemsOrbitSub1"), "reflow");