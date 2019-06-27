-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Июн 27 2019 г., 18:43
-- Версия сервера: 10.1.34-MariaDB
-- Версия PHP: 7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `phpnewsletter6`
--

-- --------------------------------------------------------

--
-- Структура таблицы `attach`
--

CREATE TABLE `attach` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `templateId` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `category`
--

INSERT INTO `category` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Категория 1', NULL, '2019-06-25 08:45:21'),
(2, 'Категория 3', NULL, '2019-06-10 15:17:09');

-- --------------------------------------------------------

--
-- Структура таблицы `charset`
--

CREATE TABLE `charset` (
  `id` int(5) NOT NULL,
  `charset` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `charset`
--

INSERT INTO `charset` (`id`, `charset`) VALUES
(1, 'utf-8'),
(2, 'iso-8859-1'),
(3, 'iso-8859-2'),
(4, 'iso-8859-3'),
(5, 'iso-8859-4'),
(6, 'iso-8859-5'),
(7, 'iso-8859-6'),
(8, 'iso-8859-8'),
(9, 'iso-8859-7'),
(10, 'iso-8859-9'),
(11, 'iso-8859-10'),
(12, 'iso-8859-13'),
(13, 'iso-8859-14'),
(14, 'iso-8859-15'),
(15, 'iso-8859-16'),
(16, 'windows-1250'),
(17, 'windows-1251'),
(18, 'windows-1252'),
(19, 'windows-1253'),
(20, 'windows-1254'),
(21, 'windows-1255'),
(22, 'windows-1256'),
(23, 'windows-1257'),
(24, 'windows-1258'),
(25, 'gb2312'),
(26, 'big5'),
(27, 'iso-2022-jp'),
(28, 'ks_c_5601-1987'),
(29, 'euc-kr'),
(30, 'windows-874'),
(31, 'koi8-r'),
(32, 'koi8-u');

-- --------------------------------------------------------

--
-- Структура таблицы `customheaders`
--

CREATE TABLE `customheaders` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `log`
--

CREATE TABLE `log` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `template` varchar(255) NOT NULL,
  `sendStatusId` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `settings`
--

INSERT INTO `settings` (`id`, `name`, `value`) VALUES
(1, 'EMAIL', 'info@trust-signal.com'),
(2, 'SHOW_EMAIL', 'on'),
(3, 'FROM', 'wwwwwwww'),
(4, 'RETURN_PATH', ''),
(5, 'LIST_OWNER', ''),
(6, 'ORGANIZATION', ''),
(7, 'SUBJECT_TEXT_CONFIRM', 'Подписка на рассылку'),
(8, 'TEXT_CONFIRMATION', 'Здравствуйте, %NAME%\r\n\r\nПолучение рассылки возможно после завершения этапа активации подписки. Чтобы активировать подписку, перейдите по следующей ссылке: %CONFIRM%\r\n\r\nЕсли Вы не производили подписку на данный email, просто проигнорируйте это письмо или перейдите по ссылке: %UNSUB%\r\n\r\nС уважением, \r\nадминистратор сайта %SERVER_NAME%'),
(9, 'REQUIRE_SUB_CONFIRMATION', 'on'),
(10, 'UNSUBLINK', 'Отписаться от рассылки: <a href=%UNSUB%>%UNSUB%</a>'),
(11, 'SHOW_UNSUBSCRIBE_LINK', 'on'),
(12, 'REQUEST_REPLY', 'on'),
(15, 'NEW_SUBSCRIBER_NOTIFY', 'on'),
(16, 'SLEEP', '0'),
(17, 'LIMIT_NUMBER', '300'),
(18, 'LIMIT_SEND', 'on'),
(19, 'DAYS_FOR_REMOVE_SUBSCRIBER', '7'),
(20, 'REMOVE_SUBSCRIBER', 'on'),
(21, 'RANDOM_SEND', 'on'),
(22, 'RENDOM_REPLACEMENT_SUBJECT', 'on'),
(23, 'RANDOM_REPLACEMENT_BODY', 'on'),
(24, 'PRECEDENCE', 'bulk'),
(25, 'CHARSET', 'utf-8'),
(26, 'CONTENT_TYPE', 'html'),
(27, 'HOW_TO_SEND', ''),
(28, 'SENDMAIL_PATH', '/usr/sbin/sendmail'),
(29, 'URL', 'http://subdomain.site2.loc'),
(30, 'ADD_DKIM', 'on'),
(31, 'DKIM_DOMAIN', 'my-domain.com'),
(32, 'DKIM_SELECTOR', 'phpnewsletter'),
(33, 'DKIM_PRIVATE', '.htkeyprivate'),
(34, 'DKIM_PASSPHRASE', 'password'),
(35, 'DKIM_IDENTITY', '1');

-- --------------------------------------------------------

--
-- Структура таблицы `subscribers`
--

CREATE TABLE `subscribers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `ip` varchar(100) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `token` varchar(32) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `subscribers`
--

INSERT INTO `subscribers` (`id`, `name`, `email`, `ip`, `active`, `token`, `created_at`, `updated_at`) VALUES
(1, 'Вася', 'mail1@mail.ru', NULL, 1, '7f80608935902f5ba9f79025243ecefb', '2019-06-17 11:29:42', '2019-06-17 13:59:24'),
(2, 'Петя', 'mail2@mail.ru', NULL, 1, '81c1c7f16765f9e90c797df20eb794e6', '2019-06-17 11:29:42', '2019-06-17 13:59:24');

-- --------------------------------------------------------

--
-- Структура таблицы `subscriptions`
--

CREATE TABLE `subscriptions` (
  `subscriberId` int(11) NOT NULL,
  `categoryId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `subscriptions`
--

INSERT INTO `subscriptions` (`subscriberId`, `categoryId`) VALUES
(1, 2),
(2, 2),
(1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `templates`
--

CREATE TABLE `templates` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `body` mediumtext NOT NULL,
  `prior` tinyint(1) NOT NULL,
  `pos` int(11) NOT NULL,
  `categoryId` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `templates`
--

INSERT INTO `templates` (`id`, `name`, `body`, `prior`, `pos`, `categoryId`, `created_at`, `updated_at`) VALUES
(1, 'dsf', 'ewrtew', 3, 0, 1, '2019-06-05 08:08:24', '2019-06-05 08:08:24'),
(2, 'шаблон 1', 'dsgs dsg', 3, 0, 1, '2019-06-05 08:10:39', '2019-06-05 08:10:39'),
(3, 'dfg', 'dfg', 3, 0, 2, '2019-06-05 10:35:02', '2019-06-05 10:35:02'),
(4, 'dfg', 'fdg ', 3, 0, 2, '2019-06-05 10:35:20', '2019-06-05 10:35:20'),
(5, 'p[]', '<p>p[]p[</p>\r\n', 3, 0, 1, '2019-06-07 15:02:19', '2019-06-07 15:02:19'),
(6, 'jklkj', '<p>jklj</p>\r\n', 3, 0, 1, '2019-06-07 15:03:46', '2019-06-07 15:03:46'),
(7, 'lkj', '<p>kjlkj</p>\r\n', 3, 0, 1, '2019-06-07 15:04:51', '2019-06-07 15:04:51');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `role` varchar(20) NOT NULL,
  `login` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `name`, `description`, `role`, `login`, `password`, `created_at`, `updated_at`) VALUES
(1, 'janickiy', 'janickiy', 'admin', 'janickiy', '$2y$10$mlxprWviwwCT.6fbTX0mm.8VXRtwzyRDKdjQtMytg1uRK/c1G/UNK', '2019-05-21 11:30:54', '2019-06-25 14:00:54'),
(2, 'yanack', 'yanack', 'moderator', 'yanack', '$2y$10$bUAvSmFC/vyugMQ/2nSPLOCX7V.P72sKC4tM9Q4ORmewKjwikijNa', '2019-05-24 11:52:52', '2019-06-25 14:00:07');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `attach`
--
ALTER TABLE `attach`
  ADD PRIMARY KEY (`id`),
  ADD KEY `templateId` (`templateId`);

--
-- Индексы таблицы `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `charset`
--
ALTER TABLE `charset`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `customheaders`
--
ALTER TABLE `customheaders`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userId` (`userId`),
  ADD KEY `email` (`email`),
  ADD KEY `template` (`template`);

--
-- Индексы таблицы `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `subscribers`
--
ALTER TABLE `subscribers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`);

--
-- Индексы таблицы `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD KEY `userId` (`subscriberId`),
  ADD KEY `categoryId` (`categoryId`);

--
-- Индексы таблицы `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `attach`
--
ALTER TABLE `attach`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `charset`
--
ALTER TABLE `charset`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT для таблицы `customheaders`
--
ALTER TABLE `customheaders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `log`
--
ALTER TABLE `log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT для таблицы `subscribers`
--
ALTER TABLE `subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `templates`
--
ALTER TABLE `templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
