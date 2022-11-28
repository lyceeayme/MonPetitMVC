function creationXHR(){
    var resultat=null;
    try{ //test pour les navigateurs : Mozilla, Opéra, ...
        resultat = new XMLHttpRequest();
    }
    catch(Erreur){
        try{
            //Test pour les navigateurs Internet explorer > 5.0
            resultat = new ActiveXObject("Msxm12.XMLHTTP");
        }
        catch(Erreur){
            try{
                //test pour le navigateur Internet explorer 5.0
                resultat = new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch(Erreur){
                resultat=null;
            }
        }
    }
    return resultat;
}