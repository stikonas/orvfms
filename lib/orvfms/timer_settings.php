<?php

function timerSettings(&$s20Table,$mac,$actionValue){
    //
    // Implements  the actions selected in the web timer page.
    //
    $actionType = $_POST['actionType'];

    if(($actionValue == "clearSwitchOff")  ||
       ($actionValue == "clearCountdown")){
        $h = $m = $s = 0;
        $act = $s20Table[$mac]['st'];
    }
    else{
        $action     = $_POST['action'];
        $h          = $_POST['hours'];
        $m          = $_POST['minutes'];
        $s          = $_POST['seconds'];
        $act = ($action == "on" ? 1 : 0);
    }

    $sec = $h * 3600 + $m * 60 + $s;

    if(($actionValue == "clearCountdown") ||
       (($actionValue == "setCountdown") && ($actionType == "now"))){
        //
        // Update regular countdown timer
        //
        // Set
        setTimer($mac,$h,$m,$s,$act,$s20Table);
        // Confirm
        $s20Table[$mac]['timerVal'] = checkTimerSec($mac,$s20Table,$action);
        $s20Table[$mac]['timerAction'] = $action;
        if(($s20Table[$mac]['timerVal'] != $sec) ||
           ($s20Table[$mac]['timerAction'] != $act)){
            error_log("Unexpected inconsistency in timerSettings function: ".$mac." Set=(".$sec.",".$act.") Res=("
                      .$s20Table[$mac]['timerVal'].",".$s20Table[$mac]['timerAction'].")\n"); 
            return 1;
        }
    }
    else{
        // 
        // Update automatic switch off after on timer
        //
        if(setSwitchOffTimer($mac,$sec,$s20Table) != $sec){
            error_log("Set switch off unsuccessfull in timersettings (wrote ".$sec." something else read...");
            return 1;
        }
    }
    return 0;
}

?>