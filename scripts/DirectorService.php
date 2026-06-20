<?php
require_once __DIR__ . '/DirectorRepository.php';

/**
 * DirectorService
 *
 * Service-laag voor regisseurs. Houdt business-logic gescheiden van
 * repository/database-code en is de plaats om validatie en transformaties
 * toe te voegen voordat data naar de DB gaat.
 */
class DirectorService
{
    private $repo;

    public function __construct()
    {
        $this->repo = new DirectorRepository();
    }

    public function list($search = null)
    {
        return $this->repo->getAll($search);
    }

    public function create($data)
    {
        return $this->repo->create($data);
    }

    public function get($id)
    {
        return $this->repo->getById($id);
    }

    public function update($id, $data)
    {
        return $this->repo->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repo->delete($id);
    }
}
