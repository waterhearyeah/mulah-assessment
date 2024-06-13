<?php
    $hostname = "localhost";
    $user = "root";
    $password = "";
    $database = "test";
        
    $connection = mysqli_connect($hostname, $user, $password, $database);
            
    if ($connection === false){
        die("Database connection failed: " . mysqli_connect_error());
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mulaht</title>
    <style>
        table td{
            width: 200px;
            border: 1px solid black;

        }
    </style>
</head>
<body>
    <form class="" action="" method="POST" enctype="multipart/form-data" >
    <div>    
        <input type="file" name="file" accept=".csv"> 
    </div>
    <button type="submit" name="import" id="import-button" style="margin-top:10px"">Submit</button>
    </form>

    <?php
    if(isset($_POST["import"])){
        if($_FILES["file"]["size"] > 0){
            $filename = $_FILES["file"]["tmp_name"];
            $file = fopen($filename, "r");
            // Skip the first row (header)
            fgetcsv($file, 10000, ",");
            while(($column = fgetcsv($file, 10000, ",")) !== FALSE){
                $in_dex = $column[0];
                $value = $column[1];

                $sqlInsert = "INSERT INTO `mulah` (`in_dex`, `value`) VALUES (?, ?)";
                $stmt = $connection->prepare($sqlInsert);
                $stmt->bind_param("ss", $in_dex, $value);  // Assuming both fields are strings
                $insertResult = $stmt->execute();

                if(!$insertResult){
                    $error[] = "Import failed for row: " . implode(",", $column) . " - " . $connection->error;
                }
            }
            fclose($file);
            
            if(empty($error)){
                echo "CSV file have been added successfully.";
            } else {
                foreach($error as $err){
                    echo $err . "<br>";
                }
            }
        }

    // Fetch data from database
    $rows = mysqli_query($connection, "SELECT * FROM `mulah`");

    // Initialize sums
    $numA = $numB = $numC = 0;

    // Calculate sums
    while ($row = mysqli_fetch_assoc($rows)) {
        $index = $row['in_dex'];
        $value = intval($row['value']);

        if ($index == 'A5' || $index == 'A20') {
            $numA += $value;
        }
        if ($index == 'A15' || $index == 'A7') {
            $numB += $value;
        }
        if ($index == 'A13' || $index == 'A12') {
            $numC += $value;
        }
    }
} 
?>


    <table class="content-table" id= "myTable">
    <thead>
        <tr>
        <td>Index</td>
        <td>Value</td>
        </tr>
    </thead>

    <?php
        $rows = mysqli_query($connection, "SELECT * FROM `mulah`");
        foreach($rows as $row):
    ?>

    <tbody>
        <tr>
            <td><?php echo $row["in_dex"]; ?></td>
            <td><?php echo $row["value"]; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>

    </table>
    <table>
        <tr>
            <td>Category</td>
            <td>Value</td>
        </tr>
        <tr>
        <td>Alpha</td>
        <td id="numA"><?php echo $numA; ?></td>
        </tr>
        <tr>
            <td>Beta</td>
            <td id="numB"><?php echo $numB; ?></td>
        </tr>
        <tr>
            <td>Charlie</td>
            <td id="numC"><?php echo $numC; ?></td>
        </tr>
    </table>

</body>
</html>
