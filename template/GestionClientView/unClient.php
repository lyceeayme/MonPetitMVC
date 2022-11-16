<?php

include_once PATH_VIEW . "header.html";
// Affiche les infos d'une variable
if (MODE_DEV) {
    echo "<br> Message :";
    var_dump($unClient,);
}
echo"<br>Nom du client : " . $unClient->getNomCli();
include_once PATH_VIEW . "footer.html";
