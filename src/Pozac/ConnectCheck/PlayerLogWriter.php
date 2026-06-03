<?php

declare(strict_types=1);

namespace Pozac\ConnectCheck;

final class PlayerLogWriter{

    private string $dataDir;

    public function __construct(string $dataDir){
        $this->dataDir = $dataDir;
        if(!is_dir($this->dataDir)){
            @mkdir($this->dataDir, 0777, true);
        }
    }

    /**
     * Append a join record (one JSON line) to the player log file.
     * File name: playername_uuid.json
     */
    public function logJoin(string $playerName, string $uuid, string $ip, int $ping, string $rating) : void{
        $fileName = $this->dataDir . $playerName . "_" . $uuid . ".json";

        $record = json_encode([
            "time"   => (new \DateTimeImmutable("now", new \DateTimeZone(date_default_timezone_get())))->format(\DateTimeInterface::ATOM),
            "ip"     => $ip,
            "ping"   => $ping,
            "rating" => $rating,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $line = $record . PHP_EOL;
        @file_put_contents($fileName, $line, FILE_APPEND | LOCK_EX);
    }
}