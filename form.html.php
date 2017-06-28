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
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link href="styles.css" type="text/css" rel="stylesheet">
    <title><?php echo $title; ?></title>
  </head> 
  
  <body>
    <hr/>
    <div id="container">
        <?php echo $header; ?>
        <form method="POST" action="">  
            <div>
                <label>Enter username:<input type='text' name='user' 
                    class="form-control <?php echo $input; ?>" 
                    value="<?php if (isset($_POST['user'])) 
                                 echo $_POST['user']; ?>" 
                    required />
                </label>
            </div>
            <div>
                <label>Enter password:<input type='password' name='pwd' 
                    class="form-control txt-input" 
                    value="<?php if (isset($_POST['pwd'])) 
                                 echo $_POST['pwd']; ?>" 
                    required />
                </label>
            </div>
            <div><input class="btn btn-red" type="submit" value="Submit" 
                        name="submit"/>
            </div>
        </form>
    </div>
    <?php echo $back; ?>