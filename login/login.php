<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: ../secured/welcome.php");
    exit;
}
 
// Include config file
require_once "../config/database.php";
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";


function updateIP() {
    global $mysqli;
    $sql = "UPDATE users SET ip = ?, last_login = ? WHERE username = ?;";

    if($stmt = $mysqli->prepare($sql)){
        echo "prepare";
        if (! isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $client_ip = $_SERVER['REMOTE_ADDR'];
        }  else {
            $client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        date_default_timezone_set('Europe/Berlin');
        $date = date('Y-m-d H:i:s', time());

        $stmt->bind_param("sss", $client_ip, $date ,$_SESSION["username"]);
        echo "bind";
        if($stmt->execute()){
            echo "execute";
        }
    }

}
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Bitte Benutzername eingeben.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Bitte Passwort eingeben.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Store result
                $stmt->store_result();
                
                // Check if username exists, if yes then verify password
                if($stmt->num_rows == 1){                    
                    // Bind result variables
                    $stmt->bind_result($id, $username, $hashed_password);
                    if($stmt->fetch()){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            
                            updateIP();
                            
                            // Redirect user to welcome page
                           header("location: ../secured/welcome.php");
                        } else{
                            // Display an error message if password is not valid
                            $password_err = "Passwort falsch.";
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $username_err = "Benutzer nicht gefunden.";
                }
            } else{
                echo "Oops! Irgendwas ist schief gelaufen.";
            }
        }
        
        // Close statement
        $stmt->close();
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
<div id="login-page" class="row z-depth-12 card-panel">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 

        <div class="center">
            <h2>Login</h2>
            <p>Please fill in your credentials to login.</p>
        </div>

        <div class="form-group">
            <div class="input-field col s12">
                <i for="username" class="material-icons prefix">account_circle</i>
                <input id="username" type="text" required="" aria-required="true" name="username" 
                    class="form-control validate <?php echo (!empty($username_err)) ? 'invalid' : ''; ?>" value="<?php echo $username; ?>">
                <label for="username" data-error="<?php echo $username_err; ?>">Benutzername</label>
                <span class="helper-texts"><?php echo $username_err; ?></span>
        </div>

        <div class="form-group">
            </div>
            <div class="input-field col s12 <?php echo (!empty($password_err)) ? 'data-error' : ''; ?>">
                <i class="material-icons prefix">lock_outline</i>
                <label for="password">Passwort</label>
                <input id="password" type="password" required="" aria-required="true" name="password" 
                    class="form-control validate <?php echo (!empty($password_err)) ? 'invalid' : ''; ?>">
                <span class="helper-texts"><?php echo $password_err; ?></span>
            </div>
        </div>

        <div class="row">          
            <label>
                <input type="checkbox" checked="checked"/>
                <span>Angemeldet bleiben</span>
            </label>
        </div>

        <div class="form-group">
            <input type="submit" class="btn btn-primary blue darken-1 col s12" value="Anmelden">
        </div>

        <div class="row">
            <div class="input-field col s6 m6 l6">
                <p class="margin medium-small"><a href="register.php">Jetzt Registrieren!</a></p>
            </div>
            <div class="input-field col s6 m6 l6">
                <p class="margin right-align medium-small"><a href="#">Passwort vergessen?</a></p>
            </div>          
        </div>
    </form>
</div>
<?php include '../includes/footer.php'; ?>