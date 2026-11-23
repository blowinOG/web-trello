<?php

declare(strict_types=1);

namespace App\Cron;

use App\Log;

final class Scheduler
{
    /** @var array<int, array{name: string, intervalSeconds: int, callback: callable}> */
    private array $tasks = [];

    private string $stateDir;

    public function __construct(?string $stateDir = null)
    {
        $this->stateDir = $stateDir ?? dirname(__DIR__, 2) . '/logs/cron_state';
        if (!is_dir($this->stateDir)) {
            @mkdir($this->stateDir, 0777, true);
        }
    }

    public function everySeconds(string $name, int $seconds, callable $callback): self
    {
        $this->tasks[] = [
            'name'            => $name,
            'intervalSeconds' => $seconds,
            'callback'        => $callback,
        ];

        return $this;
    }

    public function runDueTasks(bool $force = false): void
    {
        $now = time();

        foreach ($this->tasks as $task) {
            $stateFile = $this->stateDir . '/' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $task['name']) . '.last';
            $lastRun = is_file($stateFile) ? (int) file_get_contents($stateFile) : 0;

            if (!$force && ($now - $lastRun) < $task['intervalSeconds']) {
                continue;
            }

            $start = microtime(true);
            try {
                $result = ($task['callback'])();
                file_put_contents($stateFile, (string) $now);

                $durationMs = round((microtime(true) - $start) * 1000, 2);
                Log::info("Cron task finished: {$task['name']}", [
                    'cron_task'   => $task['name'],
                    'duration_ms' => $durationMs,
                    'result'      => $result,
                ]);
                echo "[OK] {$task['name']} ({$durationMs} ms)" . PHP_EOL;
            } catch (\Throwable $e) {
                Log::error("Cron task failed: {$task['name']}", [
                    'cron_task' => $task['name'],
                    'exception' => $e->getMessage(),
                ]);
                echo "[ERR] {$task['name']}: {$e->getMessage()}" . PHP_EOL;
            }
        }
    }
}
