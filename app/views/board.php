<?php
$h = static fn (string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
$currentUser = \App\Auth\AuthService::user();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $h($board?->title ?? 'Trello Board') ?></title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 1040px; margin: 2rem auto; padding: 0 1rem; color: #1c1c1c; background: #f4f5f7; }
        h1 { font-size: 1.5rem; margin-bottom: 0.4rem; }
        .auth-bar { margin-bottom: 0.8rem; font-size: 0.9rem; }
        .status-bar { font-size: 0.85rem; color: #555; margin-bottom: 1rem; }
        .flash { background: #dbeafe; border: 1px solid #93c5fd; color: #1e3a8a; padding: 0.6rem 0.9rem; border-radius: 6px; margin-bottom: 1rem; }
        .ok { color: #0a7a2f; font-weight: 600; }
        .fail { color: #b00020; font-weight: 600; }
        .board { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; align-items: start; }
        .column { background: #ebecf0; border-radius: 8px; padding: 0.8rem; }
        .column h2 { font-size: 1rem; margin: 0 0 0.8rem; display: flex; justify-content: space-between; }
        .card { background: #fff; border-radius: 6px; padding: 0.7rem; margin-bottom: 0.6rem; box-shadow: 0 1px 2px rgba(0,0,0,0.1); }
        .card-title { font-weight: 600; margin-bottom: 0.3rem; }
        .card-desc { font-size: 0.85rem; color: #555; margin-bottom: 0.5rem; }
        .move-form { display: flex; gap: 0.3rem; margin-top: 0.4rem; }
        .move-form select, .move-form button { font-size: 0.8rem; padding: 0.25rem 0.4rem; }
        .add-form { display: flex; flex-direction: column; gap: 0.4rem; margin-top: 0.8rem; border-top: 1px solid #d5d7dc; padding-top: 0.7rem; }
        .add-form input, .add-form textarea { padding: 0.45rem; border: 1px solid #ccc; border-radius: 4px; font-family: inherit; }
        .add-form button { padding: 0.45rem; border: 0; border-radius: 4px; background: #2563eb; color: #fff; cursor: pointer; font-weight: 500; }
        .add-form button:hover { background: #1d4ed8; }
    </style>
</head>
<body>
    <h1><?= $h($board?->title ?? 'Trello Board') ?></h1>

    <div class="auth-bar">
        <?php if ($currentUser !== null): ?>
            Вы вошли как: <b><?= $h($currentUser->name) ?></b> (<?= $h($currentUser->email) ?>)
            <form method="post" action="/logout" style="display:inline; margin-left: 0.5rem;">
                <button type="submit" style="padding: 0.2rem 0.6rem; cursor: pointer;">Выйти</button>
            </form>
        <?php else: ?>
            Вы не авторизованы: <a href="/login">Войти</a> | <a href="/register">Регистрация</a>
        <?php endif; ?>
    </div>

    <div class="status-bar">
        PHP <?= $h(PHP_VERSION) ?> | БД:
        <span class="<?= $dbError === null ? 'ok' : 'fail' ?>">
            <?= $dbError === null ? 'PostgreSQL' : 'Ошибка: ' . $h($dbError) ?>
        </span>
        | Сессия в БД: <code><?= $h((string) $sessionId) ?></code> (просмотров в сессии: <b><?= (int) $pageViews ?></b>)
    </div>

    <?php if (!empty($flashMessage)): ?>
        <div class="flash"><?= $h((string) $flashMessage) ?></div>
    <?php endif; ?>

    <?php if ($dbError === null): ?>
        <div class="board">
            <?php foreach ($columns as $column): ?>
                <div class="column">
                    <h2>
                        <span><?= $h($column->title) ?></span>
                        <span>(<?= $column->cards->count() ?>)</span>
                    </h2>

                    <?php foreach ($column->cards as $card): ?>
                        <div class="card">
                            <div class="card-title"><?= $h($card->title) ?></div>
                            <?php if ($card->description): ?>
                                <div class="card-desc"><?= $h($card->description) ?></div>
                            <?php endif; ?>

                            <form class="move-form" method="post" action="/cards/move">
                                <input type="hidden" name="card_id" value="<?= (int) $card->id ?>">
                                <select name="target_column_id">
                                    <?php foreach ($columns as $targetCol): ?>
                                        <option value="<?= (int) $targetCol->id ?>" <?= $targetCol->id === $column->id ? 'selected' : '' ?>>
                                            <?= $h($targetCol->title) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit">Перенести</button>
                            </form>
                        </div>
                    <?php endforeach; ?>

                    <form class="add-form" method="post" action="/cards/create">
                        <input type="hidden" name="column_id" value="<?= (int) $column->id ?>">
                        <input type="text" name="title" placeholder="Название карточки" maxlength="200">
                        <textarea name="description" rows="2" placeholder="Описание (необязательно)"></textarea>
                        <button type="submit">+ Добавить карточку</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</body>
</html>
