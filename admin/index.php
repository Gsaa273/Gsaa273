<?php
include 'auth.php'; // Authentication check to protect the admin area
?>
<!doctype html>
<html lang="en">
<?php
include 'header.php'; // Include the header
include("../db.php"); // Include database connection
?>
<body class="n">
<?php
include "topheader.php"; // Include the top navigation bar
include "sidenav.php";   // Include the side navigation bar
?>
<main id="view-panel">
    <div class="container-fluid">
        <?php
        // Dynamically load pages based on the 'page' query parameter
        $page = isset($_GET['page']) ? $_GET['page'] : 'home';
        include $page . '.php';
        ?>
    </div>
</main>
<script type="text/javascript">

    window.start_load = function(parent='body'){
        $(parent).append('<div id="preloader2"></div>');
    }
    window.end_load = function(){
        $('body #preloader2').remove();
    }
</script>
</body>
</html>