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
        if (empty($params)) {
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

    // a implementer page 12
    private function VerificationSaisieClient(array $params) {
        
    }

    /**
     * Methode retournant le nombre de client présent dans la table Client
     * @param array $params
     * @return void
     */
    public function nbClients(array $params): void {
        $repo = Repository::getRepository("App\Entity\Client");
        $nbClients = $repo->countRows();
        echo "Nombre de clients : " . $nbClients;
    }

    // a implementer Exercice 2 page 13
    public function statistiquesTousClients(): array {
        
    }

    public function testFindBy(array $params): void {
        $repository = Repository::getRepository("App\Entity\Client");
        $parameters = array("titreCli" => "Monsieur", "villeClie" => "Toulon");
        $clients = $repository->findBytitreCli_and_villeCli($parameters);
        $r = new ReflectionClass($this);
        $vue = str_replace('Controller', 'View', $r->getShortName()) . "/tousClients.html.twig";
        MyTwig::afficheVue($vue, array('clients' => $clients));
    }

    /**
     * Affiche les clients filtrer selon 3 criteres max
     * @param array $params
     * @return void
     */
    public function rechercheClients(array $params): void {
        $repository = Repository::getRepository("App\Entity\Client");
        $titres = $repository->findColumnDistinctValues("titreCli");
        $cps = $repository->findColumnDistinctValues("cpCli");
        $villes = $repository->findColumnDistinctValues("villeCli");

        $paramsVue['titres'] = $titres;
        $paramsVue['cps'] = $cps;
        $paramsVue['villes'] = $villes;
        // gestion du retour du formulaire
        $criteresPrepares = $this->verifieEtPrepareCriteres($params);

        if (count($criteresPrepares) > 0) {
            $clients = $repository->findBy($params);
            $paramsVue['clients'] = $clients;
            foreach ($criteresPrepares as $valeur) {
                ($valeur != "Choisir...") ? ($criteres[] = $valeur) : (null);
            }
            $paramsVue['criteres'] = $criteres;
        }
        $vue = "GestionClientView\\filtreClients.html.twig";
        MyTwig::afficheVue($vue, $paramsVue);
    }

    /**
     * Methode privé appelé dans la méthode rechercheClients, qui vérifie les valeurs dans le tableau
     * @param array $params
     * @return array
     */
    private function verifieEtPrepareCriteres(array $params): array {
        $args = array(
            'titreCli' => array(
                'filter' => FILTER_VALIDATE_REGEXP | FILTER_SANITIZE_SPECIAL_CHARS,
                'flags' => FILTER_NULL_ON_FAILURE,
                'options' => array('regexp' => '/^(Monsieur|Madame|Mademoiselle)$/')
            ),
            'cpCli' => array(
                'filter' => FILTER_VALIDATE_REGEXP | FILTER_SANITIZE_SPECIAL_CHARS,
                'flags' => FILTER_NULL_ON_FAILURE,
                'options' => array('regexp' => "/[0-9]{5}/")
            ),
            'villeCli' => FILTER_SANITIZE_SPECIAL_CHARS);
        $retour = filter_var_array($params, $args, false);
        if (isset($retour['titreCli']) || isset($retour['cpCli']) || isset($retour['villeCli'])) {
            $element = "Choisir...";
            while (in_array($element, $retour)) {
                unset($retour[array_search($element, $retour)]);
            }
        }
        return $retour;
    }

    ///                            ///
    //          PARTIE AJAX         //
    //          PARTIE AJAX         //
    //          PARTIE AJAX         //
    ///                            ///
}
