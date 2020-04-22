/**
 * On va envoyer les datas des modules par javascript.
 * cette script est capable d'envoyer le data a la service 
 * addModuleService qui va enregistre les donnes, et envoye 
 * un message si c'est bien sauvegarder
 */

// global variable pour stocker les ids des modules.
var ids = [];
var sendData = true;

/**
 * fonction qui va envoyer les datas de form a addModuleService.php
 * pour le sauvegarder.
 * elle va appeler displayOk si c'est bien enregistrer,
 * sinon elle appele displayErrorModule
 * @param {*} ev le form des inputs
 */

function sendFormModule(ev){ // form event listener
  ev.preventDefault();
  let url = 'services/addModuleService.php?'+formDataToQueryString(new FormData(this));
  if(sendData){
    showConnecting();
    fetchFromJson(url)
    .then(processAnswer)
    .then(displayOk, displayErrorModule);
  }
}

// sending data part

/**
 * fonction pour afficher connecting en attendant l'envoie des donnes
 */
function showConnecting(){
  var el = document.getElementById("addDeviceResponse");
  el.classList.remove("ErrorAnswer");
  el.classList.remove("OkAnswer");
  el.innerHTML = "<p> Sending ... </p>";
}

/**
 * gerer la reponse obtenu de la service addModuleService.php
 * @param {*} answer la reponse
 */

function processAnswer(answer){
  if(answer.status == "ok"){
    return answer.result;
  }
  throw new Error(answer.message);
}

/**
 * afficher la message passe en parametre.
 * @param {*} msg 
 */
function displayOk(msg){
  var el = document.getElementById("addDeviceResponse");
  el.classList.remove("ErrorAnswer");
  el.classList.add("OkAnswer");
  el.innerHTML = "<p>" + msg + "</p>";
  getIds(); // update ids after adding the module
  clearInput(); // clean inputs
}

/**
 * afficher l'erreur passe en parametre
 * @param {*} error 
 */
function displayErrorModule(error){
  var el = document.getElementById("addDeviceResponse");
  el.classList.remove("OkAnswer");
  el.classList.add("ErrorAnswer");
  el.innerHTML = "<p>" + error.message + "</p>";
  getIds();
}

// getting ids part

/**
 * connect to the getIdsService.php that will send the 
 * list of ids.
 */
function getIds(){
  let url = 'services/getIdsService.php';
  fetchFromJson(url)
  .then(processAnswer)
  .then(updateIds, errorIds);
}

/**
 * copy the ids from the result list to ids.
 * @param {*} result 
 */
function updateIds(result){
  ids = [];
  for(var x = 0; x < result.length; x++){
    ids.push(result[x]["m_id"]);
  }
  document.getElementById("m_id").addEventListener("keydown", checkId);
  document.getElementById("m_id").addEventListener("keyup", checkId);
}

function errorIds(){
  ids = [];
}

/**
 * verifier si l'id existe deja dans le base de donne.
 * si c'est le cas, on affiche un erreur, et on arrete le submit.
 */

function checkId(){
  var el = document.getElementById("m_id");
  var part = document.getElementById("errorId");
  if(ids.includes(el.value)){
    part.style.display = "block";
    sendData = false;
  }
  else{
    part.style.display = "none";
    sendData = true;
  }
}


/**
 * fonction pour supprimer le case de reponse de server,
 * quand on click de nouveau sur un partie de form.
 */
function removeAnswer(){
  var el = document.getElementById("addDeviceResponse");
  el.classList.remove("ErrorAnswer");
  el.classList.remove("OkAnswer");
  el.innerHTML = "";
}


/**
 * cette fonction est pour supprimer tout l'input apres 
 * qu'on a enregistre les donnes.
 */
function clearInput(){
  var inps = document.querySelectorAll(".addFormInp input, textarea");
  var checkBoxs = document.getElementsByClassName("checkBox");
  var x;
  for(x = 0; x < inps.length; x++){
    inps[x].value = "";
  }
  for(x = 0; x < checkBoxs.length; x++){
    checkBoxs[x].checked = false;
  }
  
}


window.addEventListener('load', mainAddModule);

function mainAddModule(){
  getIds();
  document.forms[0].addEventListener('submit', sendFormModule);
  document.forms[0].addEventListener('keydown', removeAnswer);
  document.forms[0].addEventListener('click', removeAnswer);
}