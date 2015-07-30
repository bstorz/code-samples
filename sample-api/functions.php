<?php
/**
 * Does All the Handling for the API
 * Interfaces with the Database Controller
 */

//Communicates with a process.  Creates an
//instance of the given process and sets up
//pipes to it.
ini_set("memory_limit","512M");

/* In the interest of full disclosure, runWithInput is a modified piece
 * of code that is available under open-source on the php.net website.  */
function runWithInput($processToRun,$stdin) {
    //Setup file descriptors
    $descriptors = array(
        0 => array("pipe","r"), //stdin
        1 => array("pipe","w"), //stdout
        2 => array("pipe","w") //stderr
    );

    //Open our process
    $process = proc_open($processToRun,$descriptors, $pipes);

    //Setup varialbes
    $inputOffset = 0;
    $inputLength = strlen($stdin);

    $stdout = '';
    $stderr = '';

    $stdoutDone = false;
    $stderrDone = false;

    //Set pipes to non-blocking mode.
    stream_set_blocking($pipes[0], 0);
    stream_set_blocking($pipes[1], 0);
    stream_set_blocking($pipes[2], 0);

    //If nothing, close up stdin
    if($inputLength == 0) fclose($pipes[0]);

    //Keep going until we're done.
    while(true){
        $output = array();
        $input = array();
        $error = NULL;

        if($inputOffset < $inputLength) $input[] = $pipes[0];
        if(!$stdoutDone) $output[] = $pipes[1];
        if(!$stderrDone) $output[] = $pipes[2];
        
        stream_select($output, $input, $error, NULL, NULL);

        if(!empty($input)) {
            $inputLoc = fwrite($pipes[0], substr($stdin, $inputOffset, 8192));
            if($inputLoc !== false) $inputOffset += $inputLoc;
            if($inputOffset >= $inputLength) fclose($pipes[0]);
        }

        foreach($output as $outputPipe){
            if($outputPipe == $pipes[1]){
                $stdout .= fread($pipes[1], 8192);
                if(feof($pipes[1])){
                    fclose($pipes[1]);
                    $stdoutDone = true;
                }
            }
            else if($outputPipe == $pipes[2]){
                $stderr .= fread($pipes[2], 8192);
                if(feof($pipes[2])){
                    fclose($pipes[2]);
                    $stderrDone = true;
                }
            }
        }

        if(!is_resource($process)) break;
        if($inputOffset >= $inputLength && $stdoutDone && $stderrDone) break;
    }

    $returnValue = proc_close($process);

    //Returns an array with stdout and stderr
    return array("stdout"=>$stdout,"stderr"=>$stderr);
}

//Handle the Database Controller
function dbcCmd($stdin) {
    return runWithInput("/opt/local/bin/python dbc/dbc.py",$stdin);
}

//Handle the Game of Life
function newGame($stdin) {
    $output = dbcCmd("clearCommands");
    shell_exec("/Applications/MAMP/htdocs/gol/gol");
    return str_replace("\n","",$output["stdout"]);
}
function shutdown(){
    runWithInput("/usr/bin/sudo /sbin/shutdown -h now");
    return "Shutting down now.  Please wait.";
}    
//Sets up the environment.  Not likely to be used, but offered.
function setupEnvironment($input){
    $output = dbcCmd("setupEnvironment\n".$input);
    return str_replace("\n","",$output["stdout"]);
}

//Inputs new commands.  Not likely to be used, but available.
function newCommands($input){
    $output = dbcCmd("newCommands\n".$input);
    return str_replace("\n","",$output["stdout"]);
}

//Clears all commands.
function clearCommands(){
    $output = dbcCmd("clearCommands\n");
    return str_replace("\n","",$output["stdout"]);
}

//Gets all commands.
function getCommands(){
    $output = dbcCmd("getCommands\n");

    //Error Check
    if($output["stdout"] == "failed\n"){
        return str_replace("\n","",$output["stdout"]);
    }

    //Split it on each tick    
    $processedOutput = explode("tick\n",$output["stdout"]);
    //Remove extra first element caused by the explosion
    array_shift($processedOutput);
    
    $ticks = array();
    $i=0;

    //Splits each tick into an array of the commands issued
    foreach($processedOutput as $tick){
        $ticks[$i] = explode("\n",$tick);
        array_pop($ticks[$i]); //Remove extra last element caused by the explosion
        $i++;
    }

    //Make it json
    return json_encode($ticks);
}

//TODO: Does not function.
function getLastModifiedTime(){
    /*$output = dbcCmd("getLastModifiedTime\n");
    
    //Error Check
    if($output["stdout"] == "failed\n"){
        return str_replace("\n","",$output["stdout"]);
    }
    
    $rtn = array("lastModifiedTime"=>str_replace("\n","",$output["stdout"]));
    return json_encode($rtn);*/
    return;
}

//Gets the Physical Grid Size separated by a comma
function getPhysicalGridSize(){
    $output = dbcCmd("getPhysicalGridSize\n");
    
    //Error Check
    if($output["stdout"] == "failed\n"){
    	return str_replace("\n","",$output["stdout"]);
    }
    
    $rtn = array("physicalGridSize"=>str_replace("\n","",$output["stdout"]));
    return json_encode($rtn);
}

//Gets the Logical Grid Size separated by a comma
function getLogicalGridSize(){
    $output = dbcCmd("getLogicalGridSize\n");
    
    //Error Check
    if($output["stdout"] == "failed\n"){
        return str_replace("\n","",$output["stdout"]);
    }
    
    $rtn = array("logicalGridSize"=>str_replace("\n","",$output["stdout"]));
    return json_encode($rtn);
}

//Get the origin where the physicalGrid matches up with the logicalGrid
function getOrigin(){
    $output = dbcCmd("getOrigin\n");
    //Error Check
    if($output["stdout"] == "failed\n"){
        return str_replace("\n","",$output["stdout"]);
    }
    $rtn = array("origin"=>str_replace("\n","",$output["stdout"]));
    return json_encode($rtn);
}

//Set the origin
function setOrigin($input){
    $output = dbcCmd("setOrigin\n".$input);
    return str_replace("\n","",$output["stdout"]);
}

//Gets the notes for the keys
//Pretty much useless at this point.
function getNotesForKeys(){
    $output = dbcCmd("getNotesForKeys\n");
    
    //Error Check
    if($output["stdout"] == "failed\n"){
        return str_replace("\n","",$output["stdout"]);
    }
    
    return $output["stdout"];
}

//Gets the current tick
function getTick(){
    $output = dbcCmd("getTick\n");
    
    //Error Check
    if($output["stdout"] == "failed\n"){
        return str_replace("\n","",$output["stdout"]);
    }
    
    $rtn = array("tick"=>str_replace("\n","",$output["stdout"]));
    return json_encode($rtn);
}

//Sets the current tick. Not likely to be used, but available.
function setTick($input){
    $output = dbcCmd("setTick\n".$input);
    return str_replace("\n","",$output["stdout"]);
}

//Gets the interval at which ticks should be played.
function getTickInterval(){
    $output = dbcCmd("getTickInterval\n");
    
    if($output["stdout"] == "failed\n"){
        return str_replace("\n","",$output["stdout"]);
    }
    
    $rtn = array("tickInterval"=>str_replace("\n","",$output["stdout"]));
    return json_encode($rtn);
}
?>
