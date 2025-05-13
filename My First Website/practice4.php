<table border = "1">
<?php
    for ($x = 0; $x <= $_POST['size']; $x++){
        echo "<tr>";
        if ($x == 0){
            echo "<td></td>";
            for ($a = 1; $a <= $_POST['size']; $a++){
                echo "<td>$a</td>";
            }
        }
        else {
            for($y = 0; $y <= $_POST['size']; $y++){
                if($y == 0){
                    echo "<td>$x</td>";
                }
                else{
                    $z = $x * $y;
                    echo "<td>$z</td>";
                }
            }
        }
        echo "</tr>";
    }
?>  
