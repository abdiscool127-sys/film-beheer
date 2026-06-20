<?php
require_once __DIR__ . '/Database.php';

class DirectorRepository
{
    private $db;

    public function __construct()
    {
        // Gebruik een aparte database voor directors. Dit maakt het overzichtelijk
        // en scheidt concerns als actors/directors veel en ander data bevatten.
        $database = new Database('film_directors');
        $this->db = $database->getConnection();
    }

    public function getAll($search = null)
    {
        if ($search) {
            // Zoek op naam met LIKE en prepared statement om injection te voorkomen
            $stmt = $this->db->prepare('SELECT * FROM directors WHERE naam LIKE :s ORDER BY id DESC');
            $stmt->execute([':s' => "%$search%"]);
        } else {
            $stmt = $this->db->query('SELECT * FROM directors ORDER BY id DESC');
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        // Insert met expliciete veldbinding
        $stmt = $this->db->prepare('INSERT INTO directors (naam, geboortedatum, bio) VALUES (:naam, :geboortedatum, :bio)');
        return $stmt->execute([
            ':naam' => $data['naam'] ?? null,
            ':geboortedatum' => $data['geboortedatum'] ?? null,
            ':bio' => $data['bio'] ?? null,
        ]);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM directors WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare('DELETE FROM directors WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function update($id, $data)
    {
        // Update met binding; geschikt voor eenvoudige onderhoudsdata.
        $stmt = $this->db->prepare('UPDATE directors SET naam = :naam, geboortedatum = :geboortedatum, bio = :bio WHERE id = :id');
        return $stmt->execute([
            ':naam' => $data['naam'] ?? null,
            ':geboortedatum' => $data['geboortedatum'] ?? null,
            ':bio' => $data['bio'] ?? null,
            ':id' => $id,
        ]);
    }
}
