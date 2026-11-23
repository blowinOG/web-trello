<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Log;
use App\Metrics;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Card;
use App\Models\User;
use Illuminate\Database\Capsule\Manager as Capsule;

final class BoardController extends AbstractController
{
    public function index(): void
    {
        // Демонстрация работы кастомной сессии в PostgreSQL
        $_SESSION['page_views'] = ((int) ($_SESSION['page_views'] ?? 0)) + 1;
        $flashMessage = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $dbError = null;
        $board = null;
        $columns = [];
        $dbVersion = '';

        $startSelect = microtime(true);
        try {
            $board = $this->ensureDefaultBoard();
            $versionRow = Capsule::selectOne('SELECT version() AS version');
            $dbVersion = (string) ($versionRow->version ?? '');

            $columns = BoardColumn::with('cards')
                ->where('board_id', $board->id)
                ->orderBy('position')
                ->get();

            Metrics::observeDbQuery('select', microtime(true) - $startSelect);
        } catch (\Throwable $e) {
            Metrics::observeDbQuery('select', microtime(true) - $startSelect);
            Metrics::businessError('load_board', 'db_error');
            $dbError = $e->getMessage();
            Log::error('Failed to load board', ['exception' => $dbError]);
        }

        $this->render('board', [
            'board'        => $board,
            'columns'      => $columns,
            'dbError'      => $dbError,
            'dbVersion'    => $dbVersion,
            'pageViews'    => $_SESSION['page_views'],
            'sessionId'    => session_id(),
            'flashMessage' => $flashMessage,
        ]);
    }

    public function createCard(): void
    {
        $title = trim((string) $this->input('title', ''));
        $description = trim((string) $this->input('description', ''));
        $columnId = (int) $this->input('column_id', 0);

        if ($title === '' || mb_strlen($title) > 200 || $columnId <= 0) {
            Metrics::businessError('create_card', 'validation');
            $_SESSION['flash'] = 'Ошибка: укажите название карточки (до 200 символов).';
            $this->redirect('/');
        }

        $start = microtime(true);
        try {
            $column = BoardColumn::find($columnId);
            if ($column === null) {
                Metrics::businessError('create_card', 'column_not_found');
                $_SESSION['flash'] = 'Ошибка: колонка не найдена.';
                $this->redirect('/');
            }

            Card::create([
                'column_id'   => $column->id,
                'title'       => $title,
                'description' => $description !== '' ? $description : null,
                'position'    => (int) $column->cards()->count(),
            ]);

            Metrics::observeDbQuery('insert', microtime(true) - $start);
            Metrics::cardCreated();
            Metrics::noteCreated();
            $_SESSION['flash'] = "Карточка «{$title}» добавлена.";
        } catch (\Throwable $e) {
            Metrics::observeDbQuery('insert', microtime(true) - $start);
            Metrics::businessError('create_card', 'db_error');
            Log::error('Failed to create card', ['exception' => $e->getMessage()]);
            $_SESSION['flash'] = 'Ошибка БД при создании карточки.';
        }

        $this->redirect('/');
    }

    public function moveCard(): void
    {
        $cardId = (int) $this->input('card_id', 0);
        $targetColumnId = (int) $this->input('target_column_id', 0);

        $start = microtime(true);
        try {
            $card = Card::find($cardId);
            $targetColumn = BoardColumn::find($targetColumnId);

            if ($card === null || $targetColumn === null) {
                Metrics::businessError('move_card', 'not_found');
                $_SESSION['flash'] = 'Карточка или колонка не найдена.';
                $this->redirect('/');
            }

            $card->column_id = $targetColumn->id;
            $card->save();

            Metrics::observeDbQuery('update', microtime(true) - $start);
            $_SESSION['flash'] = "Карточка перенесена в «{$targetColumn->title}».";
        } catch (\Throwable $e) {
            Metrics::observeDbQuery('update', microtime(true) - $start);
            Metrics::businessError('move_card', 'db_error');
            Log::error('Failed to move card', ['exception' => $e->getMessage()]);
        }

        $this->redirect('/');
    }

    private function ensureDefaultBoard(): Board
    {
        $user = User::firstOrCreate(
            ['email' => 'student@vyatsu.ru'],
            [
                'name'          => 'Студент ВятГУ',
                'password_hash' => password_hash('secret123', PASSWORD_DEFAULT),
            ]
        );

        $board = Board::firstOrCreate(
            ['title' => 'Учебная доска Trello'],
            [
                'description' => 'Канбан-доска проекта local-zoo',
                'owner_id'    => $user->id,
            ]
        );

        if ($board->columns()->count() === 0) {
            foreach (['Нужно сделать', 'В работе', 'Готово'] as $idx => $colTitle) {
                BoardColumn::create([
                    'board_id' => $board->id,
                    'title'    => $colTitle,
                    'position' => $idx,
                ]);
            }
        }

        return $board;
    }
}
