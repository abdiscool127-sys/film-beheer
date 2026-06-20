<?php
/**
 * FilmRepository
 * 
 * Deze klasse handelt alle DIRECT database-communicatie af.
 * Zij voert SQL-queries uit en geeft resultaten terug als associatieve arrays.
 * Dit staat bekend als het "Repository Pattern".
 */
require_once __DIR__ . '/Database.php';

class FilmRepository
{
    private $db; // PDO-database-verbinding
    private $filmColumns = null;

    /**
     * Constructor: zet database-verbinding klaar
     */
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Haal ALLE films op uit database
     * 
     * Als $search niet leeg is, filter op titel (LIKE)
     * LIKE '%zoekterm%' betekent: titel bevat de zoekterm
     * 
     * @param string|null $search Optionele zoekterm
     * @return array Array van films (elke film is een associatief array)
     */
    public function getAll($search = null)
    {
        if ($search) {
            // Prepared statement tegen SQL-injection
            $stmt = $this->db->prepare(
                'SELECT f.*, g.genre_naam
                 FROM films f
                 LEFT JOIN genres g ON f.genre_id = g.id
                 WHERE f.titel LIKE :s
                 ORDER BY f.id DESC'
            );
            // Bind de parameter zodat speciale tekens veilig worden verwerkt
            $stmt->execute([':s' => "%$search%"]);
        } else {
            // Geen voorzorgsmaatregel nodig hier (geen user-input)
            $stmt = $this->db->query(
                'SELECT f.*, g.genre_naam
                 FROM films f
                 LEFT JOIN genres g ON f.genre_id = g.id
                 ORDER BY f.id DESC'
            );
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Haal ÉÉN film op op basis van ID
     * 
     * @param int $id Film-ID
     * @return array|false Film-gegevens of false als niet gevonden
     */
    public function getById($id)
    {
        $stmt = $this->db->prepare(
            'SELECT f.*, g.genre_naam
             FROM films f
             LEFT JOIN genres g ON f.genre_id = g.id
             WHERE f.id = :id'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Haal alle genres op voor dropdowns
     *
     * @return array
     */
    public function getGenres()
    {
        // Eenvoudige query zonder user-input: direct uitvoeren met query()
        $stmt = $this->db->query('SELECT id, genre_naam FROM genres ORDER BY genre_naam ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Haal films op gebaseerd op genre (naam of id)
     *
     * @param string|int $genre Genre-naam (deels) of genre-id
     * @return array Array van films
     */
    public function getByGenre($genre)
    {
        if (empty($genre)) return [];

        // Als numeriek: behandel als genre_id
        // Als de gebruiker een id opgeeft (numeriek), filter direct op genre_id
        if (is_numeric($genre)) {
            $stmt = $this->db->prepare(
                'SELECT f.*, g.genre_naam
                 FROM films f
                 LEFT JOIN genres g ON f.genre_id = g.id
                 WHERE f.genre_id = :gid
                 ORDER BY f.id DESC'
            );
            $stmt->execute([':gid' => (int)$genre]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Anders: zoek op genre_naam LIKE
        // Anders: zoek op genre-naam met LIKE (deelmatching).
        // Dit is handig voor zoekvelden waarin gebruikers 'Actie' of 'Com' typen.
        $stmt = $this->db->prepare(
            'SELECT f.*, g.genre_naam
             FROM films f
             LEFT JOIN genres g ON f.genre_id = g.id
             WHERE g.genre_naam LIKE :g
             ORDER BY f.id DESC'
        );
        $stmt->execute([':g' => "%$genre%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Zoek genre-id op basis van (deels) genre-naam.
     * Retourneert NULL als geen match.
     *
     * @param string $name
     * @return int|null
     */
    public function findGenreIdByName($name)
    {
        if (empty($name)) return null;

        // Neem alleen de eerste genre als er meerdere zijn (vb. "Action, Drama")
        $parts = explode(',', $name);
        $first = trim($parts[0]);

        // Zoek op (gedeeltelijke) match met LIKE zodat varianten gevonden worden
        // (bijv. 'Actie' wordt gevonden wanneer in DB 'Actie/Thriller' staat).
        // We beperken tot LIMIT 1 omdat we slechts één id nodig hebben.
        $stmt = $this->db->prepare('SELECT id FROM genres WHERE genre_naam LIKE :n LIMIT 1');
        $stmt->execute([':n' => "%$first%"]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($res) {
            return (int)$res['id'];
        }

        $insert = $this->db->prepare('INSERT INTO genres (genre_naam) VALUES (:genre_naam)');
        $insert->execute([':genre_naam' => $first]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Haal de kolommen van de films-tabel op en cache ze.
     *
     * Hiermee blijven INSERT/UPDATE-queries werken als een kolom nog niet
     * in de database aanwezig is.
     *
     * @return array
     */
    private function getFilmColumns()
    {
        if ($this->filmColumns !== null) {
            return $this->filmColumns;
        }

        // Vraag MySQL welke kolommen daadwerkelijk aanwezig zijn.
        // Dit maakt de INSERT/UPDATE dynamisch en tolerant voor schema-verschillen.
        // SHOW COLUMNS: vraag direct aan MySQL welke velden er zijn.
        // Dit maakt de insert/update dynamisch en voorkomt errors wanneer
        // een optioneel veld ontbreekt in een bepaalde installatie.
        $stmt = $this->db->query('SHOW COLUMNS FROM films');
        $columns = [];

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $columns[] = $row['Field'];
        }

        $this->filmColumns = $columns;
        return $columns;
    }

    /**
     * Voeg NIEUWE film in database in
     * 
     * Gebruikt prepared statement (:titel, :jaar, etc.) voor veiligheid.
     * 
     * @param array $data Array met 'titel', 'jaar', 'genre_id', 'beschrijving'
     * @return bool true bij succes, false bij fout
     */
    public function create($data)
    {
        $availableColumns = $this->getFilmColumns();
        $columns = [];
        $placeholders = [];
        $values = [];

        // Loop door de mogelijke kolommen en neem alleen die mee die ook
        // daadwerkelijk in de tabel aanwezig zijn (robustheid bij schema-varianten).
        foreach (['titel', 'jaar', 'genre_id', 'beschrijving', 'poster', 'rating'] as $column) {
            if (in_array($column, $availableColumns, true)) {
                $columns[] = $column;
                $placeholders[] = ':' . $column;
                // Gebruik null wanneer waarde ontbreekt; dit voorkomt fouten
                // bij velden die optioneel zijn.
                $values[':' . $column] = $data[$column] ?? null;
            }
        }

        // Bouw de INSERT statement dynamisch en voer uit met prepared statement.
        $sql = 'INSERT INTO films (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    /**
     * WIJZIG een bestaande film in database
     * 
     * @param int $id Film-ID
     * @param array $data Array met nieuwe 'titel', 'jaar', 'genre_id', 'beschrijving'
     * @return bool true bij succes
     */
    public function update($id, $data)
    {
        $availableColumns = $this->getFilmColumns();
        $sets = [];
        $values = [':id' => $id];

        // Bereid de SET-clausules voor alleen bestaande kolommen voor.
        foreach (['titel', 'jaar', 'genre_id', 'beschrijving', 'poster', 'rating'] as $column) {
            if (in_array($column, $availableColumns, true)) {
                $sets[] = $column . ' = :' . $column;
                $values[':' . $column] = $data[$column] ?? null;
            }
        }

        // Voer de update uit met prepared statement zodat waarden veilig gebonden
        // en correct ge-escaped worden.
        $sql = 'UPDATE films SET ' . implode(', ', $sets) . ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    /**
     * VERWIJDER een film uit database
     * 
     * @param int $id Film-ID
     * @return bool true bij succes
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare('DELETE FROM films WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
