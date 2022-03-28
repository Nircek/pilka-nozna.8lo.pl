function laduj(numer_zdjecia) {
    // Podmiana zdjęcia w podglądzie
    var zdjecie = document.getElementById(numer_zdjecia);
    var sciezka = zdjecie.getAttribute("srcfull");
    var glowne_zdjecie = document.getElementById('glowne_zdjecie');
    glowne_zdjecie.value = numer_zdjecia;
    glowne_zdjecie.src = sciezka;
}

function lewo() {
    var podglad = document.getElementById('glowne_zdjecie');
    var stare_zdjecie = podglad.value;
    stare_zdjecie = Number(stare_zdjecie);
    var nowe_zdjecie = stare_zdjecie - 1;
    var nowa_sciezka = document.getElementById(nowe_zdjecie);
    nowa_sciezka = nowa_sciezka.getAttribute('srcfull');
    podglad.src = nowa_sciezka;
    podglad.value = nowe_zdjecie;
}

function prawo(liczba_zdjec) {
    var podglad = document.getElementById('glowne_zdjecie');
    var stare_zdjecie = podglad.value;
    stare_zdjecie = Number(stare_zdjecie);
    var nowe_zdjecie = stare_zdjecie + 1;
    var nowa_sciezka = document.getElementById(nowe_zdjecie);
    nowa_sciezka = nowa_sciezka.getAttribute('srcfull');
    podglad.src = nowa_sciezka;
    podglad.value = nowe_zdjecie;

}
window.onload = () => laduj(0);
