<?php

declare(strict_types=1);

/**
 * Эндпоинт для Prometheus: отдаёт все накопленные в APCu метрики в текстовом формате.
 *
 * Снаружи (порт :80) nginx отвечает на /metrics.php 404 — доступ есть только через
 * внутренний служебный server :8081 (docker/nginx/conf.d/status.conf). Метрики выдают
 * внутреннее устройство сервиса (маршруты, нагрузку, бизнес-показатели) и не
 * предназначены пользователям.
 *
 * Сам себя этот скрипт не инструментирует — см. начало instrumentation/prepend.php.
 */

require __DIR__ . '/../vendor/autoload.php';

use App\Metrics;
use Prometheus\RenderTextFormat;
use App\Database;
use App\Models\BoardColumn;

try {
    Database::boot();
    $columns = BoardColumn::withCount('cards')->get();
    $counts = [];
    foreach ($columns as $col) {
        $counts[$col->title] = $col->cards_count;
    }
    Metrics::updateCardsByColumnGauge($counts);
} catch (\Throwable $e) {
    // Если БД временно недоступна, /metrics.php всё равно должен отдать остальные метрики
}

if (!Metrics::enabled()) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Метрики выключены: запустите стек мониторинга (make mon-up)\n";
    exit;
}

$renderer = new RenderTextFormat();

header('Content-Type: ' . RenderTextFormat::MIME_TYPE);
echo $renderer->render(Metrics::registry()->getMetricFamilySamples());
