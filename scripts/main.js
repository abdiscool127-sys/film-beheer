/**
 * CLIENT-SIDE VALIDATIE
 * 
 * Deze functies voeren basisvalidatie uit op de frontend
 * VOORDAT je form naar de server wordt gestuurd.
 * Dit is sneller en geeft gebruiker direct feedback.
 * 
 * Let op: Server-side validatie (FilmService.php) is NOODZAKELIJK!
 * Nooit blind vertrouwen op client-side validatie.
 */

/**
 * Valideer film-formulier
 * 
 * Controleert:
 * - Titel is niet leeg
 * - Jaar is leeg OF een getal
 * 
 * Geeft alert als validatie mislukt.
 * Retourneert false = formulier NIET verzenden.
 * 
 * @param HTMLFormElement form Het formulier
 * @return boolean true=verzend, false=stop
 */
function validateForm(form) {
    // Haal titel-veld op en verwijder whitespace
    const titel = form.querySelector('[name="titel"]').value.trim();
    if (!titel) {
        alert('Vul een titel in.');
        return false; // Stop form-submit
    }

    // Haal jaar-veld op
    const jaar = form.querySelector('[name="jaar"]').value.trim();
    // Als jaar ingevuld EN niet-numeriek: fout
    if (jaar && isNaN(jaar)) {
        alert('Jaar moet een getal zijn.');
        return false; // Stop form-submit
    }

    // Alle checks passed: vertuuur formulier
    return true;
}
