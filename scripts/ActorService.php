<?php
require_once __DIR__ . '/ActorRepository.php';

/**
 * ActorService
 *
 * Deze laag bevat eenvoudige business-logic voor actors. De service
 * roept repository-methodes aan en kan later validatie/regels toevoegen
 * (bv. controleren op unieke namen).
 */
class ActorService
{
    private $repo;

    public function __construct()
    {
        $this->repo = new ActorRepository();
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
