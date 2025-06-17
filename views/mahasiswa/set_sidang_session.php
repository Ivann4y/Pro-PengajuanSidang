<?php

session_start();

if (isset($_GET['id_sidang']) && !empty($_GET['id_sidang'])) {
    $id_sidang = $_GET['id_sidang'];

    $_SESSION['selected_sidang_id'] = $id_sidang;

    header("Location: mdetailSidang.php");
    exit();
} else {
    header("Location: mSidang.php");
    exit();
}

?>