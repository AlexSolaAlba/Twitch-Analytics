-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-05-2025 a las 23:53:40
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
-- Base de datos: `twitch-analytics`
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
(1, 'jostpf5q0puzmxmkba9iyug38kjtg', 1753134796, 'mqf0orb9t2ufb34nhd3em686qpb8xc', 'ko7q3si9erixwp84l9w2j9zrdowquh');

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
(509658, 'Just Chatting', 'Kai Cenat', 36, 414857711, '🦃 MAFIATHON 2 🦃 KAI X KEVIN HART X DRUSKI 🦃 DAY 27 🦃 20% OF REVENUE GOING TO SCHOOL IN NIGERIA 🦃 ALL', 24868821, '22h5m32s', '2024-11-28T02:06:07Z', '2025-05-24 21:52:05'),
(516575, 'VALORANT', '0', 3, 7705399, 'V a l o r a n t Grind begins | Among us tonight at 8 CENTRAL with a SOLID crew!', 4549587, '15h15m21s', '2020-09-11T18:52:09Z', '2025-05-24 21:52:05');

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
(1, 'holaquetal22@gmail.com', '8ebf2dd9e613b068ae3cfbdffbeb730f', 'bab4db4aedcc1cefa549b5bbd080ace9', 1748362944),
(2, 'holaquetal@gmail.com', '74e365a90fb4b7e129070418d4598a62', '5b0d2de4530b154647a92e714e03d4e7', 1740497735),
(3, 'ghdjs@gmail.com', 'fb514f2384d96403b50e65723b1745ac', '', 0),
(4, 'test@example.com', '24e9a3dea44346393f632e4161bc83e6', '24e9a3dea44346393f632e4161bc83e6', 1748382725),
(5, 'testcaducado@gmail.com', 'e9cb15bba53c9d05a23c21afc7b44f40', 'e9cb15bba53c9d05a23c21afc7b44f40', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usersTwitch`
--

CREATE TABLE `usersTwitch` (
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
-- Volcado de datos para la tabla `usersTwitch`
--

INSERT INTO `usersTwitch` (`id`, `user_login`, `display_name`, `user_type`, `broadcaster_type`, `user_description`, `profile_image_url`, `offline_image_url`, `view_count`, `created_at`) VALUES
('1', 'elsmurfoz', 'elsmurfoz', '', '', '', 'https://static-cdn.jtvnw.net/user-default-pictures-uv/215b7342-def9-11e9-9a66-784f43822e80-profile_image-300x300.png', '', '0', '2007-05-22T10:37:47Z'),
('2', 'goyabean', 'goyabean', '', '', '', 'https://static-cdn.jtvnw.net/user-default-pictures-uv/294c98b5-e34d-42cd-a8f0-140b72fba9b0-profile_image-300x300.png', '', '0', '2007-05-22T10:37:47Z'),
('26', '911kh', '911kh', '', '', '', 'https://static-cdn.jtvnw.net/user-default-pictures-uv/13e5fa74-defa-11e9-809c-784f43822e80-profile_image-300x300.png', '', '0', '2018-09-04T15:19:19Z'),
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
-- Indices de la tabla `usersTwitch`
--
ALTER TABLE `usersTwitch`
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
