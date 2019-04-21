<?php
session_start();
include('../backend/functions.php');

//echo $_SESSION['name'];
//echo $_SESSION['memberID'];
//if (isset($_SESSION['memberID'])) {
//    header("location: myplaylist.php"); // Redirecting To Profile Page
//}
if (isset($_POST['search'])) {
    $query = $_POST['query'];
} else {

}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MoodWaves</title>
    <script src="https://code.jquery.com/jquery-3.4.0.slim.min.js"
            integrity="sha256-ZaXnYkHGqIhqTbJ6MB4l9Frs/r7U4jlx7ir8PJYBqbI="
            crossorigin="anonymous"></script>
  <link rel="shortcut icon" href="../images/fav_icon.png" type="image/x-icon">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<!--  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">-->
    <link href="https://fonts.googleapis.com/css?family=Raleway:500,600,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Rubik:400,500,700" rel="stylesheet">
  <!-- Bulma Version 0.7.4-->
  <link rel="stylesheet" href="https://unpkg.com/bulma@0.7.4/css/bulma.min.css" />
  <link rel="stylesheet" type="text/css " href="css/style.css">


</head>
<body>
  <section class="hero is-dark is-fullheight">
    <div class="hero-head">
      <nav class="navbar is-transparent">
        <div class="container">
          <div class="navbar-brand">
            <a class="navbar-item" href="index.php">
              <h1 class="is-size-3"><i class="fa fa-headphones"> | </i><span class="has-text-weight-bold">&nbsp;MoodWaves</span></h1>
            </a>
            <span class="navbar-burger burger" data-target="navMenu">
              <span></span>
              <span></span>
              <span></span>
            </span>
          </div>
          <div id="navMenu" class="navbar-menu">
            <div class="navbar-end">
              <span class="navbar-item">
                <a class="button is-white is-outlined">
                  <span class="icon">
                    <i class="fa fa-home"></i>
                  </span>
                  <span>Home</span>
                </a>
              </span>
                <?php
                if (isset($_SESSION['name'])){
                ?>
                <span class="navbar-item">
                <a class="button is-white is-outlined" href="myplaylist.php">
                  <span class="icon">
                    <i class="fa fa-headphones"></i>
                  </span>
                  <span>My Playlist</span>
                </a>
              </span>
                <span class="navbar-item">
                <a class="button is-white is-outlined" href="../backend/logout.php">
                  <span class="icon">
                    <i class="fa fa-sign-out"></i>
                  </span>
                  <span>Logout</span>
                </a>
              </span>
                <?php
                }elseif (!isset($_SESSION['name'])) { ?>
                <span class="navbar-item">
                <a class="button is-white is-outlined" data-target="#sign-in-modal" id="sign-in" aria-haspopup="true">
                  <span class="icon">
                    <i class="fa fa-user"></i>
                  </span>
                  <span>Sign In</span>
                </a>
              </span>
                    <?php
                } ;?>
                <?php
                if(isset($_SESSION['name'])) {
                    ?>
                    <span class="navbar-item">
                    <?php echo "<i class=\"fa fa-user\">&nbsp;&nbsp;</i>" . $_SESSION['name']; ?>
                </span>
                    <?php
                }else {}?>
            </div>
          </div>
        </div>
      </nav>
    </div>
      <div id="sign-in-modal" class="modal">
          <div class="modal-background"></div>
          <div class="modal-content">
              <div class="modal-card">
                  <form action="../backend/functions.php" method="post">
                      <header class="modal-card-head">
                          <p class="modal-card-title is-size-3">Sign In</p>
                      </header>
                      <section class="modal-card-body">
                          <div class="field">
                              <p class="control has-icons-left has-icons-right">
                                  <input class="input" name ="email" type="email" placeholder="Email">
                                  <span class="icon is-small is-left">
                              <i class="fa fa-envelope"></i>
                            </span>
                                  <span class="icon is-small is-right">
                              <i class="fa fa-check"></i>
                            </span>
                              </p>
                          </div>
                          <div class="field">
                              <p class="control has-icons-left">
                                  <input class="input" name="password" type="password" placeholder="Password">
                                  <span class="icon is-small is-left">
                              <i class="fa fa-lock"></i>
                                </span>
                              </p>
                          </div>
                      </section>
                      <footer class="modal-card-foot">
                          <button class="button is-success" name="submit" type="submit">Login</button>
                          <a class="button" name="cancel" id="cancel">Cancel</a>
                      </footer>
                  </form>
              </div>
          </div>
          <button id="image-modal-close" class="modal-close is-large"></button>
      </div>
    <div class="hero-body">
      <div class="container has-text-centered">
          <?php
          if (!isset($_SESSION['name'])){
          ?>
        <div class="column is-centered is-8 is-offset-2">
          <h1 class="title is-size-1">
            Welcome to MoodWaves.
          </h1>
        </div>
          <?php } else{}?>
          <div class="columns">
            <h2 class="column subtitle is-size-4">
            <i class="fa fa-search"> |</i>
                Browse existing playlists </h2>
              <h2 class="column subtitle is-size-4">
              <i class="fa fa-save"> |</i>
                  Save your favourite songs</h2>
              <h2 class="column subtitle is-size-4">
              <i class="fa fa-users"> |</i>
                  Share your moods </h2>
          </div>
          <br><br>
          <span>
            <h1 class="is-size-4 is-centered">Scroll Down to see the most recently shared playlist!</h1><br><br>
          </span>
          <div class="search column is-6 is-offset-3">
            <div class="box is-dark">
              <label class="search-label">Search a Song Title, Artist, Genre or Release Year</label>
              <form id="search" name="search" method="post">
                <div class="field is-grouped">
                  <div class="control is-expanded has-icons-left">
                    <input id="search-input" class="input" type="text" name="query" placeholder="e.g. 'James Bay'">
                    <span class="icon is-small is-left">
                      <i class="fa fa-search"></i>
                    </span>
                  </div>
                  <p class="control">
                    <button type="submit" name="search" class="button is-white is-outlined">
                        Search</button>
                    <button type="submit" name="search" class="button is-white is-outlined">
                            Reset</button>
                  </p>
                </div>
              </form>
            </div>
          </div>
            <div class="playlist-table">
                <?php
                if ($query) {
                    echo getPlaylist($query);
                } else {
                    echo getPlaylist('');
                }
                ?>
            </div>
      </div>
    </div>
  </section>
  <footer class="footer">
    <div class="content has-text-centered has-text-white">
      <p>
        <strong>MoodWaves</strong> by <a href="https://jwelsh.me" style="color: #404040">Jack Welsh</a>. The source code is licensed
        <a href="http://opensource.org/licenses/mit-license.php" style="color: #404040">MIT</a>, and can be found <a href="github.com/JackWelsh15/moodwaves" style="color: #404040">here.</a>
      </p>
    </div>
  </footer>
  <script type="text/javascript">
    (function() {
      var burger = document.querySelector('.burger');
      var nav = document.querySelector('#' + burger.dataset.target);
      burger.addEventListener('click', function() {
        burger.classList.toggle('is-active');
        nav.classList.toggle('is-active');
      });
    })();
  </script>
  <script>
    let btn = document.querySelector('#sign-in');
    let modalDlg = document.querySelector('#sign-in-modal');
    let imageModalCloseBtn = document.querySelector('#image-modal-close');
    let cancelBtn = document.querySelector('#cancel');
    let modalBackground = document.querySelector('.modal-background');
    console.log(modalBackground);
    btn.addEventListener('click', function() {
      modalDlg.classList.add('is-active');
    });

    imageModalCloseBtn.addEventListener('click', function() {
      modalDlg.classList.remove('is-active');
    });

    cancelBtn.addEventListener('click', function() {
      modalDlg.classList.remove('is-active');
    });

    modalBackground.addEventListener('click', function(){
        modalBackground.classList.remove('is-active');
    });
  </script>
</body>

</html>
