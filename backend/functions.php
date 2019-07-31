<?php
require 'models/track.php';

//if the session has not been started, start a new one.
if (!isset($_SESSION)) {
    session_start();
}
if (isset($_POST["submit"])) {
    login();
}
if (isset($_POST["update"])) {
    storeSelected();
}

if(isset($_POST['remove'])) {
    removeSong();
}
if(isset($_POST['exportSavedSongs'])) {
    exportList();
}

/**
 * GetConnection
 *  - Establish a connection to a MySQL instance with PDO
 *
 * @return PDO
 * @author Jack Welsh
 */
function getConnection()
{
    $dsn = "mysql:host=localhost;dbname=unn_w18020302";
    $username = "unn_w18020302";
    $password = "QUEYQ0YY";

    try {
        // get connection
        $db = new PDO($dsn, $username, $password);
        
        //pass variables to PDO
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        //catch errors & display message
        $error_message = $e->getMessage();
        echo $error_message;
    }
    return $db;
}

/**
 * Login
 *  - Authenticates the user.
 *
 * 1. Check credentials have been set.
 * 2. Get a connection to the database.
 * 3. Query for user matching provided email address.
 * 4. Compare passwords.
 * 5. Handle comparison result.
 *   a) Authenticate and create a new session.
 *   b) Reject user's authentication attempt.
 *
 * @author Jack Welsh
 */
function login()
{
    session_start(); // Starting Session

    // Check if credentials have been given.
    if (!isset($_POST['email']) and !isset($_POST['password'])) {
        $error = "Email Address or Password is invalid";
        echo $error;
        return;
    } else {
        // Define $username and $password
        $email = $_POST['email'];
        $passwordAttempt = $_POST['password'];

        /*
         * Get a connection to the db,
         * create a query, and inject given email.
         */
        $db = getConnection();
        $stmt = $db->prepare('SELECT * FROM members WHERE email = :email');
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_BOTH);
        
        // User couldnt be found...
        if ($user === false) {
            // Error message, user does not exist
            die('User does not exist');
        } else {
            /*
             * User account found. Check to see if the given password matches the
             * password hash stored in the members table.
             */
            $correctPassword = password_verify($passwordAttempt, $user['password']);

            if ($correctPassword) {
                // Set session variables, Name, ID & Email.
                $_SESSION['name'] = $user['name'];
                $_SESSION['memberID'] = $user['memberID'];
                $_SESSION['email'] = $user['email'];
                $memberID = $_SESSION['memberID'];

                // Update lastlogin to current date & time, using the session variable as primary key.
                $sql = "UPDATE `members` SET lastlogin=NOW() WHERE memberID = '$memberID'";

                // Prepare  statement.
                $statement = $db->prepare($sql);
                $statement->bindParam('memberID', $memberID);
                $statement->execute();

                // Create new session variable for last login date
                $_SESSION['lastlogin'] = $user['lastlogin'];

                // Redirect to myplaylist.php if successful.
                header('Location:../myplaylist.php');
            } else {
                // Passwords do not match.
                die('Incorrect username / password combination!');
            }
        }
    }
}

/**
 * GetPlaylist
 *  - Get and display a playlist from an XML file,
 *  - can also return a subset of the playlist where
 *  - songs contain a given query string.
 *
 * 1. Check for query, redirect to search if exists.
 * 2. Load XML.
 * 3. Display the table.
 *
 * @param query
 * @return playlist
 * @author Jack Welsh
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
 * SearchPlaylist
 *  - Use XML path to check if any song elements contain the
 *  - given query string.
 *
 * 1. Format query string to increase matches.
 * 2. Create XPath query.
 * 3. Display results.
 *
 * @param query
 * @author Jack Welsh
 */
function searchPlaylist($query)
{
    $query = ucfirst($query);
    $template = "//%s[contains(text(),'%s')]/parent::item";
    $field = "item/*";
    $q = sprintf($template, $field, $query);

    $playlist = simplexml_load_file('./xml/playlist.xml');
    $search = $playlist->xpath($q);

    // Generate results as a table.
    generateTable($search);
}

/**
 * GenerateTable
 * Display an array of tracks as a HTML table.
 *
 * 1. Iterate through each track and create a table row.
 * 2. Inject the track details into the table row.
 * 3. Update buttons on authentication status.
 *
 * @param $tracks
 */
function generateTable($tracks)
{
    // Set as a form so user can submit saved songs.
    echo "<form action='../backend/functions.php' method='post'>";

    // Create table and headers.
    echo "<table class='table is-fullwidth is-rounded is-striped is-hoverable is-dark'>";
    echo "<thead>";
    echo "<th>Title</th><th>Artist</th><th>Genre</th><th class='has-text-right'>Release Year</th><th class='has-text-centered'>Save</th><th class='has-text-centered'>Buy</th>\n";
    echo "</thead>";

    // iterate through tacks.
    foreach ($tracks as $track) {
        // embed the track id as a data attribute for saving songs.
        echo "<tr data-song-id='{$track->songid}'>";

        // print details.
        $titleString = "<td class='is 5%'>{$track->songtitle}</td>";
        $artistString = "<td class='is 5%'>{$track->artist}</td>";
        $genreString = "<td class='is 5%'>{$track->genre}</td>";
        $releaseString = "<td class='is 5% has-text-right'>{$track->releaseyear}</td>";
        $save = "<td class='has-text-centered save-song-checkbox'><input name='songid[]' value='{$track->songid}' type='checkbox'></td>";
        $saveDisabled = "<td class='has-text-centered save-song-checkbox'><input name='songid[]' value='{$track->songid}' type='checkbox' disabled></td>";
        $buy = "<td class='is 5% has-text-centered'><a class='button is-warning' href='{$track->link}' target='_blank'><span class='icon'><i class='fa fa-amazon'></i></span>&nbsp;&nbsp;Buy</a></td>";
        
        // disable save option if user is not logged in.
        if (isset($_SESSION['memberID'])) {
            echo "" . $titleString . $artistString . $genreString . $releaseString . $save . $buy;
        } else {
            echo "" . $titleString . $artistString . $genreString . $releaseString . $saveDisabled . $buy;
        }
        echo "</tr>\n";
    }
    echo "</table>";

    // Enable save button if the user is logged in.
    if (isset($_SESSION['memberID'])) {
        echo "<button class='button is-primary is-medium is-clearfix' name='update' type='submit'>Save Selected Songs</button>";
    } else {
        echo "<article class=\"message is-warning\"><div class=\"message-body\">Sign in to save songs!</div></article>";
    }
    echo "</form>";
}

/**
 * Store Selected
 *  - Saves selected songs to the users playlist.
 *
 * 1. Load in the XML song store.
 * 2. Load songs from XML with matching ids with XPath
 * 3. Create an array of track objects.
 * 4. Format tracks into an array of SQL insertion queries.
 */
function storeSelected()
{
    $ids = $_POST['songid'];

    // Load XML file.
    $playlist = simplexml_load_file('../xml/playlist.xml');
    $tracks = [];

    // Iterate through arrays...
    foreach ($ids as $id) {
        /*
         * Use XPath to find a node that matches the track id.
         * Return the parent of the id node (the full track) with
         * `/parent::item`, to get the rest of the track information.
         *
         * 1. Create a query template.
         * 2. Insert the id into the query template.
         * 3. Find the record.
         * 4. Push to array of Track objects.
         */

        // 1
        $template = "//item/songid[contains(text(),'%s')]/parent::item";
        // 2
        $q = sprintf($template, $id);
        // 3
        $search = $playlist->xpath($q);
        // 4
        array_push($tracks, new Track(''. $search[0]->songid, '' . $search[0]->artist, ''. $search[0]->songtitle, ''. $search[0]->link, ''. $search[0]->releaseyear, '' .  $search[0]->genre));
    }

    // Create an array of SQL insertion rows.
    $rows = [];
    // iterate through each track, and turn into mapped array.
    foreach ($tracks as $track) {
        array_push($rows, [
            'songID' => $track->id,
            'artist' => $track->artist,
            'songtitle' => $track->title,
            'link' => $track->link,
            'releaseYear' => $track->releaseYear,
            'genre' => $track->genre,
            'memberID' => $_SESSION['memberID']
        ]);
    }

    /*
     * Get a database connection and insert
     * the record into the saved songs table.
     *
     */
    $db = getConnection();
    $sql = "INSERT INTO saved_songs SET songID=:songID, artist=:artist, songtitle=:songtitle, link=:link, releaseYear=:releaseYear, genre=:genre, memberID=:memberID, dateSaved=NOW();";
    $stmt = $db->prepare($sql);
    foreach ($rows as $row) {
        $stmt->execute($row);
    }

    // Redirect to the myplaylist page and exit.
    header('Location: ../myplaylist.php', false);
    exit();
}

/**
 * GetSavedSongs
 *  - Load the users saved songs and display as a table.
 *
 * 1. Create a connection to the database.
 * 2. Query for songs.
 * 3. Create HTML table.
 * 4. Display each song.
 */
function getSavedSongs()
{
    try {
        // Get the users id and query for saved songs refering to the user.
        $memberID = $_SESSION['memberID'];
        $db = getConnection();
        $getSavedSongs= $db->prepare("SELECT * FROM saved_songs WHERE memberID = '$memberID'");
        $getSavedSongs->execute();
        $savedSongs = $getSavedSongs->fetchAll();

        // Create the table.
        echo "<form action='../backend/functions.php' method='post'>";
        echo "<table class='table is-centered is-fullwidth is-rounded is-hoverable is-size-6 is-dark'>";
        echo "<thead>";
        echo "<th>Title</th><th>Artist</th><th>Genre</th><th class='has-text-right'>Release Year</th><th class='has-text-centered'>Remove</th><th class='has-text-centered'>Buy</th>\n";
        echo "</thead>";

        /*
         * Iterate through each song and display it in a HTML table.
         *
         * Each song is represented as a table row,
         * with an option to remove the selected song.
         */
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
        echo "<button class='button is-light is-outlined is-pulled-right' name='exportSavedSongs' type='submit'>Export Playlist</button>";
        echo "</form>";
    } catch (PDOException $e) {
        die($e->getMessage());
    }

}

/**
 * Function to remove multiple selected songs
 * Author: Jack Welsh
 *
 */
function removeSong()
{

    $db = getConnection();
    $sess = $_SESSION['memberID']; //Pass sessions variable
    $delete = implode(",", $_POST['songRemove']); //Change array to string via php implode()
    if($delete==''){
        echo "<script>alert('Select song to remove')</script>";
        header('Location: ../myplaylist.php', false);


    } else {
        $sql = "DELETE FROM saved_songs WHERE songID='$delete' AND memberID='$sess'";
        $db->exec($sql);
        echo "<script>alert('Successfully Deleted')</script>";
    }
    header('Location: ../myplaylist.php', false);
}

/**
 * ExportList
 * - Export a list of songs into XML file.
 * - File stored on local server
 * - Redirect header to file on server.
 *
 */
function exportList()
{
    $db = getConnection();
    $memberID = $_SESSION['memberID'];
    $songsSQL = "select * from saved_songs WHERE memberID = '$memberID'";
    $stmt = $db->query($songsSQL);
    
    $elementName = "savedSongs";    // create a variable for each element name
    $fileName = "$elementName.xml";
    // open the file for writing
    $filePointer = fopen($fileName, "w");
    // write to the file
    fwrite($filePointer, "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n");
    fwrite($filePointer, "<{$elementName}>\n");  // add an 's' for the root element
    // fetch each record as an associative array
    while ($savedSong = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fwrite($filePointer, "\t<$elementName>\n");  // element name as a tag
        // iterate through each field in the associative array,
        foreach ($savedSong as $key => $value) {
            // write the key as a tag enclosing its value
            fwrite($filePointer, "\t\t<$key>$value</$key>\n");
        }
        fwrite($filePointer, "\t</$elementName>\n");
    }
    fwrite($filePointer, "</{$elementName}>\n");
    fclose($filePointer);

    header('Location: savedSongs.xml', true);
    exit();

}
