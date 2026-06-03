# ConnectCheck

[![PMMP 5.x](https://img.shields.io/badge/PMMP-5.x-blue)](https://pmmp.io)

A PocketMine-MP 5.x plugin that measures player ping on join and provides connection quality feedback.

## Features

- **Ping Sampling** — Takes 3 ping samples over 3 seconds on player join and calculates the average
- **Connection Rating** — Rates connection quality into 6 tiers based on configurable thresholds
- **Chat Feedback** — Sends a single chat message with ping value and gameplay experience hint
- **Session Logging** — Appends each join session (IP, ping, rating, timestamp) to a per-player JSON file

### Ping Tiers (configurable)

| Tier | Threshold | Message Color |
|------|-----------|---------------|
| Excellent | < 35ms | Green |
| Good | 35–69ms | Dark Green |
| Fair | 70–99ms | Gold |
| Poor | 100–199ms | Red |
| Very Poor | ≥ 200ms | Dark Red |
| N/A | Not measured | Yellow |

## Installation

1. Place the `ConnectCheck` folder into your server's `plugins/` directory
2. Restart the server
3. Configure thresholds and messages in `plugins/ConnectCheck/resources/config.yml`

## Configuration

```yaml
sampling:
  interval_ticks: 20   # ~1 second per sample
  total_samples: 3     # take 3 samples then average

thresholds:
  excellent: 35
  good: 70
  fair: 100
  poor: 200

messages:
  excellent: "§a⚡ Ping: {ping}ms — Your connection is Excellent! ..."
  # ... (all tiers customizable)
```

## Data Storage

Join logs are stored in `plugin_data/ConnectCheck/players/<name>_<uuid>.json` as JSON lines:

```json
{"time":"2026-06-03T18:05:00+08:00","ip":"1.2.3.4","ping":42,"rating":"Good"}
```

## Authors

- Pozac
- Codex

## License

MIT
