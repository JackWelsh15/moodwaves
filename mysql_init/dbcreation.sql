CREATE TABLE `members` (
 `memberID` mediumint(8) NOT NULL AUTO_INCREMENT,
 `email` varchar(255) NOT NULL,
 `name` varchar(255) NOT NULL,
 `password` varchar(255) NOT NULL,
 `lastlogin` datetime NOT NULL,
 PRIMARY KEY (`memberID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

CREATE TABLE `saved_songs` (
 `songID` varchar(255) NOT NULL,
 `memberID` mediumint(8) NOT NULL,
 `songtitle` varchar(255) NOT NULL,
 `artist` varchar(255) NOT NULL,
 `link` varchar(255) NOT NULL,
 `genre` varchar(255) NOT NULL,
 `releaseYear` varchar(8) NOT NULL,
 `dateSaved` datetime NOT NULL,
 PRIMARY KEY (`songID`,`memberID`),
 KEY `songs_fk_1` (`memberID`),
 CONSTRAINT `songs_fk_1` FOREIGN KEY (`memberID`) REFERENCES `members` (`memberID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
