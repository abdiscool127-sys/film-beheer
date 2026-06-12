<?php
/**
 * FilmService
 * 
 * Dit is de "Business Logic" laag.
 * Zij bevat validatie, regelwerk, en coördineert tussen Repository en frontend.
 * De frontend spreekt ALLEEN met Service, nooit rechtstreeks met Repository.
 */
require_once __DIR__ . '/FilmRepository.php';

class FilmService
{
    private $repo; // FilmRepository-instance

    /**
     * Constructor: zet repository klaar
     */
    public function __construct()
    {
        $this->repo = new FilmRepository();
    }

    /**
     * VALIDEER film-gegevens
     * 
     * Controleert of titel ingevuld is en of jaar een getal is.
     * Geeft array van foutberichten terug (leeg = alles OK).
     * 
     * @param array $data Film-gegevens
     * @return array Array van fout-strings (leeg = geldig)
     */
    public function validate($data)
    {
        $errors = [];
        
        // Titel is verplicht
        if (empty($data['titel'])) {
            $errors[] = 'Titel is verplicht.';
        }
        
        // Jaar moet een getal zijn (als ingevuld)
        if (!empty($data['jaar']) && !is_numeric($data['jaar'])) {
            $errors[] = 'Jaar moet een nummer zijn.';
        }
        
        return $errors;
    }

    /**
     * Haal ALLE films op (met optionele zoekopdracht)
     * 
     * @param string|null $search Zoekterm
     * @return array Array van films
     */
    public function list($search = null)
    {
        return $this->repo->getAll($search);
    }

    /**
     * Haal ÉÉN film op
     * 
     * @param int $id Film-ID
     * @return array Film-gegevens
     */
    public function get($id)
    {
        return $this->repo->getById($id);
    }

    /**
     * Maak NIEUWE film aan
     * 
     * - Valideert eerst
     * - Als geldig: sla op in database via Repository
     * - Geeft foutarray terug (leeg = success)
     * 
     * @param array $data Film-gegevens
     * @return array Foutberichten (leeg = succes)
     */
    public function create($data)
    {
        // Valideer
        $errors = $this->validate($data);
        if ($errors) return $errors; // Stop als fouten

        // Lege genre-keuze opslaan als NULL in plaats van lege string
        if (array_key_exists('genre_id', $data) && $data['genre_id'] === '') {
            $data['genre_id'] = null;
        }
        // Zorg dat poster key bestaat (kan null zijn)
        if (!array_key_exists('poster', $data)) {
            $data['poster'] = null;
        }
        // Zorg dat rating key bestaat
        if (!array_key_exists('rating', $data)) {
            $data['rating'] = null;
        }
        
        // Geen fouten: sla op en geef leeg array terug
        $this->repo->create($data);
        return [];
    }

    /**
     * WIJZIG bestaande film
     * 
     * @param int $id Film-ID
     * @param array $data Nieuwe film-gegevens
     * @return array Foutberichten (leeg = succes)
     */
    public function update($id, $data)
    {
        $errors = $this->validate($data);
        if ($errors) return $errors;

        if (array_key_exists('genre_id', $data) && $data['genre_id'] === '') {
            $data['genre_id'] = null;
        }
        if (!array_key_exists('poster', $data)) {
            $data['poster'] = null;
        }
        if (!array_key_exists('rating', $data)) {
            $data['rating'] = null;
        }

        $this->repo->update($id, $data);
        return [];
    }

    /**
     * Haal alle genres op voor dropdowns
     *
     * @return array
     */
    public function getGenres()
    {
        return $this->repo->getGenres();
    }

    /**
     * Haal films op gefilterd op genre (naam of id)
     * @param string|int $genre
     * @return array
     */
    public function listByGenre($genre)
    {
        return $this->repo->getByGenre($genre);
    }

    /**
     * Importeer filmgegevens afkomstig van externe API naar database.
     * Retourneert array met foutmeldingen (leeg = succes).
     *
     * @param array $apiData
     * @return array
     */
    public function importFromApi($apiData)
    {
        // Zet data om naar intern formaat
        $data = [
            'titel' => $apiData['Title'] ?? '',
            'jaar' => $apiData['Year'] ?? null,
            // Bewaar korte beschrijving (plot) en sla rating apart in rating-veld
            'beschrijving' => $apiData['Plot'] ?? null,
            'poster' => !empty($apiData['Poster']) && $apiData['Poster'] !== 'N/A' ? $apiData['Poster'] : null,
            'rating' => $apiData['imdbRating'] ?? null,
        ];

        // Probeer genre te mappen naar bestaande genre_id
        $genreId = null;
        if (!empty($apiData['Genre'])) {
            $genreId = $this->repo->findGenreIdByName($apiData['Genre']);
        }
        $data['genre_id'] = $genreId;

        // Gebruik bestaande create()-flow (valideert automatisch)
        return $this->create($data);
    }

    /**
     * VERWIJDER film
     * 
     * @param int $id Film-ID
     * @return bool true=succes
     */
    public function delete($id)
    {
        return $this->repo->delete($id);
    }
}
