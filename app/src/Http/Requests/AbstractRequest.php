<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Http\Responses\AbstractResponse;
use App\Http\Validation\Validator;

abstract class AbstractRequest
{
    /** @var array<string, mixed> */
    protected array $rawData;

    /** @var array<string, mixed> */
    protected array $validatedData = [];

    /** @param array<string, mixed>|null $data */
    public function __construct(?array $data = null)
    {
        if ($data !== null) {
            $this->rawData = $data;
        } else {
            $json = json_decode(file_get_contents('php://input') ?: '', true);
            $this->rawData = array_merge($_GET, $_POST, is_array($json) ? $json : []);
        }
    }

    /**
     * Правила валидации входных параметров.
     * @return array<string, string>
     */
    abstract protected function rules(): array;

    /**
     * Дополнительная проверка бизнес-логики (переопределяется при необходимости).
     */
    protected function validateLogic(): void
    {
    }

    /**
     * Выполнение полезной нагрузки запроса.
     */
    abstract protected function handle(): AbstractResponse;

    /**
     * Полный пайплайн: валидация параметров -> проверка логики -> выполнение.
     */
    public function execute(): AbstractResponse
    {
        $this->validatedData = Validator::validate($this->rawData, $this->rules());
        $this->validateLogic();

        return $this->handle();
    }

    protected function get(string $key, mixed $default = null): mixed
    {
        return $this->validatedData[$key] ?? $this->rawData[$key] ?? $default;
    }
}
