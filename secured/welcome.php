<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login/login.php");
    exit;
}
// TODO: save to database
require_once "../config/database.php";
$err_to_long = "";

function getPosts() {
    global $mysqli;
    $sql = "SELECT * FROM posts ORDER BY rating DESC, create_date DESC;";
    return $mysqli->query($sql);
}

function post() {
    global $mysqli;

        //new post created
    //encode special chars to avoid injection
    $jodel = htmlspecialchars($_POST['jodel'], ENT_QUOTES);                        
    $jodel = trim(preg_replace('/\s\s+/', ' ', $jodel));

    if (strlen($jodel) < 1) {
        $err_to_long = "Text ist zu kurz";
        return;
    }


    if (strlen($jodel) > 1000) {
        $err_to_long = "Text ist zu lang";
        return;
    }

    $sql = "INSERT INTO posts (text,create_user, create_date) VALUES(?, ?, ?);";
    if($stmt = $mysqli->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        $date = date('Y-m-d H:i:s');
        $stmt->bind_param("sss", $jodel, $_SESSION['username'], $date);

        // Attempt to execute the prepared statement
        if(!$stmt->execute()){
            echo "Oops! Something went wrong. Please try again later.";
        }

        header("location: ../secured/welcome.php");
        // Close statement
        $stmt->close();
    }
}


function rating() {
    global $mysqli;

    if(isset($_GET['tu'])) {
        $id = $_GET['tu'];
        $thumb = "u";
    } else {
        $id = $_GET['td'];
        $thumb = "d";
    }

    function getRating() {
        global $mysqli;

        $sql = "SELECT rating FROM posts WHERE id = ?;";
        if($stmt = $mysqli->prepare($sql)){

            if(isset($_GET['tu'])) {
                $stmt->bind_param("i", $_GET['tu']);
            } else {
                $stmt->bind_param("i", $_GET['td']);
            }

            if($stmt->execute()){
                $res = $stmt->get_result();
                while ($row = $res->fetch_array(MYSQLI_NUM)) {
                    foreach ($row as $r) {
                        $rating = $r;
                    }
                }
                $stmt->close();
                return $rating;
            }
        }
    }

    function ratingAvaliable($id, $thumb) {
        global $mysqli;
        $user = $_SESSION['username'];

        $sql = "SELECT * FROM rating WHERE liked = ? AND user = ? AND thumb = ?;";
        if($stmt = $mysqli->prepare($sql)){
            $stmt->bind_param("iss",$id, $user, $thumb);

            if($stmt->execute()){
                $stmt->store_result();
                    
                if($stmt->num_rows >= 1){
                    return false;
                }
                
                $stmt->close();
                return true;
            }
        }
        return false;
    }

    function updateRatingList($id, $thumb) {
        global $mysqli;
        $user = $_SESSION['username'];

        if ($thumb == "u") {
            $t = "d";
        } else {
            $t = "u";
        }

        if (ratingAvaliable($id, $t)) {
            $sql = "INSERT INTO rating (liked, user, thumb) VALUES($id, '$user', '$thumb');";
            $mysqli->query($sql);

        } else {
            $sql = "UPDATE rating SET thumb = '$thumb' WHERE liked = $id AND user = '$user';";
            $mysqli->query($sql);
        }
    }


    // Only if hasn't rated yet
    if (ratingAvaliable($id, $thumb)) {
        $sql = "UPDATE posts SET rating = ? WHERE id = ?;";
        if($stmt = $mysqli->prepare($sql)){
            $rating = getRating();

            // Bind variables to the prepared statement as parameters
            if(isset($_GET['tu'])) {
                $id = $_GET['tu'];
                ++$rating;
                $thumb = "u";
            } else {
                $id = $_GET['td'];
                --$rating;
                $thumb = "d";
            }
            
            $stmt->bind_param("ii", $rating, $id);
            if ($stmt->execute()) {
                // User liked
                $stmt->close();

                updateRatingList($id, $thumb);
            }
        } else {
            echo "Error";
        }
    }
    //header("location: ../secured/welcome.php");
}


function removePost(){
    global $mysqli;
    function removeAvailable() {
        global $mysqli;
        $sql = "SELECT * FROM posts WHERE id=? AND create_user = ?";
        if($stmt = $mysqli->prepare($sql)){
            
            $stmt->bind_param("is", $_GET["remove"], $_SESSION["username"]);
            if ($stmt->execute()) {
                $stmt->store_result();
                    
                if($stmt->num_rows == 1){        
                    return true;
                }
            }
        }
        return false;
    }

    if (removeAvailable()) {
        $sql = "DELETE FROM posts WHERE id = ?";
        if($stmt = $mysqli->prepare($sql)){
    
            $stmt->bind_param("i", $_GET["remove"]);
            if ($stmt->execute()) {
    
            }
        }
    }
}


function updateJodel() {
    global $mysqli;
    global $jodelText;

    $sql = "UPDATE posts SET text = ? WHERE id = ?;";
    if($stmt = $mysqli->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("si", trim($_POST["jodelText"]), $_GET["edit"]);
        if ($stmt->execute()) {
            // User liked
            $stmt->close();
        }
    }
}

function getJodelText($id) {
    global $mysqli;

    $sql = "SELECT text FROM posts WHERE id = ?;";
    if($stmt = $mysqli->prepare($sql)){

        $stmt->bind_param("i", $id);

        if($stmt->execute()){
            $res = $stmt->get_result();
            while ($row = $res->fetch_array(MYSQLI_NUM)) {
                foreach ($row as $r) {
                    $res = $r;
                }
            }
            $stmt->close();
            
            return htmlspecialchars($res);
        }
    }
    return "err";
}
$jodelText = "";
$postData = getPosts();

if(isset($_GET['post'])){
    post();
}

if(isset($_GET['tu']) or isset($_GET['td'])){
    rating();
    $postData = getPosts();
}

if(isset($_GET['remove'])){
    removePost();
    $postData = getPosts();
}

if(isset($_POST['update'])){
    $jodelText = getJodelText(htmlspecialchars($_GET['update']));
}

if(isset($_GET['edit'])){
    updateJodel();
    $postData = getPosts();
}





 //include header
include '../includes/header.php';
// Menubar
include '../includes/navbar.php';
?>


<!-- Content -->
<ul class="collection container with-header" style="margin-top: 15px;">
    <li class="collection-header">
        <div class="row">
            <form class="col l12 s12 xl12" action="?post=1" method="POST" enctype="multipart/form-data">
            <h5>Hey <?php echo htmlspecialchars($_SESSION["username"]); ?>, was geht?</h5>
                <div class="form-group row">
                    <div class="input-field col s12">
                    <textarea id="textarea1" name="jodel" class="form-control materialize-textarea" required="true" data-length="255"></textarea>
                    <label for="textarea1">Your post</label>
                    </div>
                    <label for="textarea1"><?php echo $err_to_long; ?></label>
                </div>
                <!-- save the color in a hidden field -->
                <button type="submit" class="btn btn-warning">Posten</button>
            </form>
        </div>
    </li>
</ul>

<!-- Modal Structure -->
<form id="editField" action="?edit=0" class="modal" method="POST">
    <div class="modal-content">
    <label for="jodelText"><b>Trödl</b></label>
        <input id="jodelText" type="text" value="<?php echo htmlspecialchars($jodelText); ?>" name="jodelText" required="true">
    </div>

    <div class="modal-footer">
        <button type="submit" class="modal-action modal-close waves-effect waves-blue btn-flat"  disabled>Speichern</button>
        <a class="modal-close waves-effect waves-red btn-flat">Close</a>
    </div>
</form>

<?php
    while($row = mysqli_fetch_array($postData))
    {
        echo '
            <div class="row container" style="margin-top: 5px;">
                <div class="col l12 s12 xl12">
                    <div class="card">     
                        <div class="card-content">
                            <i class="material-icons prefix">account_circle</i>
                            <span class="title">'.$row["create_user"].'</span>
                            <p><br>' . $row["text"] . '</p>
                        </div>
                        <div class="card-action center">
                            <a href="#">Bewertung ' .$row["rating"]. '</a>
                            <a href="?tu='.$row["id"].'"><i class="material-icons prefix">thumb_up</i></a>
                            <a href="?td='.$row["id"].'"><i class="material-icons prefix">thumb_down</i></a>';
                            if ($row["create_user"] == $_SESSION["username"]) { 
                                echo '<div class="right">
                                    <a href="?remove='.$row["id"].'" onclick="return confirm(\'Bist du dir da sicher?\');" class="btn-floating"><i class="material-icons orange">remove</i></a>
                                    <a href="#editField" class="btn-floating modal-trigger"><i class="material-icons orange">settings</i> </a>
                                </div>';}
                                    echo '
                        </div>
                    </div>
                </div>
            </div>';
    }
?>

<?php include '../includes/footer.php'; ?>