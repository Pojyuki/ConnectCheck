<?php

declare(strict_types=1);

namespace Pozac\ConnectCheck;

final class PingEvaluator{

    /** @var array<string, int> */
    private array $thresholds;
    /** @var array<string, string> */
    private array $messages;

    /**
     * @param array<string, int>    $thresholds
     * @param array<string, string> $messages
     */
    public function __construct(array $thresholds, array $messages){
        $this->thresholds = $thresholds;
        $this->messages = $messages;
    }

    /**
     * Evaluate average ping and return the rating key.
     * Returns "na" if ping is negative or null (not yet measured).
     */
    public function evaluate(?float $avgPing) : string{
        if($avgPing === null || $avgPing < 0){
            return "na";
        }

        $ping = (int) $avgPing;

        if($ping < $this->thresholds["excellent"]){
            return "excellent";
        }
        if($ping < $this->thresholds["good"]){
            return "good";
        }
        if($ping < $this->thresholds["fair"]){
            return "fair";
        }
        if($ping < $this->thresholds["poor"]){
            return "poor";
        }
        return "very_poor";
    }

    /**
     * Get the formatted chat message for a given rating.
     * When ping is null (N/A), the raw template is used as-is.
     */
    public function getMessage(string $rating, ?int $ping) : string{
        $template = $this->messages[$rating] ?? "";
        if($ping !== null){
            return str_replace("{ping}", (string) $ping, $template);
        }
        return $template;
    }

    /**
     * Get the human-readable rating label.
     */
    public function getRatingLabel(string $rating) : string{
        return match($rating){
            "na"        => "N/A",
            "excellent" => "Excellent",
            "good"      => "Good",
            "fair"      => "Fair",
            "poor"      => "Poor",
            "very_poor" => "Very Poor",
            default     => "Unknown",
        };
    }
}