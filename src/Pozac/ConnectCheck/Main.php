<?php

declare(strict_types=1);

namespace Pozac\ConnectCheck;

use Pozac\ConnectCheck\event\PlayerJoinListener;
use pocketmine\plugin\PluginBase;

final class Main extends PluginBase{

    private PingEvaluator $evaluator;
    private PlayerLogWriter $logWriter;

    protected function onEnable() : void{
        $this->saveDefaultConfig();

        $cfg = $this->getConfig();
        $samplingInterval = (int) $cfg->getNested("sampling.interval_ticks", 20);
        $totalSamples = (int) $cfg->getNested("sampling.total_samples", 3);

        $thresholds = [
            "excellent" => (int) $cfg->getNested("thresholds.excellent", 35),
            "good"      => (int) $cfg->getNested("thresholds.good", 70),
            "fair"      => (int) $cfg->getNested("thresholds.fair", 100),
            "poor"      => (int) $cfg->getNested("thresholds.poor", 200),
        ];

        $messages = [
            "na"        => (string) $cfg->getNested("messages.na", ""),
            "excellent" => (string) $cfg->getNested("messages.excellent", ""),
            "good"      => (string) $cfg->getNested("messages.good", ""),
            "fair"      => (string) $cfg->getNested("messages.fair", ""),
            "poor"      => (string) $cfg->getNested("messages.poor", ""),
            "very_poor" => (string) $cfg->getNested("messages.very_poor", ""),
        ];

        $this->evaluator = new PingEvaluator($thresholds, $messages);
        $this->logWriter = new PlayerLogWriter($this->getDataFolder() . "players" . DIRECTORY_SEPARATOR);

        $listener = new PlayerJoinListener(
            $this,
            $this->evaluator,
            $this->logWriter,
            $samplingInterval,
            $totalSamples
        );
        $this->getServer()->getPluginManager()->registerEvents($listener, $this);

        $this->getLogger()->info("ConnectCheck enabled - ping measurement ready.");
    }

    protected function onDisable() : void{
        $this->getLogger()->info("ConnectCheck disabled.");
    }
}