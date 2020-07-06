<?php
session_start();

// Define variables and initialize with empty values
$description = $title = "";
$errors = array();
$out = '';


try {

    require __DIR__.'/../config.php';
    $dbc = ConnectFrontEnd::getConnection();

    if(isset($_POST["id"]) && !empty($_POST["id"])) 
    {
        // Validate tin Hidden <input
        if(filter_var($_POST["id"], FILTER_VALIDATE_INT)) {
            // Get hidden input value
            $id = trim($_POST["id"]);
        } else {
            exit("Submited id should be integer");
        }


        // Check for a title:
        if (empty($_POST['title'])) {
            $errors['title'] = 'Please enter the title!';   
        } elseif (!preg_match ('/^[a-zA-Z0-9 \'.-]{2,100}$/i', $_POST['title']) ) {
            $errors['title'] = 'please enter valid title!';
        } else {            
            $allowed = '<div><p><span><br><a><h1><h2><h3><h4>';            
            $title = strip_tags($_POST['title'], $allowed); 
        }


        // Check for a dexription:
        if (empty($_POST['description'])) {
            $errors['description'] = 'Please enter the description!';   
        } else {             
            $allowed = '<div><p><span><br><a><img><h1><h2><h3><h4><ul><ol><li><blockquote>'; 
            // we allow some basic html to pass the validation   
            $description = strip_tags($_POST['description'], $allowed); 
        }


        // ------------------------
        // if No errors, INSERT 
        // -----------------------
        if (empty($errors)) 
        {            
            // Prepare an update statement
            $sql = "UPDATE notes SET title=:title, description=:description WHERE id=:id";
     
            if($stmt = $dbc->prepare($sql)) 
            {
                // Bind variables to the prepared statement as parameters
                $stmt->bindParam(":title", $title);
                $stmt->bindParam(":description", $description);
                $stmt->bindParam(":id", $id);
                
                if($stmt->execute()) {
                    // Records updated successfully. Redirect to landing page
                    header("location: index.php");
                    exit();
                } else{                    
                    exit("something wrong");
                }
            }
             
            // Close statement
            unset($stmt);
            
        }  

        // Close connection
        unset($dbc);

    } 
    else // check for GET[] request
    { 
        // Meke sure the GET[id] was passed from previous page
        if(isset($_GET["id"]) && !empty(trim($_GET["id"]))) 
        {
            // Validate tHE GET id as int
            if(filter_var($_GET["id"], FILTER_VALIDATE_INT)) {                
                // Get URL parameter
                $id = trim($_GET["id"]);
            } else {
                exit("GET id should be integer");
                // header("location: error.php");
            }

            // Prepare a select statement
            $sql = "SELECT * FROM notes WHERE id = :id";
            if($stmt = $dbc->prepare($sql)){
                // Bind variables to the prepared statement as parameters
                $stmt->bindParam(":id", $id);
                
                // Set parameters
                // $param_id = $id;
                
                if($stmt->execute()) 
                {
                    if($stmt->rowCount() == 1) {

                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                        // Retrieve individual field value
                        $title = $row["title"];
                        $description = $row["description"];
                    }
                    
                } else{                    
                    exit("someting went badly wrong");
                }
            }

            unset($stmt);            
            unset($dbc);
        }  else{
            // URL doesn't contain id parameter. Redirect to error page
            header("location: error.php");
            exit();
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
            <div class="col-md-12">
                <div class="page-header">
                    <h2>Update Record</h2>
                </div>
                <p>Please edit the input values and submit to update the record.</p>

                <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">

                    <div class="form-group <?php echo (!empty($errors['title'])) ? 'has-error' : ''; ?>">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" value="<?php echo $title; ?>">
                        <span class="help-block"><?php echo $errors['title'];?></span>
                    </div>

                    <div class="form-group <?php echo (!empty($errors['description'])) ? 'has-error' : ''; ?>">
                        <label>Description</label>
                        <textarea name="description" class="form-control"  rows="5" cols="40">
                            <?php echo $description; ?>                                
                        </textarea>
                        <span class="help-block"><?php echo $errors['description']; ?></span>
                    </div>

                    <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                    <input type="submit" class="btn btn-primary" value="Submit">
                    
                </form>
            </div>
        </div>        

        <div class="row">
            <div class="col-md-12"><?php echo $out; ?></div>
            <div class="pull-right"><a href="index.php" class="btn btn-danger">Back to Home page</a></div>
        </div>

    </div>
</div>

</body>
</html>