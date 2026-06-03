<?php

declare(strict_types=1);

namespace Pozac\ConnectCheck;

use pocketmine\player\Player;

final class PingSampler{

    /** @var array<int, int> collected ping samples */
    private array $samples = [];

    public function addSample(Player $player) : void{
        $session = $player->getNetworkSession();
        $ping = $session->getPing();
        if($ping !== null && $ping >= 0){
            $this->samples[] = $ping;
        }
    }

    /** @return int[] */
    public function getSamples() : array{
        return $this->samples;
    }

    /** Calculate the average of collected samples, or null if none. */
    public function getAverage() : ?float{
        if($this->samples === []){
            return null;
        }
        return array_sum($this->samples) / count($this->samples);
    }
}