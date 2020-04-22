<div class ="showDevicesContent">
    <div id="showDevicesList">
        <?php
            /**
             * Dans cette partie on va afficher les listes des modules.
             * 
             */
            require_once('lib/fonctionsHTML.php');
            if(isset($_GET["warning"])){
                echo modulesTohtml($modules_data, true);
            }
            else{
                echo modulesTohtml($modules_data, false);
            }
        ?>


    </div>

    <div id="showDevicesInfo">
            <div id = "showDeviceContent">
                <div id = "waitingGetData"></div>
                <div id = "errorGetData"></div>
                <div class = "basicModuleinfos">
                    <div class="showSimpleData">
                        <p>Id : </p>
                        <div id = "m_id"></div>
                    </div>
                    <div class="showSimpleData">
                        <p>Nom : </p>
                        <div id = "m_nom"></div>
                    </div>
                    <div class="showSimpleData">
                        <p>Type : </p>
                        <div id = "m_type"></div>
                    </div>
                    <div class="showSimpleData">
                        <p>Description : </p>
                        <div id = "m_descr"></div>
                    </div>
                </div>

                <div class = "basicModuleinfos">
                    <div class="showSimpleData" id = "temp_show">
                        <p>Temperature : </p>
                        <div id = "temp_data"></div>
                    </div>
                    <div class="showSimpleData" id = "fonc_time_show">
                        <p>Duree de fonctionnement : </p>
                        <div id = "fonc_time_data"></div> s
                    </div>
                    <div class="showSimpleData" id = "send_data_show">
                        <p>Donne envoyes : </p>
                        <div id = "send_data"></div> B
                    </div>
                    <div class="showSimpleData" id = "recv_data_show">
                        <p>Donne Recus : </p>
                        <div id = "recv_data"></div> B
                    </div>
                    <div class="showSimpleData" id = "work_cond_show">
                        <p>Etat de marche : </p>
                        <div id = "work_cond_data"></div>
                    </div>     
                </div>

                <div id = "moduleLogInfo">
                    <div id = "warningLog" class = "msgLogBox" ></div>
                    <div id = "okLog" class = "msgLogBox" ></div>
                </div>

                <p id="warningDelete"></p>
                
                <div id = "moduleActionButtons">
                    <button id="deleteModule">Supprimer</button>
                    <a id="exportModuleButton" target="_none">Exporter</a>
                </div>

                
                
            </div>
    </div>

</div>