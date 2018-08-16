SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fake-ai`
--

-- --------------------------------------------------------

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `no_research` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `questions` (`id`, `question`, `no_research`) VALUES
(1, 'Dance', 1),
(2, 'Fix me', 1),
(3, 'Hello', 1),
(4, 'Search on Google', 1),
(5, 'What time is it?', 1),
(6, 'What day is today?', 1);

CREATE TABLE `replies` (
  `id` int(11) NOT NULL,
  `question` int(11) NOT NULL,
  `reply` varchar(255) NOT NULL,
  `action` int(11) DEFAULT NULL,
  `vote` int(11) NOT NULL DEFAULT '3' COMMENT 'min: 1 / max: 5'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `replies` (`id`, `question`, `reply`, `action`, `vote`) VALUES
(1, 1, '', 2, 5),
(2, 2, '', 4, 5),
(3, 3, 'Hi!', 1, 5),
(4, 4, '', 6, 5),
(5, 5, '', 3, 5),
(6, 6, '', 5, 5);

ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `replies`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
