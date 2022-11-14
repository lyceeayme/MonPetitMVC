-- Script pour ajouter l'utilisateur nom/nom avec les droits
-- pour utiliser la base de donnée cliconmvc

use clicommvc;

Create user if not exists pignonmvc@localhost identified by "sio";

grant select, update, delete, insert
on clicommvc.*
to pignonmvc@localhost;