<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Cron\Scheduler;
use App\Database;
use App\Metrics;
use App\Models\BoardColumn;
use App\Session\DatabaseSessionHandler;

Database::boot();

$force = in_array('--force', $argv, true);
$scheduler = new Scheduler();

// Задача 1: Очистка устаревших сессий в БД (раз в 5 минут)
$scheduler->everySeconds('cleanup_expired_sessions', 300, function (): array {
    $handler = new DatabaseSessionHandler(3600);
    $deleted = $handler->gc(3600);
    return ['deleted_sessions' => $deleted];
});

// Задача 2: Синхронизация метрик карточек по колонкам (раз в 1 минуту)
$scheduler->everySeconds('sync_trello_metrics', 60, function (): array {
    $columns = BoardColumn::withCount('cards')->get();
    $counts = [];
    foreach ($columns as $col) {
        $counts[$col->title] = $col->cards_count;
    }
    Metrics::updateCardsByColumnGauge($counts);
    return ['columns_synced' => count($counts)];
});

$scheduler->runDueTasks($force);
