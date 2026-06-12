<?php
/**
 * ApiService
 * 
 * Dit handelt communicatie met externe API af (OMDB).
 * OMDB (http://www.omdbapi.com/) geeft informatie over films op basis van titel.
 */
class ApiService
{
    private $config;

    /**
     * Constructor: laad config
     */
    public function __construct()
    {
        $this->config = require __DIR__ . '/../config.php';
    }

    /**
     * Haal EXTERNE FILM-DATA op via OMDB API
     * 
     * Als API-key leeg is in config.php, geeft DEMO-GEGEVENS terug.
     * Dit is handig voor testen zonder echte API-key.
     * 
     * @param string $title Filmtitel om op te zoeken
     * @return array|null Array met filmgegevens (Title, Year, Plot, imdbRating, etc.)
     *                     OF null als verbinding mislukt
     */
    public function fetchByTitle($title)
    {
        // Als er geen API-key is ingesteld, geef voorbeelddata terug (demo-modus)
        if (empty($this->config->api_key)) {
            // Demo/mock data zodat de UI kan laten zien hoe externe data eruitziet
            return [
                'Title' => $title,
                'Year' => '2020',
                'Genre' => 'Drama, Actie',
                'Director' => 'Demo Regisseur',
                'Actors' => 'Acteur A, Acteur B',
                'Plot' => 'Dit is voorbeeld-plot voor demonstratiedoeleinden.',
                'imdbRating' => '7.5',
                'Poster' => ''
            ];
        }
        
        // Bouw OMDB API-link
        $url = $this->config->api_url . '?apikey=' . urlencode($this->config->api_key) . '&t=' . urlencode($title);
        
        // Beperk timeout naar 5 seconden (sneller falen dan oneindig wachten)
        $opts = ['http' => ['timeout' => 5]];
        $context = stream_context_create($opts);
        
        // Voer HTTP-request uit
        $resp = @file_get_contents($url, false, $context);
        if ($resp === false) return null; // Verbinding mislukt
        
        // Parse JSON-respons
        $data = json_decode($resp, true);
        return $data;
    }
}
