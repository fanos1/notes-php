<?php
session_start();

// Define variables and initialize with empty values
$description = $title = "";
$errors = array();
$out = '';


try {
    require __DIR__.'/../config.php';
    $dbc = ConnectFrontEnd::getConnection();

    // Process delete operation after confirmation
    if(isset($_POST["id"]) && !empty($_POST["id"]))
    {        
        //check if the form submited is our own form
        if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['formtoken1']) {
            //$formtoken should always be set, if it is not set, create error
            exit('The form submited is not valid. Please reload the page');
        }
        if (!empty($_POST['med'] )) { //!empty means bots must have populated form submited             
            exit('The form submited is not valid. Med');
        }

        // Check for a id: Hidden field
        if(filter_var($_POST["id"], FILTER_VALIDATE_INT)) {
            // Get hidden input value
            $id = trim($_POST["id"]);
        } else {
            exit("Submited id should be integer");
        }

        // Prepare a delete statement
        $sql = "DELETE FROM notes WHERE id = :id";

        $stmt = $dbc->prepare($sql);                
        $stmt->bindParam(":id", $param_id);            
        
        if($stmt->execute()) { 
            // Records deleted successfully. Redirect to landing page
            header("location: index.php");
            exit();
        } else{
            exit("somethin wrong with system");
        }
             
        // Close statement
        unset($stmt);        
        // Close connection
        unset($dbc);

    } 
    else // if GET[id]
    {
        // Check existence of id parameter
        if(empty(trim($_GET["id"]))){
            // users who visit this page should visit with the id PARAM GET[id]
            // header("location: error.php");
            exit('GEt without id');
        }
    }
  

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


        </div>        

        <div class="row">
            <div class="col-md-12"><?php echo $out; ?></div>
            <div class="pull-right"><a href="index.php" class="btn btn-danger">Back to Home page</a></div>
        </div>

    </div>
</div>


<div class="wrapper">
    <div class="container-fluid">
        <div class="row">

            <div class="col-md-12">
                <div class="page-header">
                    <h1>Delete Record</h1>
                </div>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input type="hidden" name="csrf" value="<?php echo $_SESSION['formtoken1']; ?>" />   
                    <p class="hp" style="display: none;"> <input type="text" name="med" id="med" value=""> </p>

                    <div class="alert alert-danger fade in">
                        <input type="hidden" name="id" value="<?php echo trim($_GET["id"]); ?>"/>
                        
                        <p>
                            <input type="submit" value="DELETE" class="btn btn-danger"> ->
                            <a href="index.php" class="btn btn-default">Back to Home page</a>
                        </p>
                    </div>
                </form>
            </div>

        </div>        
    </div>
</div>

</body>
</html>