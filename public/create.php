<?php
session_start();

// Define variables and initialize with empty values
$description = $title = "";
$errors = array();
$out = '';


try {

    require __DIR__.'/../config.php';
    $dbc = ConnectFrontEnd::getConnection();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    {
        //check if the form submited is our own form
        if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['formtoken1']) {
            //$formtoken should always be set, if it is not set, create error
            exit('The form submited is not valid. Please reload the page');
        }
        if (!empty($_POST['med'] )) { //!empty means bots must have populated form submited             
            exit('The form submited is not valid. Med');
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
            $q = "INSERT INTO notes (title, description) VALUES(:title, :description) ";
                
            $stmt = $dbc->prepare($q);                             
            $stmt->bindParam(':description', $description);  
            $stmt->bindParam(':title', $title);              
            
            if($stmt->execute()) {                
                $out .= '<div class="alert alert-success">successfully record created!</div>';
                $out .= '<div><a href="/index.php"> Back to Home Page </a></div>';                
            } else{                
                // should not happen. Trigger Error. exit for now..
                exit("Something went wrong. Please try again later."); 
            }
            
        }


    } // END $_SERVER['REQUEST_METHOD']

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
                    <h2>Create Record</h2>
                </div>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input type="hidden" name="csrf" value="<?php echo $_SESSION['formtoken1']; ?>" />   
                    <p class="hp" style="display: none;"> <input type="text" name="med" id="med" value=""> </p>

                    <div class="form-group <?php echo (!empty($errors['title']) ) ? 'has-error' : ''; ?>">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" value="<?php echo $title; ?>">
                        <span class="help-block"> <?php echo $errors['title'];?> </span>
                    </div>
                    <div class="form-group <?php echo (!empty($errors['description']) ) ? 'has-error' : ''; ?>">
                        <label>description</label>                        
                        <textarea name="description" rows="5" cols="40" class="form-control">
                            <?php echo $description; ?>
                        </textarea>
                        <span class="help-block"> <?php echo $errors['description'];?> </span>
                    </div>                    
                    
                    <input type="submit" class="btn btn-primary" value="Submit">                        
                </form>
            </div>                
        </div>        

        <div class="row">
            <div class="col-md-12"><?php echo $out; ?></div>
        </div>

    </div>
</div>


<script type="text/javascript" src="js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
tinymce.init({
    selector: "textarea",
    themes: "modern",   
    plugins: [
        "advlist autolink lists link image charmap print preview anchor",
        "searchreplace visualblocks code fullscreen",
        "insertdatetime media table contextmenu paste moxiemanager"
    ],
    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"   
});
</script>

</body>
</html>