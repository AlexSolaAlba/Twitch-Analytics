-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-05-2025 a las 18:43:16
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `twitch_analytics`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `token`
--

CREATE TABLE `token` (
  `tokenID` int(11) NOT NULL,
  `accessToken` varchar(64) NOT NULL,
  `tokenExpire` int(11) NOT NULL,
  `clientId` varchar(64) NOT NULL,
  `clientSecret` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `token`
--

INSERT INTO `token` (`tokenID`, `accessToken`, `tokenExpire`, `clientId`, `clientSecret`) VALUES
(1, 'tb9rmjd4bo2ldoru98wogmfaclvrng', 1750831928, 'mqf0orb9t2ufb34nhd3em686qpb8xc', 'ko7q3si9erixwp84l9w2j9zrdowquh');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `topscache`
--

CREATE TABLE `topscache` (
  `game_id` int(11) NOT NULL,
  `game_name` varchar(100) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `total_videos` int(11) NOT NULL,
  `total_views` int(11) NOT NULL,
  `most_viewed_title` varchar(100) NOT NULL,
  `most_viewed_views` int(11) NOT NULL,
  `most_viewed_duration` varchar(100) NOT NULL,
  `most_viewed_created_at` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `topscache`
--

INSERT INTO `topscache` (`game_id`, `game_name`, `user_name`, `total_videos`, `total_views`, `most_viewed_title`, `most_viewed_views`, `most_viewed_duration`, `most_viewed_created_at`, `created_at`) VALUES
(509658, 'Just Chatting', 'KaiCenat', 36, 453731096, '🦃 MAFIATHON 2 🦃 KAI X KEVIN HART X DRUSKI 🦃 DAY 27 🦃 20% OF REVENUE GOING TO SCHOOL IN NIGERIA 🦃 ALL', 24911112, '22h5m32s', '2024-11-28T02:06:07Z', '2025-05-08 19:18:05'),
(32982, 'Grand Theft Auto V', 'らっだぁ', 34, 43221895, '【VCR2日目】初めての犯罪！ 鳩禁指示禁', 2116977, '13h27m6s', '2023-12-11T04:23:05Z', '2025-05-08 19:18:05'),
(29595, 'Dota 2', 'dota2ti_ru', 21, 148501050, '[RU] Team Secret vs Team Spirit | Основной этап | The International 10 | День 6', 17072719, '12h45m1s', '2021-10-17T05:57:42Z', '2025-05-08 19:18:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE `user` (
  `userID` int(11) NOT NULL,
  `userEmail` varchar(100) NOT NULL,
  `userApiKey` varchar(100) NOT NULL,
  `userToken` varchar(100) NOT NULL,
  `userTokenExpire` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `user`
--

INSERT INTO `user` (`userID`, `userEmail`, `userApiKey`, `userToken`, `userTokenExpire`) VALUES
(1, 'holaquetal22@gmail.com', '8ebf2dd9e613b068ae3cfbdffbeb730f', '09141422ea9df1950069536db36caa0b', 1747251055),
(2, 'holaquetal@gmail.com', '495c04d1a065ecff8cb21dc218674d79', '5b0d2de4530b154647a92e714e03d4e7', 1740497735),
(3, 'usuario@example.com', '91bc1f3dec5152f9a4c30e938008abad', 'd0908e2c4460996281fd6fe87288cbf6', 1746776805),
(4, 'usuariooo@example.com', '07a48053d48178ba98de51cfefebab70', '', 0),
(5, 'test@example.com', '24e9a3dea44346393f632e4161bc83e6', '24e9a3dea44346393f632e4161bc83e6', 1747248356);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `userstwitch`
--

CREATE TABLE `userstwitch` (
  `id` varchar(100) NOT NULL,
  `user_login` text NOT NULL,
  `display_name` text NOT NULL,
  `user_type` text NOT NULL,
  `broadcaster_type` text NOT NULL,
  `user_description` longtext NOT NULL,
  `profile_image_url` text NOT NULL,
  `offline_image_url` text NOT NULL,
  `view_count` text NOT NULL,
  `created_at` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `userstwitch`
--

INSERT INTO `userstwitch` (`id`, `user_login`, `display_name`, `user_type`, `broadcaster_type`, `user_description`, `profile_image_url`, `offline_image_url`, `view_count`, `created_at`) VALUES
('1', 'elsmurfoz', 'elsmurfoz', '', '', '', 'https://static-cdn.jtvnw.net/user-default-pictures-uv/215b7342-def9-11e9-9a66-784f43822e80-profile_image-300x300.png', '', '0', '2007-05-22T10:37:47Z'),
('1234', 'zdraste_vladkenov', 'zdraste_vladkenov', '', '', 'wasde876', 'https://static-cdn.jtvnw.net/user-default-pictures-uv/ebe4cd89-b4f4-4cd9-adac-2f30151b4209-profile_image-300x300.png', '', '0', '2018-09-04T15:23:04Z'),
('2', 'goyabean', 'goyabean', '', '', '', 'https://static-cdn.jtvnw.net/user-default-pictures-uv/294c98b5-e34d-42cd-a8f0-140b72fba9b0-profile_image-300x300.png', '', '0', '2007-05-22T10:37:47Z'),
('3', 'djracerx', 'djracerx', '', '', '', 'https://static-cdn.jtvnw.net/user-default-pictures-uv/998f01ae-def8-11e9-b95c-784f43822e80-profile_image-300x300.png', '', '0', '2007-05-22T10:37:47Z');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`tokenID`);

--
-- Indices de la tabla `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userID`);

--
-- Indices de la tabla `userstwitch`
--
ALTER TABLE `userstwitch`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `token`
--
ALTER TABLE `token`
  MODIFY `tokenID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `user`
--
ALTER TABLE `user`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
