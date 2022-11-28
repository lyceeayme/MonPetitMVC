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
abstract class Repository {

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

    public static function getRepository(string $entity): Repository {
        $repositoryName = str_replace('Entity', 'Repository', $entity) . 'Repository';
        $repository = new $repositoryName($entity);
        return $repository;
    }

    public function findAll(): array {
        $sql = "select * from " . $this->table;
        $lignes = $this->connexion->query($sql);
        $lignes->setFetchMode(PDO::FETCH_CLASS, $this->classeNameLong, null);
        return $lignes->fetchAll();
    }

    public function findIds(): array {
        $sql = "select * from " . $this->table;
        $lignes = $this->connexion->query($sql);
        return $lignes->fetchAll();
    }

    public function find(int $id): ?object {
        $sql = "select * from " . $this->table . " where id=:id";
        $lignes = $this->connexion->prepare($sql);
        $lignes->bindParam(':id', $id, PDO::PARAM_INT);
        $lignes->execute();
        $lignes->setFetchMode(PDO::FETCH_CLASS, $this->classeNameLong, null);
        return $lignes->fetch();
    }

    public function insert(object $objet): void {
        // conversion objet -> tableau
        $attributs = (array) $objet;
        array_shift($attributs);
        $colonnes = "(";
        $colonnesParams = "(";
        $parametres = array();
        foreach ($attributs as $cle => $valeur) {
            $cle = str_replace("\0", "", $cle);
            $c = str_replace($this->classeNameLong, "", $cle);
            $p = ":" . $c;
            if ($c != "id"){
                $colonnes .= $c . " ,";
                $colonnesParams .= " ? ,";
                $parametres[]= $valeur;
            }
        }
        $cols = substr($colonnes, 0, -1);
        $colsParams = substr($colonnesParams, 0, -1);
        $sql = " insert into " . $this->table . " " . $cols . ") values " . $colsParams . ")";
        $unObjetPDO = Connexion::getConnexion();
        $req = $unObjetPDO->query($sql);
    }
    
    public function countRows() :int {
        $sql = "select Count(*) from " . $this->table;
        $pdo = $this->connexion->query($sql);
        $nb = $pdo->fetch(PDO::FETCH_NUM)[0];
        return intval($nb);
    }
    
    public function execulteSQL (string $sql): ?array {
        $resultat = $this->connexion->query($sql);
        return $resultat->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function __call(string $methode, array $arguments) {
        if (preg_match("#^findBy#", $methode)){
            return $this->traiteFindBy($methode, array_values($arguments[0]));
        }
    }
    
    public function findBy(array $params){
        $element = "Choisir...";
        while (in_array($element, $params)){
            unset($params[array_search($element,$params)]);
        }
        $cles = array_keys($params);
        $methode ="findBy";
        for ($i =0; $i < count($cles); $i++){
            if ($i>0){
                $methode .= "_and_";
            }
            $methode .= $cles[$i];
        }
        return $this->traiteFindBy($methode, array_values($params));
    }
    
    private function traiteFindBy($methode, $params){
        $criteres = str_replace("findBy", "", $methode);
        $criteres = explode("_and_", $criteres);
        if(count($criteres) > 0){
            $sql = 'select * from ' . $this->table . " where ";
            $pasPremier =false;
            foreach($criteres as $critere){
                if ($pasPremier){
                    $sql .= ' and ';
                }
                $sql .= $critere . " = ? ";
                $pasPremier = true;
            }
            $lignes = $this->connexion->prepare($sql);
            $lignes->execute($params);
            $lignes->setFetchMode(PDO::FETCH_CLASS, $this->classeNameLong, null);
            return $lignes->fetchAll();
        }
    }
    
    public function findColumnDistinctValues(string $colonne) : array{
        $sql = "select distinct " . $colonne . " libelle from " . $this->table . " order by 1";
        $tab = $this->connexion->query($sql)->fetchAll(PDO::FETCH_COLUMN);
        return $tab;
    }
}
