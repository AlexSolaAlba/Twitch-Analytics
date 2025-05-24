<?php

namespace TwitchAnalytics\Infraestructure\DB;

use TwitchAnalytics\Domain\Models\Video;

class DataBaseHandlerVideos extends DataBaseHandler
{
    public function insertVideosInDB(array $videos): void
    {
        $connection = $this->connectWithDB();
        $this->checkConnection($connection);

        try {
            $stmt = $this->insertVideosInDBQuery($connection, $videos);
            $stmt->close();
        } finally {
            if ($connection instanceof \mysqli) {
                $connection->close();
            }
        }
    }

    private function insertVideosInDBQuery(false|\mysqli_stmt $connection, array $videos): false|\mysqli_stmt
    {
        $stmt = $connection->prepare(
            "INSERT INTO topscache(game_id, game_name, user_name, total_videos, total_views,
            most_viewed_title, most_viewed_views, most_viewed_duration, most_viewed_created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        foreach ($videos as $video) {
            $gameId = $video->getGameId();
            $gameName = $video->getGameName();
            $userName = $video->getUserName();
            $totalVideos = $video->getTotalVideos();
            $totalViews = $video->getTotalViews();
            $mostViewedTitle = $video->getMostViewedTitle();
            $mostViewedViews = $video->getMostViewedViews();
            $mostViewedDuration = $video->getMostViewedDuration();
            $mostViewedCreatedAt = $video->getMostViewedCreatedAt();
            $stmt->bind_param(
                "issiisiss",
                $gameId,
                $gameName,
                $userName,
                $totalVideos,
                $totalViews,
                $mostViewedTitle,
                $mostViewedViews,
                $mostViewedDuration,
                $mostViewedCreatedAt
            );
            $this->checkStmtExecution($stmt);
        }
        return $stmt;
    }

    public function getVideosFromDB(): bool|array
    {
        $connection = $this->connectWithDB();
        $this->checkConnection($connection);

        try {
            $stmt = $this->getVideosFromDBQuery($connection);
            $this->checkStmtExecution($stmt);
            $videosRaw = $stmt->get_result();
            $stmt->close();
            $videos = [];
            if ($videosRaw->num_rows > 0) {
                while ($row = $videosRaw->fetch_assoc()) {
                    $videos[] = new Video(
                        $row["game_id"],
                        $row["game_name"],
                        $row["user_name"],
                        $row["total_videos"],
                        $row["total_views"],
                        $row["most_viewed_title"],
                        $row["most_viewed_views"],
                        $row["most_viewed_duration"],
                        $row["most_viewed_created_at"],
                        $row["created_at"]
                    );
                }
                return $videos;
            }
            return false;
        } finally {
            if ($connection instanceof \mysqli) {
                $connection->close();
            }
        }
    }

    private function getVideosFromDBQuery(false|\mysqli_stmt $connection): false|\mysqli_stmt
    {
        $stmt = $connection->prepare("SELECT * FROM topscache");
        return $stmt;
    }

    public function deleteAllVideosFromDB(): void
    {
        $connection = $this->connectWithDB();
        $this->checkConnection($connection);

        try {
            $stmt = $this->deleteAllVideosFromDBQuery($connection);
            $this->checkStmtExecution($stmt);
            $stmt->close();
        } finally {
            if ($connection instanceof \mysqli) {
                $connection->close();
            }
        }
    }

    private function deleteAllVideosFromDBQuery(false|\mysqli_stmt $connection): false|\mysqli_stmt
    {
        $stmt = $connection->prepare("DELETE FROM topscache");
        return $stmt;
    }
}
