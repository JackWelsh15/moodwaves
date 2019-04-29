<?php

if(!isset($_SESSION)) //if the session has not been started, start a new one.
{
    session_start();
}

require 'models/track.php';
if (isset($_POST['submit'])) {
    login();
}
if (isset($_POST['update'])) {
    storeSelected();
}

/**
 * this function establishes a database connection using PDO.
 * @return PDO
 * @Author: Jack Welsh
 */
function getConnection()
{
//     production connection
     $dsn = "mysql:host=localhost;dbname=unn_w18020302";
     $username = "unn_w18020302";
     $password = "QUEYQ0YY";

//    // local connection
//    $dsn = "mysql:host=172.18.0.2;dbname=moodwaves;port=3306";
//    $username = "root";
//    $password = "secret";

    try {
        $db = new PDO($dsn, $username, $password); //pass variables to PDO
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) { //catch errors & display message
        $error_message = $e->getMessage();
        echo $error_message;
    }
    return $db;
}

/**
 * This function verifies user login against hashed password in database.
 * This function also sets session variables and updates 'lastlogin' field on db.
 * @Author Jack Welsh
 */
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
        $passwordAttempt = $_POST['password'];
        // getConnection() function opens a new connection to the MySQL server.
        $db = getConnection();
        $stmt = $db->prepare('SELECT * FROM members WHERE email = :email');
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_BOTH);

        if($user === false){
            //Error message, user does not exist
            die('User does not exist');
        } else{
            //User account found. Check to see if the given password matches the
            //password hash stored in the members table.

            //Compare the passwords.
            $correctPassword = password_verify($passwordAttempt, $user['password']);

            if ($correctPassword){
                //set session variables, Name & ID.
                $_SESSION['name'] = $user['name'];
                $_SESSION['memberID'] = $user['memberID'];
                $_SESSION['email'] = $user['email'];

                $memberID = $_SESSION['memberID'];
                //Update lastlogin to current date & time, using the session variable as primary key.
                $sql = "UPDATE `members` SET lastlogin=NOW() WHERE memberID = '$memberID'";
                //Prepare  statement.
                $statement = $db->prepare($sql);
                $statement->bindParam('memberID', $memberID);
                $statement->execute();

                $_SESSION['lastlogin'] = $user['lastlogin'];

                header('Location:../public/myplaylist.php'); //redirect to myplaylist.php if successful.
            } else{
                //$correctPassword was FALSE. Passwords do not match.
                die('Incorrect username / password combination!');
            }
        } //end second else statement
    } //end first else statement
} //end login()

/**
 * This function gets playlist from the xml file.
 * @return $playlist
 * @Author Jack Welsh
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

/**
 * @param $query
 */
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

/**
 * @param $tracks
 */
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
      $save = "<td class='has-text-centered save-song-checkbox'><input name='songid[]' value='{$track->songid}' type='checkbox'></td>";
      $buy = "<td class='is 5% has-text-centered'><a class='button is-warning' href='{$track->link}' target='_blank'><span class='icon'><i class='fa fa-amazon'></i></span>&nbsp;&nbsp;Buy</a></td>";

      echo "" . $titleString . $artistString . $genreString . $releaseString . $save . $buy;
      echo "</tr>\n";
  }
    echo "</table>";
    echo "<button class='button is-primary' name='update' type='submit'>Save Selected Songs</button>";
    echo "</form>";
}

if (isset($_POST['update'])) {
    storeSelected();
}

/**
 *
 */
function storeSelected()
{
    $ids = $_POST['songid'];
    $playlist = simplexml_load_file('../public/xml/playlist.xml');
    $tracks = [];

    foreach ($ids as $id) {
        $template = "//item/songid[contains(text(),'%s')]/parent::item";
        $q = sprintf($template, $id);
        $search = $playlist->xpath($q);
        // $search = $search[0][0];
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
//            'memberID' => 1 // todo update from session.
            'memberID' => $_SESSION['memberID']
        ]);
    }

    // check not already saved first
    $db = getConnection();
    $sql = "INSERT INTO saved_songs SET songID=:songID, artist=:artist, songtitle=:songtitle, link=:link, releaseYear=:releaseYear, genre=:genre, memberID=:memberID, dateSaved=NOW();";
    $stmt = $db->prepare($sql);
    foreach ($rows as $row) {
        $stmt->execute($row);
    }

    header('Location: ../public/myplaylist.php', false);
    exit();
}

/**
 *
 */
function getSavedSongs(){

    try {
   $memberID = $_SESSION['memberID'];
   $db = getConnection();
        $getSavedSongs= $db->prepare("SELECT * FROM saved_songs WHERE memberID = '$memberID'");
        $getSavedSongs->execute();
        $savedSongs = $getSavedSongs->fetchAll();

            echo "<form action='../backend/functions.php' method='post'>";
            echo "<table class='table is-centered is-fullwidth is-rounded is-hoverable is-size-6 is-dark'>";
            echo "<thead>";
            echo "<th>Title</th><th>Artist</th><th>Genre</th><th class='has-text-right'>Release Year</th><th class='has-text-centered'>Remove</th><th class='has-text-centered'>Buy</th>\n";
            echo "</thead>";
// iterate through the grade nodes displaying the contents
            foreach ($savedSongs as $savedSong) {
                echo "<tr data-song-id='{$savedSong['songID']}'>";
                $titleString = "<td class='is 5%'>{$savedSong['songtitle']}</td>";
                $artistString = "<td class='is 5%'>{$savedSong['artist']}</td>";
                $genreString = "<td class='is 5%'>{$savedSong['genre']}</td>";
                $releaseString = "<td class='is 5% has-text-right'>{$savedSong['releaseYear']}</td>";
                $remove = "<td class='has-text-centered save-song-checkbox'><input name='songRemove[]' value='{$savedSong['songID']}' type='checkbox'></td>";
                $buy = "<td class='is 5% has-text-centered'><a class='button is-warning' href='{$savedSong['link']}' target='_blank'><span class='icon'><i class='fa fa-amazon'></i></span>&nbsp;&nbsp;Buy</a></td>";

                echo "" . $titleString . $artistString . $genreString . $releaseString . $remove . $buy;
                echo "</tr>\n";
            }
            echo "</table>";
            echo "<button class='button is-danger' name='remove' type='submit'>Remove Selected Songs</button>";
            echo "<button class='button is-info' name='export' type='submit'>Export Playlist</button>";
            echo "</form>";

    } catch (PDOException $e) {
        die($e->getMessage());
    }

    if(isset($_POST['remove']));
    removeSong();
    if(isset($_POST['export']));
    exportList();

}

/**
 *
 */
function removeSong()
{
if(isset($_POST['remove'])) {
    $db = getConnection();
    $del_song = $_POST['songRemove'];
    $M = count($del_song);
    $sess = $_SESSION['memberID'];
    for ($j = 0; $j < $M; $j++)
        echo "hello";
    {
        $results = $db->prepare("DELETE FROM saved_songs WHERE songID=songID AND memberID='$sess'");
        $results->bindParam(':songID', $del_song[$j]);
        $results->bindParam(':memberID', $sess);
        if ($results->execute())
            echo "removed";
        {
            header('Location: ../public/myplaylist.php', true);
            exit();
        }
    }
}
}

/**
 *
 */
function exportList(){
    $db = getConnection();
    $songsSQL = 'select * from saved_songs';
    $stmt = $db->query($songsSQL);
    $elementName = "savedSongs";    // create a variable for each element name
    $fileName = "$elementName.xml";
// open the file for writing
    $filePointer = fopen($fileName, "w");
// write to the file
    fwrite( $filePointer, "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n" );
    fwrite( $filePointer, "<{$elementName}s>\n" );  // add an 's' for the root element
// fetch each record as an associative array
    while ($savedSong = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fwrite( $filePointer, "\t<$elementName>\n" );  // element name as a tag
        // iterate through each field in the associative array,
        //   see php help on foreach
        foreach ( $savedSong as $key => $value ) {
            // write the key as a tag enclosing its value
            fwrite( $filePointer, "\t\t<$key>$value</$key>\n" );
        }
        fwrite( $filePointer, "\t</$elementName>\n" );
    }
    fwrite( $filePointer, "</{$elementName}s>\n" );
    fclose( $filePointer );
}

