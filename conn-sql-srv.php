<?php
/*
Connect to the local server using Windows Authentication and specify
the AdventureWorks database as the database in use. To connect using
SQL Server Authentication, set values for the "UID" and "PWD"
 attributes in the $connectionInfo parameter. For example:
$connectionInfo = array( "Database"=>"xalenx", "UID"=>"wreta", "PWD"=>"20S_ql16!");
*/
/*$serverName = "(local)";*/
$serverName = "UX-DESKTOP\SQLEXPRESS";
$connectionInfo = array( "Database"=>"Xalenx");

echo "me voy a conectar";
$conn = mssql_connect($serverName, 'wreta', '20S_ql16!');
print_r(sqlsrv_errors());

if ($conn) {
     echo "Connection established.\n";
} else {
     echo "Connection could not be established.\n";
     die(print_r(sqlsrv_errors(), true));
}

//-----------------------------------------------
// Perform operations with connection.
//-----------------------------------------------

/* Close the connection. */
sqlsrv_close($conn);
?>