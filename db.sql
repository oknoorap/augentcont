SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `nn`
--

-- --------------------------------------------------------

--
-- Table structure for table `cat`
--

CREATE TABLE IF NOT EXISTS `cat` (
  `id` varchar(255) NOT NULL,
  `name` text NOT NULL,
  `icon` varchar(50) NOT NULL DEFAULT 'ellipsis-h',
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `index`
--

CREATE TABLE IF NOT EXISTS `index` (
  `id` varchar(255) NOT NULL,
  `keyword_id` varchar(255) DEFAULT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `url` text NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `keywords`
--

CREATE TABLE IF NOT EXISTS `keywords` (
  `id` varchar(255) NOT NULL,
  `keyword` text NOT NULL,
  `cat_id` varchar(255) NOT NULL,
  `count` bigint(20) unsigned NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
