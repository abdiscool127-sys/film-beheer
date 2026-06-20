<?php
require_once __DIR__ . '/Database.php';

class ActorRepository
{
    private $db;

    public function __construct()
    {
        // Gebruik een aparte database voor actors. Database accepteert optionele
        // naam waardoor we meerdere DB's vanuit dezelfde Database-class kunnen
        // benaderen.
        $database = new Database('film_actors');
        $this->db = $database->getConnection();
    }

    public function getAll($search = null)
    {
        if ($search) {
            // Zoek op naam met LIKE; gebruik prepared statement voor veiligheid
            $stmt = $this->db->prepare('SELECT * FROM actors WHERE naam LIKE :s ORDER BY id DESC');
            $stmt->execute([':s' => "%$search%"]);
        } else {
            $stmt = $this->db->query('SELECT * FROM actors ORDER BY id DESC');
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        // Eenvoudige insert; wij binden expliciet velden zodat NULL/lege waarden
        // correct in de database terechtkomen.
        $stmt = $this->db->prepare('INSERT INTO actors (naam, geboortedatum, bio) VALUES (:naam, :geboortedatum, :bio)');
        return $stmt->execute([
            ':naam' => $data['naam'] ?? null,
            ':geboortedatum' => $data['geboortedatum'] ?? null,
            ':bio' => $data['bio'] ?? null,
        ]);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM actors WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare('DELETE FROM actors WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function update($id, $data)
    {
        // Update: bind alle velden en voer de query uit. Voor eenvoudige tabellen
        // is dit voldoende; bij complexere relaties is extra logica nodig.
        $stmt = $this->db->prepare('UPDATE actors SET naam = :naam, geboortedatum = :geboortedatum, bio = :bio WHERE id = :id');
        return $stmt->execute([
            ':naam' => $data['naam'] ?? null,
            ':geboortedatum' => $data['geboortedatum'] ?? null,
            ':bio' => $data['bio'] ?? null,
            ':id' => $id,
        ]);
    }
}
