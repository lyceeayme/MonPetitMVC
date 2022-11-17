<?php

include_once PATH_VIEW . "header.html";

if (MODE_DEV) {
    echo "<br> Message :";
    var_dump($clients,);
}

Foreach($clients as $client){
    echo "<br>" . $client->getId() . " " . $client->getTitreCli() . " " . $client->getNomCli() . " " . $client->getPrenomCli();
}

include_once PATH_VIEW . "footer.html";