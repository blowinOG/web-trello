<?php
$h = static fn (string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход — Trello Board</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 400px; margin: 4rem auto; padding: 0 1rem; background: #f4f5f7; }
        .box { background: #fff; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
        h1 { font-size: 1.3rem; margin-top: 0; }
        label { display: block; font-size: 0.9rem; margin-bottom: 0.3rem; font-weight: 600; }
        input { width: 100%; box-sizing: border-box; padding: 0.5rem; margin-bottom: 0.8rem; border: 1px solid #ccc; border-radius: 4px; }
        .err { color: #b00020; font-size: 0.85rem; margin-bottom: 0.8rem; }
        button { width: 100%; padding: 0.6rem; border: 0; border-radius: 4px; background: #2563eb; color: #fff; font-weight: 600; cursor: pointer; }
        .links { margin-top: 1rem; text-align: center; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Вход в Trello</h1>
        <?php foreach ($errors as $error): ?>
            <div class="err"><?= $h($error) ?></div>
        <?php endforeach; ?>
        <form method="post" action="/login">
            <label>Email</label>
            <input type="email" name="email" value="<?= $h((string) ($old['email'] ?? '')) ?>" required>
            <label>Пароль</label>
            <input type="password" name="password" required>
            <button type="submit">Войти</button>
        </form>
        <div class="links">
            Нет аккаунта? <a href="/register">Зарегистрироваться</a> | <a href="/">На доску</a>
        </div>
    </div>
</body>
</html>
