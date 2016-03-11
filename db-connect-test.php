<?php
            # Fill our vars and run on cli
            # $ php -f db-connect-test.php
            $dbname = 'wordpress246';
            $dbuser = 'wreta';
            $dbpass = 'M_sql16!';
            $dbhost = 'localhost';
           $link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
    
    /* comprueba la conexión */
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
    
    
?>