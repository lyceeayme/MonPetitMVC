<?php

declare(strict_types=1);

namespace App\Model;

use PDO;
use App\Entity\Client;
use Tools\Connexion;
use Exception;
use App\Exceptions\AppException;

/**
 * Description of GestionClientModel
 *
 * @author ayme.pignon
 */
class GestionClientModel {

    public function find(int $id): Client {
        try {
            $unObjetPdo = Connexion::getConnexion();
            $sql = "Select * from CLIENT where id=:id";
            $ligne = $unObjetPdo->prepare($sql);
            $ligne->bindValue(":id", $id, PDO::PARAM_INT);
            $ligne->execute();
            return $ligne->fetchObject(Client::class);
        } catch (Exception) {
            throw new AppException("Erreur technique inattendue");
        }
    }

    public function findAll() {
        try {
            $connexion = Connexion::getConnexion();
            $sql = $connexion->prepare("Select * from CLIENT");
            $sql->execute();
            return $sql->fetchAll(PDO::FETCH_CLASS, Client::class);
        } catch (Exception) {
            throw new AppException("Erreur technique inattendue");
        }
    }

}
