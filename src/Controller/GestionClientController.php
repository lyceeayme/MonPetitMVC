<?php

declare (strict_types=1);

namespace App\Controller;

use App\Exceptions\AppException;
use App\Entity\Client;
use Tools\MyTwig;
use Tools\Repository;
use ReflectionClass;

/**
 * Description of GestionClientController
 *
 * @author ayme.pignon
 */
class GestionClientController {

    public function chercheUn(array $params) {
        //appel de la méthode find(id) de la classe model
        $modele = Repository::getRepository("App\Entity\Client");
        //Tableau de chaque ligne sous forme de tableau pour recup des valeurs
        $ids = $modele->findIds();
        // On place les ids trouvés dans un tableau qu'on envoie à la vue
        $params['lesId'] = $ids;
        //On cherche si l'id du client est dans l'URL càd on gère le retour du formulaire
        if (array_key_exists('id', $params)) {
            // le formulaire revient
            $id = filter_var(intval($params["id"]), FILTER_VALIDATE_INT);
            $unClient = $modele->find($id);
            if ($unClient) {
                $params['unClient'] = $unClient;
            } else {
                $params['message'] = "Client " . $id . " inconnu";
            }
        }
        $r = new ReflectionClass($this);
        $vue = str_replace('Controller', 'View', $r->getShortName()) . "/unClient.html.twig";
        MyTwig::afficheVue($vue, $params);
    }

    public function chercheTous() {
        $repository = Repository::getRepository("App\Entity\Client");
        $clients = $repository->findAll();
        if ($clients) {
            $r = new ReflectionClass($this);
            $vue = str_replace('Controller', 'View', $r->getShortName()) . "/tousClients.html.twig";
            MyTwig::afficheVue($vue, array('clients' => $clients));
        } else {
            throw new AppException("Aucun client à afficher");
        }
    }

    public function creerClient(array $params) {
        if (empty($params)){
            $vue = "GestionClientView\\creerClient.html.twig";
            MyTwig::afficheVue($vue, array());
        } else {
            try {
                $params = $this->VerificationSaisieClient($params);
                // Création de l'objet client
                $client = new Client($params);
                $repository = Repository::getRepository("App\Entity\Client");
                $repository->insert($client);
                $this->chercheTous();
            } catch (Exception $ex) {
                throw new AppException("Erreur à l'enregistrement d'un nouveau client");
            }
        }
    }
    // a implementer
    private function VerificationSaisieClient(array $params){
        
    }
    
    /**
     * Methode retournant le nombre de client présent dans la table Client
     * @param array $params
     * @return void
     */
    public function nbClients (array $params) : void{
        $repo = Repository::getRepository("App\Entity\Client");
        $nbClients =$repo->countRows();
        echo "Nombre de clients : " . $nbClients;
    }
    
    // a implementer
    public function statistiquesTousClients(): array{
        
    }

}
