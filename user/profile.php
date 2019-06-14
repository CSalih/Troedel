<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login/login.php");
    exit;
}
 
// Include config file
require_once "../config/database.php";
 
// Define variables and initialize with empty values
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if(isset($_GET['changepassword'])){
 
    // Validate new password
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Bitte Passwort eingeben.";       
    } elseif(strlen(trim($_POST["new_password"])) < 6){
        $new_password_err = "Passwort muss mindestens 6 Zeichen haben.";
    } else{
        $new_password = trim($_POST["new_password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Passwort bestätigen.";   
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Passwort stimmt nicht überein.";
        }
    }
        
    // Check input errors before updating the database
    if(empty($new_password_err) && empty($confirm_password_err)){
        // Prepare an update statement
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        
        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("si", $param_password, $param_id);
            
            // Set parameters
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_id = $_SESSION["id"];
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Password updated successfully. Destroy the session, and redirect to login page
                session_destroy();
                header("location: ../login/login.php");
                exit();
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

if (isset($_GET['delete'])) {
    $sql = "DELETE FROM users WHERE id=?";
    
    if($stmt = $mysqli->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("i", $_SESSION["id"]);
        
        // Set parameters
        // Attempt to execute the prepared statement
        if($stmt->execute()){
            // Password updated successfully. Destroy the session, and redirect to login page
            session_destroy();
            header("location: ../login/login.php");
            exit();
        } else{
            echo "Oops! Irgendwas ist schief gelaufen.";
        }
        // Close statement
        $stmt->close();
        $mysqli->close();
    }
}
 
 //include header
include '../includes/header.php';
include '../includes/navbar.php';
?>



<!-- Navbar tabs -->
<nav class="nav-extended">
    <div class="nav-content">
        <ul id="tabs-swipe-demo" class="tabs tabs-transparent blue darken-1">
            <li class="tab"><a class="active" href="#password">Passwort ändern</a></li>
            <li class="tab"><a href="#delete">Account löschen</a></li>
        </ul>
    </div>
</div>


<!-- Tab 1 -->
<div id="password" class="col s12 blue-text text-darken-2" style="width=80%">
<div class="card-panel row z-depth-12 ">
    <form action="?changepassword=sdaSDWAllas" method="post"> 
        <div class="center">
            <h2>Passwort ändern</h2>
        </div>

        <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'data-error' : ''; ?>">
            <div class=" col s12" >
                <label for="new_password">Neues Passwort</label>
                <input id="new_password" type="password" name="new_password" 
                    class="form-control validate" value="<?php echo $new_password; ?>">
                <span class="helper-texts"><?php echo $new_password_err; ?></span>
            </div>
        </div>

        <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'data-error' : ''; ?>">
            <div class="col s12" >
                <label for="confirm_password">Passwort bestätigen</label>
                <input id="confirm_password" type="password" name="confirm_password" 
                        class="form-control validate" value="<?php echo $confirm_password; ?>">
                <span class="helper-texts"><?php echo $confirm_password_err; ?></span>
            </div>
        </div>
        
        <div class="form-group"  style="text-align:center">
            <input type="submit" class="btn btn-primary" value="Passwort zurücksetzen">
            <input type="reset" class="btn btn-default grey darken-1" value="Reset">
        </div>
    </form>
</div>
</div>

<div id="delete" class="col s12">
        <div class="center">
            <h2>Account Löschen</h2>
            <form action="?delete=sdaSDWAllas" method="post"> 
                <div class="form-group"  style="text-align:center">
                    
                        <input type="submit" onclick="return confirm('Bist du dir da ganz sicher? :(');" class="btn red" value="Account löschen!">
                </div>
            </form>
        </div>
</div>


<?php include '../includes/footer.php'; ?>