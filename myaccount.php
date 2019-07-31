<?php
session_start();
include('backend/functions.php');
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MoodWaves</title>
    <link rel="shortcut icon" href="../images/fav_icon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css?family=Raleway:500,600,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Rubik:400,500,700" rel="stylesheet">
    <!-- Bulma Version 0.7.4-->
    <link rel="stylesheet" href="https://unpkg.com/bulma@0.7.4/css/bulma.min.css"/>
    <link rel="stylesheet" type="text/css " href="./css/style.css">

</head>
<body>
<section class="hero is-dark is-fullheight">
    <div class="hero-head">
        <nav class="navbar is-transparent">
            <div class="container">
                <div class="navbar-brand">
                    <a class="navbar-item" href="index.php">
                        <h1 class="is-size-3"><i class="fa fa-headphones"> | </i><span class="has-text-weight-bold">&nbsp;MoodWaves</span>
                        </h1>
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
                <a class="button is-white is-outlined" href="index.php">
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
                <a class="button is-white is-outlined is-active" href="myaccount.php">
                  <span class="icon">
                    <i class="fa fa-user"></i>
                  </span>
                    <span><?php echo $_SESSION['name'] ?></span>
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
                        <?php }elseif (!isset($_SESSION['name'])) { ?>
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
                                <input class="input" name="email" type="email" placeholder="Email">
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
                <h1 class="title is-size-2">Account Details</h1>
                <div class="level is-size-4 is-4">
                    <div class="level-item">
                         <div class="box has-text-white">
                            <?php if(isset($_SESSION)); {?>
                                <h2><strong>Name:</strong> <?php echo $_SESSION['name'];?></h2>
                                <h2><strong>Email: </strong><?php echo $_SESSION['email'];?></h2>
                                <h2><strong>Last Login: </strong><?php echo $_SESSION['lastlogin'];?></h2>
                            <?php }?>
                        </div>
                    </div>
                 </div>
            </div>
        </div>
<footer class="footer">
    <div class="content has-text-centered has-text-white">
        <p>
            <strong>MoodWaves</strong> by <a href="https://jwelsh.me" style="color: #404040">Jack Welsh</a>. The source
            code is licensed
            <a href="http://opensource.org/licenses/mit-license.php" style="color: #404040">MIT</a>, and can be found <a
                href="github.com/JackWelsh15/moodwaves" style="color: #404040">here.</a>
        </p>
    </div>
</footer>

<script type="text/javascript">
    (function () {
        let burger = document.querySelector('.burger');
        let nav = document.querySelector('#' + burger.dataset.target);
        burger.addEventListener('click', function () {
            burger.classList.toggle('is-active');
            nav.classList.toggle('is-active');
        });
    })();
</script>
<?php if(!isset($_SESSION['memberID'])){?>
    <script>
        let btn = document.querySelector('#sign-in');
        let modalDlg = document.querySelector('#sign-in-modal');
        let imageModalCloseBtn = document.querySelector('#image-modal-close');
        let cancelBtn = document.querySelector('#cancel');
        btn.addEventListener('click', function () {
            modalDlg.classList.add('is-active');
        });

        imageModalCloseBtn.addEventListener('click', function () {
            modalDlg.classList.remove('is-active');
        });

        cancelBtn.addEventListener('click', function () {
            modalDlg.classList.remove('is-active');
        });
    </script>
<?php  } else{}; ?>
</body>

</html>
