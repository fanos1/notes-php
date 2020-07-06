

<?php
session_start();
$errors = array();
$out = '';

try {

  require __DIR__.'/../config.php';
  $dbc = ConnectFrontEnd::getConnection();

    // Fetch 
    $sql = "SELECT * FROM notes";

    if($result = $dbc->query($sql)) 
    {
        if($result->rowCount() > 0) 
        {
            $out .= '
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Title</th>
                    <th>Descrip</th>
                    <th>Action </th>
                  </tr>
                </thead>
                <tbody> ';

                foreach ($result as $key => $value) 
                {
                  $excerpt = substr($value['description'], 0, 10);  // abcd

                  $out .= '<tr> 
                    <td>'. htmlentities($value['title']). '</td> 
                    <td>'. htmlentities($excerpt). '... </td> 
                    <td>                       
                      <a href="read.php?id='. $value['id'].'"> <span class="glyphicon glyphicon-eye-open"></span> </a>
                      <a href="update.php?id='. $value['id'].'"> <span class="glyphicon glyphicon-pencil"></span> </a>
                      <a href="delete.php?id='. $value['id'].'"> <span class="glyphicon glyphicon-trash"></span> </a>
                    </td> 
                    
                  </tr>'; 
                }
                $out .= "</tbody></table>";  
            
            // Free result set
            unset($result);
        } else{
            //echo "<p class='lead'><em>No records were found.</em></p>";
            $out .= "<p class='lead'><em>No records were found.</em></p>";
        }
    } 
    
    // Close connection
    unset($dbc);


}
catch (Error $e) {
   echo 'error! we apologise';   
}
catch (PDOException $e) {
  $err_title = 'An error has occurred'; 
  $pdo_err_output = 'Database error: ' . $e->getMessage() . ' in ' .$e->getFile() . ':' . $e->getLine();    
  // error_log($pdo_err_output, 1, "dobalnltd@gmail.com"); // Send erro to email
  echo $pdo_err_output; // debug
  exit('An Error occured(1), we apologise1');
}










// =========== HTML =============
include (LAYOUTS.'header.html.php'); ?>

<div class="wrapper">
    <div class="container-fluid">
        <div class="row">

            <div class="col-md-12">
                <div class="page-header clearfix">
                    <h2 class="pull-left">Write and read notes</h2>
                    <a href="create.php" class="btn btn-success pull-right">Add New note</a>
                </div>

                <?php echo $out; ?>

            </div>

        </div>        
    </div>
</div>

  

  <script src="js/vendor/modernizr-3.11.2.min.js"></script>
  <!-- 
  <script src="js/plugins.js"></script> 
  <script src="js/main.js"></script>
  -->

  <!-- Google Analytics: change UA-XXXXX-Y to be your site's ID. 
  <script>
    window.ga = function () { ga.q.push(arguments) }; ga.q = []; ga.l = +new Date;
    ga('create', 'UA-XXXXX-Y', 'auto'); ga('set', 'anonymizeIp', true); ga('set', 'transport', 'beacon'); ga('send', 'pageview')
  </script>
  <script src="https://www.google-analytics.com/analytics.js" async></script>
  -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>    
<script type="text/javascript">
$(document).ready(function() {
  
  $('[data-toggle="tooltip"]').tooltip();   

    
});    

</script>       
    

</body>
</html>
