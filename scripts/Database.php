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

        /*
         * SUPPORT VOOR MEERDERE DATABASES
         * De constructor accepteert optioneel één argument: de naam van de database
         * die gebruikt moet worden. Dit maakt het mogelijk om met meerdere
         * databases in dezelfde app te werken (bijv. aparte DB voor actors).
         * Gebruik: new Database('film_actors')
         */
        $args = func_get_args();
        $dbName = count($args) > 0 && !empty($args[0]) ? $args[0] : $config->db_name;

        // Bouw de DSN volgens PDO-MySQL format
        $dsn = "mysql:host={$config->db_host};dbname={$dbName};charset=utf8mb4";
        
        try {
            // Maak PDO-verbinding
            $this->pdo = new PDO($dsn, $config->db_user, $config->db_pass);

            // Zorg dat PDO fouten als Exceptions gooit. Dit voorkomt dat
            // onopgemerkte warnings blijven rondzwerven en maakt errorhandling
            // in hogere lagen (service/repository) eenvoudiger.
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Optioneel: hier kun je nog attributen zetten zoals
            // PDO::ATTR_DEFAULT_FETCH_MODE of connection pooling opties
            // afhankelijk van je omgeving.
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
