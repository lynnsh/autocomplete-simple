<html>
    
  <head>
    <meta charset="UTF-8">   
    <link rel="stylesheet" href=
      "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script 
      src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js">
    </script>
    <link rel="stylesheet" 
      href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="styles.css" type="text/css" rel="stylesheet">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="scripts.js"></script>
    <title>Autocomplete Project</title>
  </head> 
  
  <body>
      <hr/>
      <div id="container">
        <h2 class='header'>Enter the city:</h2>       
        <form method="GET" action="" name="search">
            <div class="row">
                <div class="city"><input type='text'
                     class="form-control txt-red" name='selected' size='10'
                     id='city' value="<?php if (isset($_GET['selected'])) 
                                        echo $_GET['selected']; ?>" />
                </div>
                <div><input class="btn btn-red" 
                     type="submit" value="Submit" name="submit" 
                     onclick="onSubmit()"/>
                </div>
            </div>
        </form>
        
        <div id='list'>           
        <?php
            require 'DAOCitySearch.php';
            $daoCity = new DAOCitySearch();
            session_start();
            session_regenerate_id();

            //update/add new value to the database if the city was selected
            if(!empty($_GET['selected'])) {
                if(strlen($_GET['selected']) < 255) {
                    $value = htmlentities($_GET['selected']);
                    $user = isset($_SESSION['user']) ? $_SESSION['user']: null;
                    $daoCity -> insertSearch($value, $user);
                    echo "<h3 id='choice'>Your choice:</h3><p>$value</p>";
                }
                else
                    echo "<p class='error'>The city name is too long!</p>";
            }
        ?>
        </div>
        <div class="links">
            <?php 
            //login links
            if(!isset($_SESSION['user']))
                echo '<a class="btn btn-a" href="login.php">Login</a> - '
                   . '<a class=" btn btn-a" href="register.php">'
                   . 'Register</a>';
            else
                echo '<a class="btn btn-a" href="logout.php">Logout</a>';
            ?>
      </div>
    </div>
  </body>
</html>