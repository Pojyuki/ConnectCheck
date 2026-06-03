<?php

declare(strict_types=1);

namespace Pozac\ConnectCheck\event;

use Pozac\ConnectCheck\Main;
use Pozac\ConnectCheck\PingEvaluator;
use Pozac\ConnectCheck\PlayerLogWriter;
use Pozac\ConnectCheck\PingSampler;
use Pozac\ConnectCheck\task\PingSampleTask;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

final class PlayerJoinListener implements Listener{

    private Main $plugin;
    private PingEvaluator $evaluator;
    private PlayerLogWriter $logWriter;
    private int $samplingInterval;
    private int $totalSamples;

    public function __construct(
        Main $plugin,
        PingEvaluator $evaluator,
        PlayerLogWriter $logWriter,
        int $samplingInterval,
        int $totalSamples
    ){
        $this->plugin = $plugin;
        $this->evaluator = $evaluator;
        $this->logWriter = $logWriter;
        $this->samplingInterval = $samplingInterval;
        $this->totalSamples = $totalSamples;
    }

    public function onPlayerJoin(PlayerJoinEvent $event) : void{
        $player = $event->getPlayer();
        $session = $player->getNetworkSession();
        $ip = $session->getIp();

        $sampler = new PingSampler();

        $this->plugin->getScheduler()->scheduleRepeatingTask(
            new PingSampleTask(
                $player,
                $sampler,
                $this->evaluator,
                $this->logWriter,
                $ip,
                $this->totalSamples
            ),
            $this->samplingInterval
        );
    }
}