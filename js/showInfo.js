window.addEventListener("load", mainShowModule);
// constant

// time to check for updates
const UPDATETIME = 1000;



// variables globales
var intervalUpdate;

// true si on click sur sprimer pour la premier fois.
var first_time_clicked = true;

// les id des donne simple.
var simpleInfoKeys = ["m_id", "m_nom", "m_type", "m_descr"]; 

// les id des partie que l'utilisateur a choisi d'afficher ou non
var choicesShowKey = ["temp_show", "fonc_time_show", "send_data_show", "recv_data_show",
"work_cond_show"]; 

// les id des partie pour les data des choix
var choicesDataKey = ["temp_data", "fonc_time_data", "send_data", "recv_data",
"work_cond_data"];


/**
 * Envoye le request a service getModulteDatasServices, 
 * pour recuperer les informations de module d'un id egale 
 * a id passe en parametre.
 * 
 * @param {*} id l'id de module
 */
function getModuleData(id){
    let url = 'services/getModuleDatasService.php?id=' + id;
    fetchFromJson(url)
    .then(processModuleDatas)
    .then(displayModuleDatas, errorModuleData);  
}


/**
 * verifie s'il y pas des erreur dans la reponse de service.
 * s'il y a alors on declenche un erreur, 
 * sinon on renvoie le resultat de message
 * @param {*} answer 
 */
function processModuleDatas(answer){
    document.getElementById("waitingGetData").innerHTML = ""; // effacer le wainting data .. apres avoir connecte
    if(answer.status == "ok"){
        return answer.result;
    }
    throw new Error(answer.message);
}


/**
 * fonction qui va traiter les informations s'il y a pas des erreurs.
 * Elle va afficher ces informations.
 * 
 * @param {*} result le result de request de service getModuleDataService
 */
function displayModuleDatas(result){
    var errorPart = document.getElementById("errorGetData");
    errorPart.innerText = ""; // effacer le case d'erreur
    errorPart.style.display = "none"; // eliminer le partie d'erreur
    
    document.getElementById("showDeviceContent").style.visibility = "visible"; // show the showDevicesInfo part    
    var basicData = result.basicData[0];
    document.getElementById("exportModuleButton").href = "exportModules.php?id=" + basicData["m_id"]; // le lien pour export les datas d'un module

    displayModuleBasicData(basicData)
    var okMsgs = result.okMsg;
    displayModuleOkMsg(okMsgs);
    var warningMsgs = result.warningMsg;
    displayModuleWarningMsg(warningMsgs);
}


/**
 * fonction qui va etre appeler en cas d'erreur dans la reponse de service getModuleDataService
 * Et afficher l'erreur dans la partie erreur.
 * @param {*} error message de l'erreur
 */
function errorModuleData(error){
    document.getElementById("showDeviceContent").style.visibility = "visible"; // show the showDevicesInfo part    
    var errorPart = document.getElementById("errorGetData");
    errorPart.style.display = "block";
    errorPart.innerText = error;
}


/**
 * afficher les datas de module
 * @param {*} basicData data de module
 */
function displayModuleBasicData(basicData){
    var x;
    for(x = 0; x < simpleInfoKeys.length; x++){
        document.getElementById(simpleInfoKeys[x]).innerHTML = basicData[simpleInfoKeys[x]];
    }

    // boucle pour afficher les informations qui sont choisi par l'utilisateur
    for(x = 0; x < choicesShowKey.length; x++){
        if(basicData[choicesShowKey[x]] == "1"){ // verfie si l'utilisateur a selectionne cet information
            document.getElementById(choicesShowKey[x]).style.display = "flex";
            document.getElementById(choicesDataKey[x]).innerHTML = basicData[choicesDataKey[x]];
        }
        else{
            document.getElementById(choicesShowKey[x]).style.display = "none";
        }
    }
}

/**
 * afficher les messages envoye par le module de type ok,
 * et qui sont pas encore deja vu par l'utilisateur.
 * @param {*} okMsgs liste des messages de type ok
 */
function displayModuleOkMsg(okMsgs){
    var okBox = document.getElementById("okLog");
    var msgBox;
    for(var x = 0; x < okMsgs.length; x++){
        msgBox = document.createElement("div"); // creation d'un noeud div
        msgBox.classList.add("msgBox");
        msgBox.innerHTML = okMsgs[x]["msg_data"]+"  "+okMsgs[x]["ins_date"];

        okBox.insertBefore(msgBox, okBox.firstChild); // ajoute le noeud en premiere position
    }
}


/**
 * afficher les messages envoye par le module de type warning.
 * et qui sont pas encore deja vu par l'utilisateur.
 * @param {*} warningMsgs list des message de type warning
 */
function displayModuleWarningMsg(warningMsgs){
    var warningBox = document.getElementById("warningLog");
    var msgBox;
    for(var x = 0; x < warningMsgs.length; x++){
        msgBox = document.createElement("div"); // creation d'un noeud div
        msgBox.classList.add("warningBox");
        msgBox.classList.add("msgBox");
        msgBox.innerHTML = warningMsgs[x]["msg_data"]+"  "+warningMsgs[x]["ins_date"];

        warningBox.insertBefore(msgBox, warningBox.firstChild); // ajoute le noeud en premiere position
    }    
}


/**
 * fonction a executer quand l'utilisateur click sur un module.
 * Elle appele les autres fonctions pour afficher les datas de module selectionne.
 * et elle le repete chaque UPDATETIME millisecond
 * @param {*} el 
 */
function selectModuleData(el){
    el = el.target;
    // le click sur box peut ce passer sur leur node enfant, alors ici on associe el a le module box qui est clicke
    if(el.className == "moduleNom" || el.className == "moduleType"){
        el = el.parentNode;
    }
    if(el.className == "rightPart" || el.className == "moduleId"){
        el = el.parentNode;
    }

    clearInterval(intervalUpdate); // unset le dernier interval ajouter
    var id = el.getElementsByClassName("moduleId")[0].innerText; // recupere l'id de module
    
    // si le box est un warning, alors on v'a l'eliminer de la liste des modules quand on le click
    if(el.classList.contains("warningBox")){
        document.getElementById("showDevicesList").removeChild(el);
    }

    // effacer le contenu de tableau de warning messages et ok message avant l'ecriture
    document.getElementById("warningLog").innerHTML = "";
    document.getElementById("okLog").innerHTML = "";
    
    document.getElementById("waitingGetData").innerHTML = "Getting data ..."; // ecrire qu'on attend les data de server.
    // obtenir les donnes de la module et afficher le
    getModuleData(id);

    // appele la fonction getModuleData chaque 10 secondes
    intervalUpdate = setInterval(getModuleData, UPDATETIME, id);
}

/*
-----------------------------------
    part pour suprimer un module
-----------------------------------
*/
/**
 * envoyer l'id a service deleteModuleService.php pour 
 * supprimer le module selecter.
 * 
 */
function deleteModule(){
    if(first_time_clicked){
        first_time_clicked = false;
        setTimeout(resetFirstTime, 10000); // reset apres 10s s'il le click pas de nouveau
        document.getElementById("warningDelete").innerHTML = "Warning: le module va etre supprimer completement, pour la confirmation click de nouveau !";
        return;

    }
    id = document.getElementById("m_id").innerHTML;
    let url = 'services/deleteModuleService.php?id=' + id;
    fetchFromJson(url)
    .then(processModuleDatas)
    .then(refreshPage, errorModuleData);  
}

/**
 * reload la page si le module est bien supprimer
 */
function refreshPage(){
    location.reload(false);
}

/**
 * Annlez la demande pour supprimer le moduel.
 * le first_time_clicked est true de nouveau
 */
function resetFirstTime(){
    first_time_clicked = true;
    document.getElementById("warningDelete").innerHTML = "";
}

function mainShowModule(){
    var modules = document.getElementsByClassName("moduleBox"); // liste des modules
    var delete_btn = document.getElementById("deleteModule"); // le boutton pour supprimer un module
    
    for(var x = 0; x < modules.length; x++){
        modules[x].addEventListener('click', selectModuleData);
    }

    delete_btn.addEventListener('click', deleteModule);
    
}