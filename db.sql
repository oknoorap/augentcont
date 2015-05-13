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
  `id` varchar(255) CHARACTER SET latin1 NOT NULL,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `icon` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT 'ellipsis-h',
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

INSERT INTO `cat` (`id`, `name`, `icon`, `time`) VALUES
('VoXl0m3N1q', 'Others', 'ellipsis-h', 1415098244);

--
-- Table structure for table `index`
--

CREATE TABLE IF NOT EXISTS `index` (
  `id` varchar(255) CHARACTER SET latin1 NOT NULL,
  `keyword_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `url` text COLLATE utf8_unicode_ci NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `keywords`
--

CREATE TABLE IF NOT EXISTS `keywords` (
  `id` varchar(255) CHARACTER SET latin1 NOT NULL,
  `keyword` text COLLATE utf8_unicode_ci NOT NULL,
  `cat_id` varchar(255) CHARACTER SET latin1 NOT NULL,
  `count` bigint(20) unsigned NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
