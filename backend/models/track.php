<?php
class Track
{
    public $id;
    public $artist;
    public $title;
    public $link;
    public $releaseYear;
    public $genre;

    public function __construct($id, $artist, $title, $link, $releaseYear, $genre)
    {
        $this->id = $id;
        $this->artist = $artist;
        $this->title = $title;
        $this->link = $link;
        $this->releaseYear = $releaseYear;
        $this->genre = $genre;
    }
}
