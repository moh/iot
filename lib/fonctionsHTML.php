<?php

//creat the list of modules
function modulesTohtml($modules, $warning){
    // si c'est un warning ajoute le class warning a liste 
    if(empty($modules)){
        return "<p><strong>Pas des modules</strong></p>";
    }
    if($warning){
        $classes = "moduleBox warningBox";
    }
    else{
        $classes = "moduleBox";
    }
    $html = "";
    foreach($modules as $module){
        $html .= "<div class='$classes'><div class='moduleId'>$module[m_id]</div>
        <div class='rightPart'>
        <div class='moduleNom'>$module[m_nom]</div><div class='moduleType'>$module[m_type]</div>
        </div></div>\n";
    }
    return $html;
}

?>