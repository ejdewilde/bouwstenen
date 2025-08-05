document.getElementById("WerksoortSelect").value = prefildat.WerksoortSelect;
radiobtn = document.getElementById(prefildat.video);
radiobtn.checked = true;
obsdat = prefildat.observatie_datum;
lftmaa = prefildat.LeeftijdMaand;
lftjaa = prefildat.LeeftijdJaar;
maandeneraf = lftmaa + 12 * lftjaa;
pasleeftijdaan(obsdat);

function pasleeftijdaan(obsie) {
    const maanden = 1000 * 60 * 60 * 24 * 7 * 52 / 12;
    const today = new Date();
    const date1 = new Date(obsie);
    const gebdat = subtractMonths(maandeneraf, date1);
    const leeftijd = Math.abs(today - gebdat);
    const leeftijdinmaanden = leeftijd / maanden;
    const leeftijdinjaren = Math.floor(leeftijdinmaanden / 12);
    const lftmaandeninjaar = Math.floor(leeftijdinmaanden - 12 * leeftijdinjaren);
    document.getElementById("LeeftijdJaar").value = leeftijdinjaren;
    document.getElementById("LeeftijdMaand").value = lftmaandeninjaar;
}

function subtractMonths(numOfMonths, date = new Date()) {
    date.setMonth(date.getMonth() - numOfMonths);

    return date;
}

var bijwerkenleeftijd = document.getElementById("observatie_datum");

bijwerkenleeftijd.addEventListener("change", function() {
    const date1 = bijwerkenleeftijd.value;
    pasleeftijdaan(date1);
});