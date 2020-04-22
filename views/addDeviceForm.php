<!--
    Cette page represente l'interface de formulaire pour inscrire un nouvelle module.
    Le formulaire contient plusieur input a propos de 
-->

<div class="addDeviceContent">
    <h1>Inscrire un nouveau module</h1>
    <p>
        Chaque module est identifie par un unique id.
    </p>
    <form action="" method="get" class="addForm">
        <div class="addFormInp">
            <input type="text" name="nom" maxlength="50" required /><label>Nom</label>
        </div>
        
        <div class="addFormInp">
            <input type="text" name="type" maxlength="50" required /><label>Type</label>
        </div>
        
        <div class="addFormInp">
            <input type="number" name="id" min="0" id="m_id" required/><label for="m_id">ID</label>
        </div>

        <div class="ErrorAnswer" id="errorId">
            Cet id existe deja !
        </div>

        <div class="addFormInp">
            <textarea name="description" rows="10" cols="30" value=""></textarea><label>Description</label>
        </div>

        <p>
            Cochez les informations que vous souhaitez d'afficher :
        </p>

        <ul id="addFormChoices">
            <li class="addFormBox">
                <label>
                    <input type="checkbox" class="checkBox" name="temperature"/>
                    Temperature
                </label>
            </li>

            <li class="addFormBox">
                <label>
                    <input type="checkbox" class="checkBox" name="fonction_time"/>
                    duree de fonctionnement
                </label>
            </li>
            
            <li class="addFormBox">
                <label>
                    <input type="checkbox" class="checkBox" name="send_data"/>
                    Données envoyees
                </label>
            </li>
            
            <li class="addFormBox">
                <label>
                    <input type="checkbox" class="checkBox" name="receive_data"/>
                    Données reçue
                </label>
            </li>
            
            <li class="addFormBox">
                    <input type="checkbox" class="checkBox" name="working_cond"/>
                    Etat de marche
            </li>
        </ul>

        <input type="submit" value="Sauvegarder" id="sendBtn"/>
    </form>

    <div id = "addDeviceResponse"></div>
</div>