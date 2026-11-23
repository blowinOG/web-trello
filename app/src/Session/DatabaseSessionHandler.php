<?php

declare(strict_types=1);

namespace App\Session;

use App\Models\Session;
use SessionHandlerInterface;

final class DatabaseSessionHandler implements SessionHandlerInterface
{
    private int $lifetimeSeconds;

    public function __construct(int $lifetimeSeconds = 3600)
    {
        $this->lifetimeSeconds = $lifetimeSeconds;
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string|false
    {
        $session = Session::find($id);
        if ($session === null) {
            return '';
        }

        if ($session->last_activity < (time() - $this->lifetimeSeconds)) {
            $this->destroy($id);
            return '';
        }

        return (string) $session->payload;
    }

    public function write(string $id, string $data): bool
    {
        Session::updateOrCreate(
            ['id' => $id],
            [
                'payload'       => $data,
                'last_activity' => time(),
            ]
        );

        return true;
    }

    public function destroy(string $id): bool
    {
        Session::where('id', $id)->delete();
        return true;
    }

    public function gc(int $max_lifetime): int|false
    {
        $expiredBefore = time() - $max_lifetime;
        return Session::where('last_activity', '<', $expiredBefore)->delete();
    }
}
