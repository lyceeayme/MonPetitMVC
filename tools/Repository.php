<?php

declare(strict_types=1);
namespace Tools;

use Tools\Connexion;
use PDO;

/**
 * Description of Repository
 *
 * @author ayme.pignon
 */
abstract class  Repository {
    
    private $classeNameLong;
    private $classeNamespace;
    private $table;
    private $connexion;
    
    private function __construct(string $entity) {
        $tablo = explode("\\", $entity);
        $this->table = array_pop($tablo);
        $this->classeNamespace = implode("\\", $tablo);
        $this->classeNameLong = $entity;
        $this->connexion = Connexion::getConnexion();
    }
    
    public static function getRepository(string $entity) : Repository{
        $repositoryName = str_replace('Entity', 'Repository', $entity) . 'Repository';
        $repository = new $repositoryName($entity);
        return $repository;
    }
    
    public function findAll() : array{
        $sql = "select * from " . $this->table;
        $lignes = $this->connexion->query($sql);
        $lignes->setFetchMode(PDO::FETCH_CLASS, $this->classeNameLong, null);
        return $lignes->fetchAll();
    }
    
    public function findIds() :array{
        $sql = "select * from " . $this->table;
        $lignes = $this->connexion->query($sql);
        return $lignes->fetchAll();
    }
    
    public function find(int $id): ?object {
        $sql = "select * from " . $this->table . " where id=:id";
        $lignes = $this->connexion->prepare($sql);
        $lignes->bindParam(':id', $id, PDO::PARAM_INT);
        $lignes->execute();
        return $lignes->fetch($this->table::class );
    }
}
