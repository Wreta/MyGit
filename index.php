<?php
    
    include_once "db-connect-test.php";
    /* devuelve el nombre de la base de datos actualmente seleccionada */
    if ($result = mysqli_query($link, "SELECT DATABASE()")) {
        
        $row = mysqli_fetch_row($result);
        $sql = "Select meta_id, order_item_id, meta_key from xlnx_store_woocommerce_order_itemmeta where order_item_id = 152;";
        $queryResult = mysqli_query($link, $sql);

        if (mysqli_num_rows($queryResult) > 0) {
            while ($row = mysqli_fetch_assoc($queryResult)) {
            echo "Id: " . $row["meta_id"]. " - Id Order: " . $row["order_item_id"]. " - Key: " . $row["meta_key"]. "<br>";
            }
                   
        } else {
         
        var_dump($queryResult);
   
        }       
        
       
    } else {
        echo "Adios mundo cruel!";
    }
?>