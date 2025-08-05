var kleur = ["#058DC7", "#50B432", "#ED561B", "#DDDF00", "#24CBE5"];

function doerij(num) {
  toon = num + 1;
  var regel = "regel_" + toon;
  var x = document.getElementById(regel);
  x.style.display = 'block';
}


function verbergrijen() {
  var rijen = document.getElementsByClassName('inputregel');
  for (var i = 0; i < rijen.length; ++i) {
    rijen[i].style.display = 'none';
  }
}

function voegrijtoe() {
  var tablep = document.getElementById("nabij");
  var nextIndexp = tablep.tBodies[0].childNodes.length - 3;

  var newRow = document.createElement("TR");
  var f1 = document.createElement("TD");
  var f1i = document.createElement("input");
  var f2 = document.createElement("TD");
  var f2i = document.createElement("input");


  f1i.setAttribute("id", "naam_" + nextIndexp);
  f1i.setAttribute("name", "naam_" + nextIndexp);
  f1i.setAttribute("placeholder", "naam");
  //f1i.setAttribute("th:field", "${questionAnswerSet.naa}");
  //f1i.setAttribute("style", "resize: none; width: 90%;");
  //f1i.setAttribute("onchange", "voegrijtoe()");
  f1i.setAttribute("type", "text");
  f2.setAttribute("colspan", "2");
  f2.setAttribute("align", "middle");
  f2i.setAttribute("id", "slide" + nextIndexp);
  f2i.setAttribute("class", "slider");
  f2i.setAttribute("type", "range");
  f2i.setAttribute("name", "slide_" + nextIndexp);
  f2i.setAttribute("min", "1");
  f2i.setAttribute("max", "100");

  f1.appendChild(f1i);
  f2.appendChild(f2i);

  newRow.appendChild(f1);
  newRow.appendChild(f2);
  tablep.tBodies[0].appendChild(newRow);
}
