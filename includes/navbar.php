<style type="text/css">

.dropdown-content {
  background-color: #FFFFFF;
  margin: 0;
  display: none;
  min-width: 300px; /* Changed this to accomodate content width */
  min-height: 150px;
	margin-left: -1px; /* Add this to keep dropdown in line with edge of navbar */
  overflow: hidden; /* Changed this from overflow-y:auto; to overflow:hidden; */
  opacity: 0;
  position: absolute;
  white-space: nowrap;
  z-index: 1;
  will-change: width, height;
}


</style>


<ul id="mobilenav" class="sidenav">
    <li><a href="../secured/welcome.php">Startseite</a></li>
    <li><a href="../includes/about.php">Über</a></li>
    <li><a href="../user/profile.php">Paswort ändern</a></li>
    <li><a href="../login/logout.php">Abmelden</a></li>
</ul>

<ul id="userdropdown" class="dropdown-content sidenav">
    <li><a href="../includes/about.php">Über</a></li>
    <li><a href="../user/profile.php">Paswort ändern</a></li>
    <li><a href="../login/logout.php">Abmelden</a></li>
</ul>
<nav>
    <div class="nav-wrapper blue darken-1">
        <a href="../secured/welcome.php" style = "margin-left: 15px;" class="brand-logo"><i class="material-icons">people</i>Trödel</a>
        <a href="#" data-target="mobilenav" class="sidenav-trigger"><i class="material-icons">menu</i></a>

        <ul class="right hide-on-med-and-down">
            <li><a class="dropdown-trigger" data-target="userdropdown"  data-beloworigin="true">
                <i class="material-icons left">account_circle</i>
                     <?php echo htmlspecialchars($_SESSION["username"]); ?>
                    <i class="material-icons right">arrow_drop_down</i>
                </a>
            </li>
        </ul>
    </div>
</nav>


