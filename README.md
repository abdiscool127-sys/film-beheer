# Filmbeheer (project)

Korte handleiding en opleverpunten voor de module "Website met beheer externe data" (CREBO 25998).

## Wat is aanwezig
- CRUD voor `films` met genres (DB: `film_app`) — volledige flows in `pages/`.
- Externe API-integratie (OMDB) via `scripts/ApiService.php`.
- Nieuwe features: aparte entiteiten `actors` en `directors` met eigen databases:
  - SQL: `sql/actors_schema.sql`, `sql/directors_schema.sql`
  - Repositories: `scripts/ActorRepository.php`, `scripts/DirectorRepository.php`
  - Services: `scripts/ActorService.php`, `scripts/DirectorService.php`
  - API endpoints: `api/actors.php`, `api/directors.php`
  - Pagina's: `pages/actors.php`, `pages/directors.php` (create/read/update/delete)

## Installatie / SQL import
Importeer de databases via commandline of phpMyAdmin:

```bash
mysql -u root -p < sql/schema.sql
mysql -u root -p < sql/actors_schema.sql
mysql -u root -p < sql/directors_schema.sql
```

Controleer `config.php` voor database-credentials en `base_url`.

## Routes / pagina's
- Films overzicht: `/film_app/index.php?page=list`
- Actors: `/film_app/index.php?page=actors`
- Directors: `/film_app/index.php?page=directors`

## Presentatiepunten (10 minuten)
1. Overzicht architectuur: `scripts/Database.php` (PDO), Repositories + Services.
2. Databaseontwerp: `films` ↔ `genres` (FK), plus aparte DB's voor `actors` en `directors`.
3. Demo: CRUD op films, demo import via OMDB, CRUD op actors/directors.
4. Veiligheid: Prepared statements voorkomen SQL-injectie; input validatie op server-side.
5. Reflectie: keuzes voor OOP-structuur en waarom services/repositories scheiden.

## Security & privacy (kort)
- Gebruik altijd prepared statements voor DB-queries (gedaan).
- Valideer en sanitize gebruikersinput (basis-checks aanwezig; verbeter waar nodig).
- Externe data: behandel als onbetrouwbaar — controleer velden voor gebruik en bewaar geen gevoelige data.
- Privacy: verwijder of pseudonimiseer persoonsgegevens als je echte data gebruikt (AVG).

## Toekomst / uitbreidingen
- Verbeter validatie en foutmeldingen in UI.
- Voeg paginering en autorisatie toe.
- Zet configuratie (DB-credentials, API-keys) in environment-variabelen.

## Feedback of extra taken
Wil je dat ik:
- commit messages en geschiedenis opschoon? (Ik geef hiervoor advies; ik zal geen acties uitvoeren die misleiding bevorderen.)
- extra tests of een kleine deployscript toevoeg? 
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
