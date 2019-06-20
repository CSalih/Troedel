<?php
// Include config file
require_once "../config/database.php";
 
// Define variables and initialize with empty values
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    $user = htmlspecialchars($_POST['username'], ENT_QUOTES);
    $user = trim(preg_replace('/\s\s+/', ' ', $user));

    // Validate username
    if(empty($user)){
        $username_err = "Bitte Benutzername eingeben.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";

        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_username);
            
            // Set parameters
            $param_username = $user;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // store result
                $stmt->store_result();
                
                if($stmt->num_rows == 1){
                    $username_err = "Benutzername nicht verfügbar.";
                } else{
                    $username = $user;
                }
            } else{
                echo "Oops! Irgendwas ist schief gelaufen.";
            }

            // Close statement
            $stmt->close();
        }
    }

    $pass = htmlspecialchars($_POST['password'], ENT_QUOTES);
    $pass = trim(preg_replace('/\s\s+/', ' ', $pass));
    
    // Validate password
    if(empty($pass)){
        $password_err = "Bitte Passwort eingeben.";     
    } elseif(strlen($pass) < 6){
        $password_err = "Passwort muss mindestens 6 Zeichen haben.";
    } else{
        $password = $pass;
    }

    $passconfirm = htmlspecialchars($_POST['confirm_password'], ENT_QUOTES);
    $passconfirm = trim(preg_replace('/\s\s+/', ' ', $passconfirm));
    
    // Validate confirm password
    if(empty($passconfirm)){
        $confirm_password_err = "Passwort bestätigen.";     
    } else{
        $confirm_password = $passconfirm;
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Passwort stimmt nicht überein.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
         
        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ss", $param_username, $param_password);
            
            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Redirect to login page
                header("location: login.php");
            } else{
                echo "Oops! Irgendwas ist schief gelaufen.";
            }
            // Close statement
            $stmt->close();
        }
    }
    
    // Close connection
    $mysqli->close();
}
 //include header
 include '../includes/header.php';
 ?>


<style>
    .banner {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        z-index: -1;
        width: 100%;
        background-image: url("../resources/image/login-background.png");
        background-size: cover;
        background-position: 50% 0%;
        background-repeat: no-repeat;
        filter: blur(16px);
    }
</style>

<link rel="stylesheet" type="text/css" href="../resources/css/login.css">
<div class="banner" tabindex="-1"></div>

<div id="login-page" class="row z-depth-6 card-panel">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 

        <div class="center">
            <h2>Registrierung</h2>
            <p>Formular bitte ausfüllen.</p>
        </div>

        <div class="input-field col s12">
            <i class="material-icons prefix">account_circle</i>
            <input id="username" type="text" name="username" class="form-control" value="<?php echo $username; ?>">
            <label for="username">Benutzername</label>
            <span class="help-block"><?php echo $username_err; ?></span>
        </div>

        <div class="form-group <?php echo (!empty($password_err)) ? 'data-error' : ''; ?>">
            <div class="input-field col s12" >
                <i class="material-icons prefix">lock_outline</i>
                <label for="password">Passwort</label>
                <input id="password" type="password" name="password" 
                        class="form-control validate" value="<?php echo $password; ?>">
                <span class="helper-texts"><?php echo $password_err; ?></span>
            </div>
        </div>

        
        <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'data-error' : ''; ?>">
            <div class="input-field col s12" >
                <i class="material-icons prefix">lock_outline</i>
                <label for="confirm_password">Passwort bestätigen</label>
                <input id="confirm_password" type="password" name="confirm_password" 
                        class="form-control validate" value="<?php echo $confirm_password; ?>">
                <span class="helper-texts"><?php echo $confirm_password_err; ?></span>
            </div>
        </div>
        
        <div class="form-group"  style="text-align:center">
            <input type="submit" class="btn btn-primary blue darken-1" value="Registrieren">
            <input type="reset" class="btn btn-default grey darken-1" value="Reset">
        </div>


        <div class="row">
            <div class="input-field col s6 m6 l6">
                <p class="margin medium-small"><a href="login.php">Ich habe ein Account</a></p>
            </div>
            <div class="input-field col s6 m6 l6">
                <p class="margin right-align medium-small"><a href="#">Passwort vergessen?</a></p>
            </div>          
        </div>
    </form>
</div>    

<?php include '../includes/footer.php'; ?>