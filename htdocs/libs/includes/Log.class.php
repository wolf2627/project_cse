<?php

class Log
{
    /**
     * Logs user activities Database and in a log file.
     *
     * @param string $activity Description of the activity performed.
     * @param string $level Log level (INFO, ERROR, WARNING, DEBUG).
     * @param bool $dbup Log to MongoDB if true.
     */
    public static function dolog($activity, $level = 'info', $dbup = false)
    {
        // Normalize log level
        $level = strtoupper($level);
        $allowedLevels = ['INFO', 'ERROR', 'WARNING', 'DEBUG'];

        if (!in_array($level, $allowedLevels)) {
            $level = 'INFO';
        }

        // Get current user session
        $session_user = Session::getUser();
        $userId = $session_user ? $session_user->get_Id() : 'unknown';

        // Prepare timestamp
        $date = new DateTime();
        $timestamp = $date->format('D M d H:i:s.u Y');

        // Get client information
        $clientIp = $_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $clientPort = $_SERVER['REMOTE_PORT'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

        // Log data structure
        $logEntry = [
            'user_id' => $userId !== 'unknown' ? new MongoDB\BSON\ObjectId($userId) : null,
            'activity' => $activity,
            'level' => $level,
            'client' => "{$clientIp}:{$clientPort}",
            'user_agent' => $userAgent,
            'time' => $timestamp,
        ];

        if($dbup){
            // Log to MongoDB
            self::logToDB($logEntry);
        }
        
        // Log to File
        self::logToFile($logEntry);
    }

    /**
     * Logs an entry to MongoDB.
     *
     * @param array $logEntry
     */
    private static function logToDB(array $logEntry)
    {
        try {
            $db = Database::getConnection();
            $collection = $db->activity_log;

            // Remove null fields before inserting
            $cleanLogEntry = array_filter($logEntry, fn($value) => $value !== null);

            $result = $collection->insertOne($cleanLogEntry);

            if (!$result->isAcknowledged()) {
                throw new Exception('MongoDB insert operation not acknowledged.');
            }
        } catch (Exception $e) {
            error_log('MongoDB Logging Error: ' . $e->getMessage());
        }
    }

    /**
     * Logs an entry to a file.
     *
     * @param array $logEntry
     */
    private static function logToFile(array $logEntry)
    {
        $logFilePath = $_SERVER['DOCUMENT_ROOT'] . '/../logs/activity.log';

        try {
            // Prepare log message
            $logMessage = sprintf(
                "[%s] [%s] - User ID: %s - Action: %s - %s - User Agent: %s\n",
                $logEntry['time'],
                $logEntry['level'],
                $logEntry['user_id'] ?? 'unknown',
                $logEntry['activity'],
                $logEntry['client'],
                $logEntry['user_agent']
            );

            // Append to the log file
            if (file_put_contents($logFilePath, $logMessage, FILE_APPEND | LOCK_EX) === false) {
                throw new Exception('Failed to write to log file.');
            }
        } catch (Exception $e) {
            error_log('File Logging Error: ' . $e->getMessage());
        }
    }
}
