-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               10.1.31-MariaDB - mariadb.org binary distribution
-- Операционная система:         Win32
-- HeidiSQL Версия:              9.5.0.5249
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Дамп структуры базы данных rtymtsiv
CREATE DATABASE IF NOT EXISTS `rtymtsiv` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `rtymtsiv`;

-- Дамп структуры для таблица rtymtsiv.admins
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(255) CHARACTER SET latin1 NOT NULL,
  `password` varchar(255) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы rtymtsiv.admins: ~5 rows (приблизительно)
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
REPLACE INTO `admins` (`id`, `login`, `password`) VALUES
	(1, 'admin', 'admin'),
	(3, 'admin1', '1'),
	(16, 'admin2', 'admin'),
	(17, 'admin3', 'admin'),
	(18, 'admin4', '1');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;

-- Дамп структуры для таблица rtymtsiv.answers
CREATE TABLE IF NOT EXISTS `answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `answer` text NOT NULL,
  `admin_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы rtymtsiv.answers: ~8 rows (приблизительно)
/*!40000 ALTER TABLE `answers` DISABLE KEYS */;
REPLACE INTO `answers` (`id`, `question_id`, `answer`, `admin_id`) VALUES
	(1, 6, 'Ответ 1', 1),
	(2, 5, 'Ответ 3', 1),
	(3, 6, 'Ответ 2', 1),
	(4, 6, 'Ответ 1222222222222222', 1),
	(5, 14, '333333333333', 1),
	(6, 5, 'ну и всё', 1),
	(7, 16, 'ответ1', 1),
	(8, 2, 'sdzfbxgchvj', 1),
	(9, 24, '44444444444444', 1),
	(10, 26, '55555555555555', 1);
/*!40000 ALTER TABLE `answers` ENABLE KEYS */;

-- Дамп структуры для таблица rtymtsiv.categories
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы rtymtsiv.categories: ~4 rows (приблизительно)
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
REPLACE INTO `categories` (`id`, `name`) VALUES
	(1, 'Basics'),
	(2, 'Mobile'),
	(3, 'Account'),
	(8, '111'),
	(10, '222');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;

-- Дамп структуры для таблица rtymtsiv.questions
CREATE TABLE IF NOT EXISTS `questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `topic` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `admin_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `hidden` tinyint(1) NOT NULL,
  `date` date NOT NULL,
  `author` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы rtymtsiv.questions: ~5 rows (приблизительно)
/*!40000 ALTER TABLE `questions` DISABLE KEYS */;
REPLACE INTO `questions` (`id`, `topic`, `category_id`, `question`, `admin_id`, `email`, `hidden`, `date`, `author`) VALUES
	(20, '1', 1, '1', 0, 'tymtsiv.roman@gmail.com', 1, '2018-07-29', 'Роман Тымцив'),
	(22, '2', 2, '2', 0, 'tymtsiv.roman@gmail.com', 0, '2018-07-29', 'Роман Тымцив'),
	(23, '3', 3, '3', 0, 'tymtsiv.roman@gmail.com', 0, '2018-07-29', 'Роман Тымцив'),
	(24, '4', 8, '4', 0, 'tymtsiv.roman@gmail.com', 0, '2018-07-29', 'Роман Тымцив'),
	(26, '5', 10, '55', 0, 'tymtsiv.roman@gmail.com', 0, '2018-07-29', 'Роман Тымцив');
/*!40000 ALTER TABLE `questions` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
