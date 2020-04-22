window.addEventListener('load', mainNotification);

const NOTIFICATIONTIME = 1000;

/**
 * lire le data de service simulateModule, une simulation de data envoye par le module.
 * s'il y a pas un erreur, on passe les resultats a displayNotifications,
 * sinon a errorNotifications.
 * 
 */
function getNotifications(){
    let url = 'services/simulateModulService.php';
    fetchFromJson(url)
    .then(processNotifications)
    .then(displayNotifications, errorNotifications);  
}

/**
 * verfie la reponse de service,
 * si c'est un erreur alors renvoie un erreur avec le message d'erreur.
 * sinon envoie le result de message.
 * @param {*} answer 
 */
function processNotifications(answer){
    if(answer.status == "ok"){
        return answer.result;
      }
      throw new Error(answer.message);
}

/**
 * Montrer une notification si le type de message dans resultat est warning.
 * 
 * @param {*} result resultat de reponse de service
 */
function displayNotifications(result){
    var warningPart = document.getElementById("warningNotification");
    if(result.type == "ok"){
        return;
    }
    // si le type est warning alors on met une notifications
    else{
        warningPart.style.display = "block";
        warningPart.href = "showDevices.php?warning";
        warningPart.innerText = "Warning : " + result.id;
    }

}

/**
 * En cas d'erreur on ecrit l'erreur.
 * @param {*} msg 
 */
function errorNotifications(msg){
    var warningPart = document.getElementById("warningNotification");
    warningPart.style.display = "block";
    warningPart.innerhtml = "<span >ERROR"+ msg + "</span>";
}

/**
 * appele la fonction getNotification chaque NOTIFICATIONTIME milliseconde
 */
function mainNotification(){
    var interval = setInterval(getNotifications, NOTIFICATIONTIME);
}