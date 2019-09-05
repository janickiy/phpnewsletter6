-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Сен 05 2019 г., 03:19
-- Версия сервера: 10.1.36-MariaDB
-- Версия PHP: 7.2.10

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
  `file_name` varchar(255) NOT NULL,
  `templateId` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `attach`
--

INSERT INTO `attach` (`id`, `name`, `file_name`, `templateId`, `created_at`, `updated_at`) VALUES
(1, '1c365d540e69c948.jpeg', '001.jpeg', 1, '2019-08-26 22:40:34', '2019-08-26 22:40:34');

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
(2, 'Категория 1113', NULL, '2019-07-29 22:23:37'),
(3, 'erewrwe', '2019-07-29 22:23:42', '2019-07-29 22:23:42');

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
-- Структура таблицы `ready_sent`
--

CREATE TABLE `ready_sent` (
  `id` int(11) NOT NULL,
  `subscriberId` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `templateId` int(11) NOT NULL,
  `template` varchar(255) NOT NULL,
  `success` tinyint(1) NOT NULL,
  `errorMsg` text,
  `readMail` tinyint(1) DEFAULT NULL,
  `scheduleId` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `ready_sent`
--

INSERT INTO `ready_sent` (`id`, `subscriberId`, `email`, `templateId`, `template`, `success`, `errorMsg`, `readMail`, `scheduleId`, `created_at`, `updated_at`) VALUES
(1, 1, 'ewqre@qweqw.ru', 1, '', 1, NULL, 1, 1, '2019-08-31 21:00:00', '2019-08-31 21:00:00');

-- --------------------------------------------------------

--
-- Структура таблицы `redirect_log`
--

CREATE TABLE `redirect_log` (
  `id` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `redirect_log`
--

INSERT INTO `redirect_log` (`id`, `url`, `email`, `created_at`, `updated_at`) VALUES
(1, 'wer.ru', 'weqr@wrwe.ru', '2019-09-04 21:00:00', '2019-09-04 21:00:00'),
(2, 'wer.ru', 'bererw@242r.ru', NULL, NULL),
(3, 'wer2.ru', 'aetgfd@wrewe.ti', NULL, NULL),
(4, 'ter.ru', 'dfsr@werew.ru', '2019-09-04 21:00:00', NULL),
(5, 'ter.ru', 'eqweqw@rwerw.ru', '2019-09-10 21:00:00', NULL),
(6, 'ter.ru', 'qewrr21@Q3.TRU', '2019-09-03 21:00:00', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `schedule`
--

CREATE TABLE `schedule` (
  `id` int(11) NOT NULL,
  `date` timestamp NULL DEFAULT NULL,
  `templateId` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `schedule`
--

INSERT INTO `schedule` (`id`, `date`, `templateId`, `created_at`, `updated_at`) VALUES
(1, '2019-07-31 21:25:00', 1, '2019-07-31 20:25:51', '2019-07-31 20:25:51'),
(2, '2019-07-31 23:00:00', 4, '2019-07-31 20:42:47', '2019-07-31 20:42:47'),
(3, '2019-08-02 09:00:00', 2, '2019-07-31 20:59:12', '2019-07-31 20:59:12');

-- --------------------------------------------------------

--
-- Структура таблицы `schedule_category`
--

CREATE TABLE `schedule_category` (
  `scheduleId` int(11) NOT NULL,
  `categoryId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `schedule_category`
--

INSERT INTO `schedule_category` (`scheduleId`, `categoryId`) VALUES
(1, 1),
(1, 2),
(2, 1),
(2, 2),
(1, 1),
(1, 2),
(2, 1),
(2, 2),
(3, 3);

-- --------------------------------------------------------

--
-- Структура таблицы `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `token` varchar(100) NOT NULL,
  `expiry` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `sessions`
--

INSERT INTO `sessions` (`id`, `userId`, `token`, `expiry`, `created_at`, `updated_at`) VALUES
(1, 1, '412ff5af8c0d9a71d46ae64a333fa324', '2020-08-20 10:35:46', '2019-08-21 10:35:46', '2019-08-21 10:35:46'),
(2, 1, '019cd7bffad653427315717c83367bce', '2020-08-20 10:42:41', '2019-08-21 10:42:41', '2019-08-21 10:42:41'),
(4, 1, '1fae3f940c8c45046a439aef4d1e2546', '2020-09-03 20:15:34', '2019-09-04 20:15:36', '2019-09-04 20:15:36');

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
(1, 'EMAIL', 'yanack@yandex.ru'),
(2, 'SHOW_EMAIL', '1'),
(3, 'FROM', 'wwwwwwww'),
(4, 'RETURN_PATH', ''),
(5, 'LIST_OWNER', ''),
(6, 'ORGANIZATION', ''),
(7, 'SUBJECT_TEXT_CONFIRM', 'Подписка на рассылку'),
(8, 'TEXT_CONFIRMATION', 'Здравствуйте, %NAME%\r\n\r\nПолучение рассылки возможно после завершения этапа активации подписки. Чтобы активировать подписку, перейдите по следующей ссылке: %CONFIRM%\r\n\r\nЕсли Вы не производили подписку на данный email, просто проигнорируйте это письмо или перейдите по ссылке: %UNSUB%\r\n\r\nС уважением, \r\nадминистратор сайта %SERVER_NAME%'),
(9, 'REQUIRE_SUB_CONFIRMATION', '0'),
(10, 'UNSUBLINK', 'Отписаться от рассылки: <a href=%UNSUB%>%UNSUB%</a>'),
(11, 'SHOW_UNSUBSCRIBE_LINK', '1'),
(12, 'REQUEST_REPLY', '0'),
(15, 'NEW_SUBSCRIBER_NOTIFY', '0'),
(16, 'SLEEP', '0'),
(17, 'LIMIT_NUMBER', '300'),
(18, 'LIMIT_SEND', '0'),
(19, 'DAYS_FOR_REMOVE_SUBSCRIBER', '7'),
(20, 'REMOVE_SUBSCRIBER', '0'),
(21, 'RANDOM_SEND', '0'),
(22, 'RENDOM_REPLACEMENT_SUBJECT', '0'),
(23, 'RANDOM_REPLACEMENT_BODY', '0'),
(24, 'PRECEDENCE', 'list'),
(25, 'CHARSET', 'utf-8'),
(26, 'CONTENT_TYPE', 'html'),
(27, 'HOW_TO_SEND', 'smtp'),
(28, 'SENDMAIL_PATH', '/usr/sbin/sendmail'),
(29, 'URL', 'http://subdomain.site2.loc/'),
(30, 'ADD_DKIM', '0'),
(31, 'DKIM_DOMAIN', 'my-domain.com'),
(32, 'DKIM_SELECTOR', 'phpnewsletter'),
(33, 'DKIM_PRIVATE', '.htkeyprivate'),
(34, 'DKIM_PASSPHRASE', 'password'),
(35, 'DKIM_IDENTITY', ''),
(36, 'INTERVAL_TYPE', 'minute'),
(37, 'INTERVAL_NUMBER', '1'),
(38, 'LANGUAGE', 'ru');

-- --------------------------------------------------------

--
-- Структура таблицы `smtp`
--

CREATE TABLE `smtp` (
  `id` int(11) NOT NULL,
  `host` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `port` int(6) NOT NULL,
  `authentication` varchar(20) NOT NULL,
  `secure` varchar(20) NOT NULL,
  `timeout` int(6) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `smtp`
--

INSERT INTO `smtp` (`id`, `host`, `username`, `email`, `password`, `port`, `authentication`, `secure`, `timeout`, `active`, `created_at`, `updated_at`) VALUES
(4, 'smtp.yandex.ru', 'yanack@yandex.ru', '', 'larafabian', 465, 'login', 'ssl', 5, 1, '2019-08-21 07:26:20', '2019-08-21 07:26:20');

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
  `timeSent` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `subscribers`
--

INSERT INTO `subscribers` (`id`, `name`, `email`, `ip`, `active`, `token`, `timeSent`, `created_at`, `updated_at`) VALUES
(1, 'Вася', 'mail1@mail.ru', NULL, 0, '7f80608935902f5ba9f79025243ecefb', NULL, '2019-06-17 11:29:42', '2019-08-29 21:45:02'),
(2, 'Петя', 'mail2@mail.ru', NULL, 1, '81c1c7f16765f9e90c797df20eb794e6', NULL, '2019-06-17 11:29:42', '2019-06-17 13:59:24');

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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `templates`
--

INSERT INTO `templates` (`id`, `name`, `body`, `prior`, `pos`, `created_at`, `updated_at`) VALUES
(1, 'dsf', '<p>ewrtew</p>\r\n', 3, 0, '2019-06-05 08:08:24', '2019-08-26 22:40:34'),
(2, 'шаблон 1', '<p>dsgs dsg</p>\r\n', 3, 0, '2019-06-05 08:10:39', '2019-08-08 00:33:47'),
(3, 'dfg', 'dfg', 3, 0, '2019-06-05 10:35:02', '2019-06-05 10:35:02'),
(4, 'dfg', 'fdg ', 3, 0, '2019-06-05 10:35:20', '2019-06-05 10:35:20'),
(5, 'p[]', '<p>p[]p[</p>\r\n', 3, 0, '2019-06-07 15:02:19', '2019-06-07 15:02:19'),
(6, 'jklkj', '<p>jklj</p>\r\n', 3, 0, '2019-06-07 15:03:46', '2019-06-07 15:03:46'),
(7, 'lkj', '<p>kjlkj</p>\r\n', 3, 0, '2019-06-07 15:04:51', '2019-06-07 15:04:51');

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
(1, 'janickiy', 'janickiy', 'admin', 'janickiy', '$2y$10$eJwI.FaYENMU7YulbOqKYuO4i0qusBnfaAvzJnsj.P9yJoyG.fKfG', '2019-05-21 11:30:54', '2019-08-21 08:31:37'),
(0, 'yanack', '', 'admin', 'yanack', '$2y$10$0ig2G8GtpF6YoXd6x8AfduksLTIp8vAgOFc.VP0fOSJhcJuC//y5.', '2019-08-21 08:32:39', '2019-08-21 08:32:39');

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
-- Индексы таблицы `ready_sent`
--
ALTER TABLE `ready_sent`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subscriberId` (`subscriberId`),
  ADD KEY `templateId` (`templateId`),
  ADD KEY `scheduleId` (`scheduleId`),
  ADD KEY `success` (`success`),
  ADD KEY `readmail` (`readMail`);

--
-- Индексы таблицы `redirect_log`
--
ALTER TABLE `redirect_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`),
  ADD KEY `url` (`url`);

--
-- Индексы таблицы `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `templates` (`templateId`);

--
-- Индексы таблицы `schedule_category`
--
ALTER TABLE `schedule_category`
  ADD KEY `categoryId` (`categoryId`),
  ADD KEY `scheduleId` (`scheduleId`);

--
-- Индексы таблицы `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `smtp`
--
ALTER TABLE `smtp`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
-- AUTO_INCREMENT для таблицы `ready_sent`
--
ALTER TABLE `ready_sent`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `redirect_log`
--
ALTER TABLE `redirect_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `schedule`
--
ALTER TABLE `schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT для таблицы `smtp`
--
ALTER TABLE `smtp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
