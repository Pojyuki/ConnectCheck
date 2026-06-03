<?php

declare(strict_types=1);

namespace Pozac\ConnectCheck\task;

use Pozac\ConnectCheck\PingEvaluator;
use Pozac\ConnectCheck\PlayerLogWriter;
use Pozac\ConnectCheck\PingSampler;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;

final class PingSampleTask extends Task{

    private Player $player;
    private PingSampler $sampler;
    private PingEvaluator $evaluator;
    private PlayerLogWriter $logWriter;
    private string $ip;
    private int $totalSamples;
    private int $sampleCount = 0;

    public function __construct(
        Player $player,
        PingSampler $sampler,
        PingEvaluator $evaluator,
        PlayerLogWriter $logWriter,
        string $ip,
        int $totalSamples
    ){
        $this->player = $player;
        $this->sampler = $sampler;
        $this->evaluator = $evaluator;
        $this->logWriter = $logWriter;
        $this->ip = $ip;
        $this->totalSamples = $totalSamples;
    }

    public function onRun() : void{
        // Stop if player went offline during sampling
        if(!$this->player->isOnline()){
            $this->getHandler()?->cancel();
            return;
        }

        $this->sampler->addSample($this->player);
        $this->sampleCount++;

        if($this->sampleCount >= $this->totalSamples){
            $this->getHandler()?->cancel();

            $avg = $this->sampler->getAverage();
            $rating = $this->evaluator->evaluate($avg);
            $ping = ($avg !== null) ? (int) round($avg) : null;

            // Send chat message
            $msg = $this->evaluator->getMessage($rating, $ping);
            if($msg !== ""){
                $this->player->sendMessage($msg);
            }

            // Write log
            $this->logWriter->logJoin(
                $this->player->getName(),
                $this->player->getUniqueId()->toString(),
                $this->ip,
                $ping ?? 0,
                $this->evaluator->getRatingLabel($rating)
            );
        }
    }
}