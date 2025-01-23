<?php  
session_start();  
session_destroy(); // This destroys the session  

// Clear the session array (optional, since session_destroy() does this)  
foreach ($_SESSION as $key => $value) {  
    unset($_SESSION[$key]);  
}  

// Redirect to login page  
header('Location: login.php');   
exit(); // Always call exit after header to stop script execution
