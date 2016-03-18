<div class="table-responsive">
<table class="table table-condensed">
    <thead> 
        <tr> 
           <th>#</th><th>idPost</th> <th>Nombre</th> <th>Apellido</th><th>Dni</th><th>Sexo</th><th>Email</th>
            <th>Teléfono</th><th>Movil</th>
            <th>Pais</th> <th>Dirección</th><th>Ciudad</th><th>Zip</th><th>Provincia</th>
            <th>Aniversario</th><th>Mayor Edad</th><th>Federado</th><th>Race Phone</th>
            <th>Contacto</th><th>Tel. Contacto</th><th>Talla</th><th>fecha Inscrp.</th><th>Estado</th><th>Tipo</th>
        </tr> 
    </thead> 
    <tbody> 
<?php
    header('Content-Type: text/html; charset=utf-8');
    include_once "db-connect-test.php";

if ($result = mysqli_query($conn, "SELECT DATABASE()")) {
    
    $row = mysqli_fetch_row($result);
    /*conectar a _posts*/
    $queryPosts = "SELECT * 
                   FROM xlnx_store_posts WHERE post_type like 'shop_%';";
    $postResult = mysqli_query($conn, $queryPosts);
 
    $contador = 0;
    if (mysqli_num_rows($postResult) > 0) {
        while ($rowPosts = mysqli_fetch_assoc($postResult)) {
        $contador ++;
        /*conectar a _postmeta*/
       /*Datos personales*/
        $queryPmeta= "SELECT meta_value 
            FROM xlnx_store_postmeta WHERE post_id = " . $rowPosts["ID"] . "";
        $qFirstName = $queryPmeta . " and meta_key = '_billing_first_name';";
        $qLastName = $queryPmeta . " and meta_key = '_billing_last_name';";
        $qEmail = $queryPmeta . " and meta_key = '_billing_email';";
        $qPhone = $queryPmeta . " and meta_key = '_billing_phone';";
        $qCountry = $queryPmeta . " and meta_key = '_billing_country';";
        $qAddress = $queryPmeta . " and meta_key = '_billing_address_1';";
        $qCity = $queryPmeta . " and meta_key = '_billing_city';";
        $qZip = $queryPmeta . " and meta_key = '_billing_postcode';";
              
        //connect to usermeta
        $queryUserMeta = "SELECT meta_value FROM xlnx_store_usermeta 
                        WHERE user_id ='" . $rowPosts["ID"] . "'";
        $resultUserMeta = mysqli_query($conn, $queryUserMeta);
        $userMeta = mysqli_fetch_assoc($resultUserMeta);
        
        //datos usuario
        $qState = $queryUserMeta . " and meta_key = 'billing_state';";
        $qBirthDate = $queryUserMeta . " and meta_key = 'user_birth_date';";
        $qDniUser = $queryUserMeta . " and meta_key = 'user_id_number';";
        $qSexo = $queryUserMeta . " and meta_key = 'user_gender';";
        $qUserMobile = $queryUserMeta . " and meta_key = 'user_mobile';";

        
        $fieldPersonalData = array($qFirstName,$qLastName,$qEmail,$qPhone,$qCountry,$qAddress,$qCity,$qZip,$qState,$qBirthDate,$qDniUser,$qSexo,$qUserMobile);
        $cnt = 0;
        foreach ($fieldPersonalData as $clave => $value) {
            $dbQuery = mysqli_query($conn, $value);

            switch ($cnt){
                case 0;
                    $firstName = mysqli_fetch_assoc($dbQuery);
                    $cnt ++;
                    break;
                case 1;
                    $lastName = mysqli_fetch_assoc($dbQuery);
                    $cnt ++;
                    break;
                case 2;
                    $email = mysqli_fetch_assoc($dbQuery);
                    $cnt ++;
                    break;
                case 3;
                    $phone = mysqli_fetch_assoc($dbQuery);
                    $cnt ++;
                    break;
                case 4;
                    $country = mysqli_fetch_assoc($dbQuery);
                    $cnt ++;
                    break;
                case 5;
                    $address = mysqli_fetch_assoc($dbQuery);
                    $cnt ++;
                    break;
                case 6;
                    $city = mysqli_fetch_assoc($dbQuery);
                    $cnt ++;
                    break;
                case 7;
                    $zip = mysqli_fetch_assoc($dbQuery);
                    $cnt ++;
                    break;
                case 8;
                    $state = mysqli_fetch_assoc($dbQuery);
                    $cnt ++;
                    break;
                case 9;
                    $birthDate = mysqli_fetch_assoc($dbQuery);
                    $cnt ++;
                    break;
                case 10;
                    $dniUser = mysqli_fetch_assoc($dbQuery);
                    $cnt ++;
                    break;
                case 11;
                    $sexo = mysqli_fetch_assoc($dbQuery);
                    $cnt ++;
                    break;
                case 12;
                    $userMobil = mysqli_fetch_assoc($dbQuery);
                    $cnt ++;
                    break;
            }
        }

        /*conectar a _order_items*/
        $queryOrderItemId = "SELECT order_item_id 
                            FROM xlnx_store_woocommerce_order_items WHERE order_id ='" . $rowPosts["ID"] . "';";
        $resultOrderItemId = mysqli_query($conn, $queryOrderItemId);
        $orderItemId = mysqli_fetch_assoc($resultOrderItemId);

        /*conectar a _order_itemmeta*/
        $queryOrderItemMeta = "SELECT meta_value 
                                FROM xlnx_store_woocommerce_order_itemmeta 
                                WHERE order_item_id = " . $orderItemId["order_item_id"] . "";
        
        /*Datos acompañante*/
        $qMayorEdad = $queryOrderItemMeta . " and meta_key = 'Age';";
        $qFederado = $queryOrderItemMeta . " and meta_key = 'Are you affiliated to any federation?';";
        $qRacePhone = $queryOrderItemMeta . " and meta_key = 'Your race-day phone number';";
        $qContacto = $queryOrderItemMeta . " and meta_key = 'Name of you contact person';";
        $qContactoPhone = $queryOrderItemMeta . " and meta_key = 'Your contact person's phone number';";
        $qTalla = $queryOrderItemMeta . " and meta_key = 'Cycling jersey size';";

        $orderItemMeta = array($qMayorEdad,$qFederado,$qRacePhone,$qContacto,$qContactoPhone,$qTalla);

        $cnt2 = 0;
        foreach ($orderItemMeta as $key => $value) {
            $dbQuery = mysqli_query($conn, $value);

            switch ($cnt2){
                case 0;
                    $mayorEdad = mysqli_fetch_assoc($dbQuery);
                    $cnt2 ++;
                    break;
                case 1;
                    $federado = mysqli_fetch_assoc($dbQuery);
                    $cnt2 ++;
                    break;
                case 2;
                    $racePhone = mysqli_fetch_assoc($dbQuery);
                    $cnt2 ++;
                    break;
                case 3;
                    $contacto = mysqli_fetch_assoc($dbQuery);
                    $cnt2 ++;
                    break;
                case 4;
                    $contactoPhone = mysqli_fetch_assoc($dbQuery);
                    $cnt2 ++;
                    break;
                case 5;
                    $talla = mysqli_fetch_assoc($dbQuery);
                    $cnt2 ++;
                    break;
            }
            
        }
      
            if (strpos($mayorEdad["meta_value"], 'I confirm') !== FALSE) {
                $esMenorEdad = 1;
            } else { 
                $esMenorEdad = 0;
            };
            if (strpos($federado["meta_value"], 'I confirm') !== FALSE) {
                $esFederado = 1;
            } else { 
                $esFederado = 0;
            };

            echo "<tr>";
                    echo "<td>" . $contador . "</td>";
                    echo "<td>" . $rowPosts["ID"] . "</td>";
                    echo "<td>" . $firstName["meta_value"] . "</td>";
                    echo "<td>" . $lastName["meta_value"] . "</td>";
                    echo "<td>" . $dniUser["meta_value"] . "</td>";
                    echo "<td>" . $sexo["meta_value"] . "</td>";
                    echo "<td>" . $email["meta_value"] . "</td>";
                    echo "<td>" . $phone["meta_value"] . "</td>";
                    echo "<td>" . $userMobil["meta_value"] . "</td>";
                    echo "<td>" . $country["meta_value"] . "</td>";
                    echo "<td>" . $address["meta_value"] . "</td>";
                    echo "<td>" . $city["meta_value"] . "</td>";
                    echo "<td>" . $zip["meta_value"] . "</td>";
                    echo "<td>" . $state["meta_value"] . "</td>";
                    
                    echo "<td>" . $birthDate["meta_value"] . "</td>";
                    echo "<td>" . $esMenorEdad["meta_value"] . "</td>";
                    echo "<td>" . $esFederado["meta_value"] . "</td>";
                    echo "<td>" . $racePhone["meta_value"] . "</td>";
                    echo "<td>" . $contacto["meta_value"] . "</td>";
                    echo "<td>" . $contactoPhone["meta_value"] . "</td>";
                    echo "<td>" . $talla["meta_value"] . "</td>";

                    echo "<td>" . $rowPosts["post_date"]."</td>";
                    echo "<td>" . $rowPosts["post_status"]."</td>";
                    echo "<td>" . $rowPosts["post_type"]."</td>";
            echo "</tr>";
        }

    } else {
             echo "<h1>No he podido extraer los datos</h1>";             
    }

} else {
        echo "No se ha podido conectar a la Base de Datos!";
}
   
?>

   
    </tbody> 
</table>
    </div>
<?
    /* cerrar la conexión */
    mysqli_close($conn);
    /*include "conn-sql-srv.php";*/
?>