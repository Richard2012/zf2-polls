SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: 'polls'
--

-- --------------------------------------------------------

--
-- Table structure for table 'answers'
--

CREATE TABLE IF NOT EXISTS answers (
  id int(11) NOT NULL AUTO_INCREMENT,
  poll_id int(11) NOT NULL,
  `order` tinyint(4) NOT NULL DEFAULT '0',
  answer varchar(255) NOT NULL,
  votes int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Table structure for table 'iplong'
--

CREATE TABLE IF NOT EXISTS iplong (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  poll_id smallint(5) unsigned NOT NULL,
  iplong int(10) unsigned NOT NULL,
  extrainfo varchar(124) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (id),
  KEY iplong (iplong),
  KEY iplong_2 (iplong)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table 'polls'
--

CREATE TABLE IF NOT EXISTS polls (
  id int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  question varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  server_name varchar(255) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
