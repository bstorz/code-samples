<?php
/**
 * This is caught on any request to the api and serves to hand off all requests to the appropriate functions.
 *
 */

include("functions.php");

//Gets Request Parameters
$request = "$_SERVER[REQUEST_URI]";
$request = substr($request,1);
$parameters = explode("/", $request);

//Only look for post data if there is any.
if(isset($_POST["data"])) $input = $_POST["data"];

//Hand off based on the paragmeters.
switch($parameters[1]){
    case "setupEnvironment":
        echo setupEnvironment($input);
        break;
    case "newCommands":
        echo newCommands($input);
        break;
    case "clearCommands":
        echo clearCommands();
        break;
    case "getCommands":
        echo getCommands();
        break;
    case "getLastModifiedTime":
        echo getLastModifiedTime();
        break;
    case "getPhysicalGridSize":
        echo getPhysicalGridSize();
        break;
    case "getLogicalGridSize":
        echo getLogicalGridSize();
        break;
    case "getOrigin":
        echo getOrigin();
        break;
    case "setOrigin":
        echo setOrigin($input);
        break;
    case "getNotesForKeys":
        echo getNotesForKeys();
        break;
    case "getTick":
        echo getTick();
        break;
    case "setTick":
        echo setTick($input);
        break;
    case "getTickInterval":
        echo getTickInterval();
        break;
    case "newGame":
        echo newGame("");
        break; 
    case "shutdown":
        echo shutdown();
        break;
    default:
        echo "invalid";
        break;
}
?>
