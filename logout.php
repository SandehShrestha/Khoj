<?php
session_start();
session_unset();
session_destroy();
session_write_close();
setcookie(session_name(),'',0,'/');
session_regenerate_id(true);

// Redirect to login page
header("Location: login/index.php");
exit();
?> 