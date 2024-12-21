<?php
// phpinfo();
try {
    // $dsn = "mysql:host=sql211.infinityfree.com;port=3306;dbname=if0_37946139_taskmanagement";
    // $username = "if0_37946139";
    // $password = "HXobTcuXon59";

    // $dsn = "mysql:host=localhost;port=3306;dbname=task_management";
    // $username = "root";
    // $password = "";


    $dsn = "mysql:host=sql12.freesqldatabase.com;port=3306;dbname=sql12752944";
    $username = "sql12752944";
    $password = "9w5Eq9LF97";

    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connection successful!";
   
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
