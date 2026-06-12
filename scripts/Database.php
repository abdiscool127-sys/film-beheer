<?php
/**
 * Database-verbinding
 * 
 * Deze klasse zorgt voor de verbinding met MySQL via PDO (PHP Data Objects).
 * Dit is veiliger dan de oude mysql_* functies.
 */
class Database
{
    private $pdo; // PDO-object voor alle database-queries

    /**
     * Constructor: verbind met database
     * 
     * Laadt de config, bouwt de DSN (Data Source Name), en maakt verbinding.
     * Als het mislukt, geeft een foutmelding.
     */
    public function __construct()
    {
        // Laad instellingen uit config.php
        $config = require __DIR__ . '/../config.php';
        
        // Bouw de DSN: http://php.net/manual/en/pdo.mysql.dsn.php
        $dsn = "mysql:host={$config->db_host};dbname={$config->db_name};charset=utf8mb4";
        
        try {
            // Maak PDO-verbinding
            $this->pdo = new PDO($dsn, $config->db_user, $config->db_pass);
            
            // Zet fouten op exceptions (automatisch gooien bij SQL-fout)
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Als verbinding mislukt, stop en toon fout
            die('Databaseverbinding mislukt: ' . $e->getMessage());
        }
    }

    /**
     * Geef de PDO-verbinding terug
     * 
     * Dit wordt door FilmRepository gebruikt voor queries.
     */
    public function getConnection()
    {
        return $this->pdo;
    }
}
