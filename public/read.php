<?php
session_start();

// Define variables and initialize with empty values
$description = $title = "";
$errors = array();
$out = '';


try {
    require __DIR__.'/../config.php';
    $dbc = ConnectFrontEnd::getConnection();


    if ($_SERVER['REQUEST_METHOD'] === 'GET') 
    {
        // Check existence of id parameter before processing further
        if(isset($_GET["id"]) )
        {   
            if (filter_var($_GET["id"], FILTER_VALIDATE_INT)) {
                $id = trim($_GET["id"]);        
            } else {
                // URL doesn't contain id parameter. Redirect to error page
                //header("location: error.php");
                //exit();
                exit("URL validation error");
            }
            
            
            // Prepare a select statement
            $sql = "SELECT * FROM notes WHERE id = :id";
            
            $stmt = $dbc->prepare($sql);  
            $stmt->bindParam(":id", $id);            
            if($stmt->execute()) {

                if($stmt->rowCount() == 1) {
                    // result should contain 1 row only
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $name = $row["title"];
                    $address = $row["description"];
                }                
            } else {
                exit("execute failed! we apologise"); 
            }
                     
            // Close statement
            unset($stmt);            
            // Close connection
            unset($dbc);
        } 

    }// 'REQUEST_METHOD' == GET 

}
catch (Error $e) {
   echo 'error! we apologise';   
   echo "<h3> $e </h3>"; // debug only
}
catch (PDOException $e) {
  $err_title = 'An error has occurred'; 
  $pdo_err_output = 'Database error: ' . $e->getMessage() . ' in ' .$e->getFile() . ':' . $e->getLine();    
  // error_log($pdo_err_output, 1, "dobalnltd@gmail.com"); // Send erro to email
  echo $pdo_err_output; // debug
  exit('An Error occured(1), we apologise1');
}

/* 
* generate random string and put it in SESSION
* embed this string to the <form>. When form is submitted we compare the Submited FormToken with our SESSION[formtoken]
* If user submited different formToken with <form>, exit the app. 
*/
$_SESSION['formtoken1'] = md5(uniqid(rand(), true));
$formToken1 = $_SESSION['formtoken1'];



// =========== HTML =============
include (LAYOUTS.'header.html.php'); ?>

<div class="wrapper">
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-12">
                <div class="page-header">
                    <h1>View Record</h1>
                </div>

                <div class="form-group">
                    <label>Title</label>
                    <p class="form-control-static"><?php echo $row["title"]; ?></p>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <p class="form-control-static"><?php echo htmlentities( $row["description"]); ?></p>
                </div>                
            </div>

        </div>

        <div class="row">
            <div class="col-md-12"><?php echo $out; ?></div>
            <p><a href="index.php" class="btn btn-primary">Back</a></p>
        </div>

    </div>
</div>


</body>
</html>