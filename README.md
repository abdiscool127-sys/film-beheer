# Filmbeheer App

Deze webapplicatie is een PHP-project met een MySQL-database en een externe API-koppeling. De applicatie is opgebouwd met OOP, gescheiden logica en een eenvoudige CRUD-interface.

## Wat de app doet
- toont films uit een database
- voegt nieuwe films toe
- bewerkt bestaande films
- verwijdert films
- zoekt op titel
- filtert op genre
- haalt externe filmdata op via een API
- importeert API-data naar de database

## Opdrachtcontext
Deze app is gemaakt voor de module "Website met beheer externe data". De belangrijkste onderdelen zijn:
- gebruik van een bestaande relationele database met meerdere tabellen en relaties
- volledige CRUD-functionaliteit in PHP
- structurering met OOP (Repository, Service, ApiService)
- koppeling met een externe API voor filminformatie
- integratie van API-gegevens met de lokale database

## Installatie
1. Plaats de map in `c:/xampp/htdocs/film_app`.
2. Start Apache en MySQL in XAMPP.
3. Importeer het database-schema uit `sql/schema.sql` in phpMyAdmin of via de MySQL CLI.
4. Pas `config.php` aan:
   - `db_host`
   - `db_name`
   - `db_user`
   - `db_pass`
   - `api_key` (OMDB API-key)
5. Open de applicatie in je browser: `http://localhost/film_app/index.php`.

## Bestandsstructuur
- `index.php` - router/entrypoint voor de pagina's
- `config.php` - database- en API-instellingen
- `pages/` - front-endpagina's met HTML en formulierverwerking
- `scripts/` - PHP-classes en logica
  - `Database.php` - PDO-verbinding met MySQL
  - `FilmRepository.php` - databasequeries en JOINs
  - `FilmService.php` - validatie en businesslogica
  - `ApiService.php` - externe API-oproepen
- `stylesheet/style.css` - thema en styling
- `sql/schema.sql` - database-schema

## Externe API
De app gebruikt de OMDB API via `scripts/ApiService.php`.
- Zet je OMDB API-key in `config.php` bij `api_key`.
- Als er geen API-key staat, werkt de API-demo niet.
- De applicatie gebruikt velden zoals `Title`, `Year`, `Genre`, `Director`, `Actors`, `Plot`, `Poster` en `imdbRating`.

## Belangrijk voor deze aflevering
- de applicatie draait op PHP + MySQL
- de database heeft meerdere tabellen (`films`, `genres`) met een relatie
- CRUD werkt via het Service- en Repository-model
- API-data kan worden opgehaald en naar de database worden geïmporteerd
- de code is geschreven in overzichtelijke onderdelen zonder onnodige AI-verwijzingen

## Tip
Gebruik een gratis OMDB API-key om de externe filmdata zichtbaar te maken. Zonder key blijven API-functies beperkt tot mock-data.
