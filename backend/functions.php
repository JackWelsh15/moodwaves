<?php
require 'models/track.php';
if (isset($_POST['submit'])) {
    login();
}
if (isset($_POST['update'])) {
    storeSelected();
}
// Author: Jack Welsh
//
// Function to get database connection.

function getConnection()
{
    // production
    // $dsn = "mysql:host=localhost;dbname=unn_w18020302";
    // $username = "unn_w18020302";
    // $password = "QUEYQ0YY";

    // local
    $dsn = "mysql:host=172.18.0.2;dbname=moodwaves;port=3306";
    $username = "root";
    $password = "secret";

    try {
        $db = new PDO($dsn, $username, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        $error_message = $e->getMessage();
        echo $error_message;
    }
    return $db;
}

function login()
{
    session_start(); // Starting Session

    if (!isset($_POST['email']) and !isset($_POST['password'])) {
        $error = "Email Address or Password is invalid";
        echo $error;
        return;
    } else {
        // Define $username and $password
        $email = $_POST['email'];
        $password = $_POST['password'];
        // getConnection() function opens a new connection to the MySQL server.
        $db = getConnection();
        $stmt = $db->prepare('SELECT * FROM members WHERE email = ? AND password=?');
        $stmt->execute(array($email, $password));
        $user = $stmt->fetch(PDO::FETCH_BOTH);

        if (!$user) {
            echo "Incorrect username";
            return;
        }

        if($stmt->rowCount() > 0){
            $_SESSION['name'] = $user['name'];
            $_SESSION['memberID'] = $user['memberID'];
            header('Location:../public/myplaylist.php');
        } else {
            echo "Email or Password Invalid";
        }

//        // password verification
//        if ($user['password'] == 'password') {
//        } elseif (password_verify($password, $user['password'])) {
//            $_SESSION['email'] = $user->email;
//            $_SESSION['memberID'] = $user->memberID;
//        } else {
//            echo "incorrect";
//            return;
//        }
//
//        header('Location: ../public/myplaylist.php');
    }
}

/**
 * @return $playlist
 */
function getPlaylist($query)
{
    if ($query != '') {
        searchPlaylist($query);
        return;
    }
    $playlist = simplexml_load_file('./xml/playlist.xml');
    generateTable($playlist);
    return $playlist;
}

function searchPlaylist($query)
{
    $query = ucfirst($query);
    $template = "//%s[contains(text(),'%s')]/parent::item";
    $field = "item/*";
    $q = sprintf($template, $field, $query);

    $playlist = simplexml_load_file('./xml/playlist.xml');
    $search = $playlist->xpath($q);

//     print_r($search);
    generateTable($search);
}

function generateTable($tracks)
{
    echo "<form action='../backend/functions.php' method='post'>";
    echo "<table class='table is-centered is-fullwidth is-rounded is-hoverable is-size-6 is-dark'>";
    echo "<thead>";
    echo "<th>Title</th><th>Artist</th><th>Genre</th><th class='has-text-right'>Release Year</th><th class='has-text-centered'>Save</th><th class='has-text-centered'>Buy</th>\n";
    echo "</thead>";
// iterate through the grade nodes displaying the contents
  foreach ($tracks as $track) {
      echo "<tr data-song-id='{$track->songid}'>";
      $titleString = "<td class='is 5%'>{$track->songtitle}</td>";
      $artistString = "<td class='is 5%'>{$track->artist}</td>";
      $genreString = "<td class='is 5%'>{$track->genre}</td>";
      $releaseString = "<td class='is 5% has-text-right'>{$track->releaseyear}</td>";
      $save = "<td class='has-text-centered save-song-checkbox'><input name='songid[]' id='songid' value='{$track->songid}' type='checkbox'></td>";
      $buy = "<td class='is 5% has-text-centered'><a class='button is-warning' href='{$track->link}' target='_blank'><span class='icon'><i class='fa fa-amazon'></i></span>&nbsp;&nbsp;Buy</a></td>";

      echo "" . $titleString . $artistString . $genreString . $releaseString . $save . $buy;
      echo "</tr>\n";
  }
    echo "</table>";
    echo "<button class='button is-primary' name='update' type='submit'>Save Selected Songs</button>";
    echo "</form>";
}

function storeSelected()
{
    if (!isset($_SESSION['memberID'])) {
        return;
    }
    $ids = $_POST['songid[]'];
    $playlist = simplexml_load_file('../public/xml/playlist.xml');
    $tracks = [];

    foreach ($ids as $id) {
        $template = "//item/songid[contains(text(),'%s')]/parent::item";
        $q = sprintf($template, $id);
        $search = $playlist->xpath($q);
        array_push($tracks, new Track(''. $search[0]->songid, '' . $search[0]->artist, ''. $search[0]->songtitle, ''. $search[0]->link, ''. $search[0]->releaseyear, '' .  $search[0]->genre));
    }

    $rows = [];
    foreach ($tracks as $track) {
        array_push($rows, [
        'songID' => $track->id,
        'artist' => $track->artist,
        'songtitle' => $track->title,
        'link' => $track->link,
        'releaseYear' => $track->releaseYear,
        'genre' => $track->genre,
        'memberID' => $_SESSION['memberID'] // todo update from session.
      ]);
    }

    // check not already saved first
    $db = getConnection();
    $sql = "INSERT INTO saved_songs SET songID=:songID, artist=:artist, songtitle=:songtitle, link=:link, releaseYear=:releaseYear, genre=:genre, memberID=:memberID, dateSaved=NOW();";
    $stmt = $db->prepare($sql);
    foreach ($rows as $row) {
        $stmt->execute($row);
    }

    header('Location: ../public/myplaylist.php');
    exit();
}
