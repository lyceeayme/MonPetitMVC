<?php

declare (strict_types=1);

namespace App\Controller;
use App\Model\GestionClientModel;
use App\Exceptions\AppException;
use \Tools\MyTwig;
use ReflectionClass;

/**
 * Description of GestionClientController
 *
 * @author ayme.pignon
 */
class GestionClientController {
    
    public function chercheUn(array $params){
        //appel de la méthode find(id) de la classe model
        $modele = new GestionClientModel();
        $id = filter_var(intval($params["id"]), FILTER_VALIDATE_INT);
        $unClient = $modele->find($id);
        if($unClient){
            $r = new ReflectionClass($this);
            $vue = str_replace('Controller','View', $r->getShortName()) ."/unClient.html.twig";
            MyTwig::afficheVue($vue, array('unClient' => $unClient));
        } else{
            throw new AppException("Client ". $id . " inconnu");
        }
    }
    
    public function chercheTous(){
        $modele = new GestionClientModel();
        $clients = $modele->findAll();
        if($clients){
            $r = new ReflectionClass($this);
            $vue = str_replace('Controller','View', $r->getShortName()) ."/tousClients.html.twig";
            MyTwig::afficheVue($vue, array('clients' => $clients));
        }else{
            throw new AppException("Aucun client à afficher");
        }
    }
}
