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
    
    public function findIds(){
        try{
            $unObjetPdo = Connexion::getConnexion();
            $sql = $unObjetPdo->query("select id from client");
            if ($sql->rowCount() > 0){
                return $sql->fetchAll(PDO::FETCH_ASSOC);
            } else{
                throw new AppException("Aucun client trouvé");
            }        
        } catch (PDOException) {
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

    public function enregistreClient(Client $client){
        try{
            $unObjetPdo = Connexion::getConnexion();
            $script = "insert into client (titreCli, nomCli, prenomCli, adresseRue1Cli, adresseRue2Cli, cpCli, villeCli, telCli)"
                    . " values (:titreCli, :nomCli, :prenomCli, :adresseRue1Cli, :adresseRue2Cli, :cpCli, :villeCli, :telCli)";
            $sql = $unObjetPdo->prepare($script);
            $sql->bindValue(':titreCli', $client->getTitreCli(), PDO::PARAM_STR);
            $sql->bindValue(':nomCli', $client->getNomCli(), PDO::PARAM_STR);
            $sql->bindValue(':prenomCli', $client->getPrenomCli(), PDO::PARAM_STR);
            $sql->bindValue(':adresseRue1Cli', $client->getAdresseRue1Cli(), PDO::PARAM_STR);
            $sql->bindValue(':adresseRue2Cli', ($client->getAdresseRue2Cli() == "")?(null) : ($client->getAdresseRue2Cli()), PDO::PARAM_STR);
            $sql->bindValue(':cpCli', $client->getCpCli(), PDO::PARAM_STR);
            $sql->bindValue(':villeCli', $client->getVilleCli(), PDO::PARAM_STR);
            $sql->bindValue(':telCli', $client->getTelCli(), PDO::PARAM_STR);
            $sql->execute();
        } catch (PDOException) {
            throw new AppException("Erreur technique inattendue");
        }
    }
}
