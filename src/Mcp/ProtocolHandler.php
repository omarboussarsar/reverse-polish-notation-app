<?php

declare(strict_types=1);

namespace App\Mcp;

use App\Service\ReversePolishNotation;
use InvalidArgumentException;
use stdClass;

final class ProtocolHandler
{
    private const SERVER_NAME = 'reverse-polish-notation-mcp';
    private const SERVER_VERSION = '1.0.0';
    private const PROTOCOL_VERSION = '2024-11-05';

    public function __construct(
        private readonly ReversePolishNotation $calculator,
    ) {
    }

    public function handle(array $message): ?array
    {
        $method = $message['method'] ?? null;
        $id = $message['id'] ?? null;

        if (!is_string($method)) {
            return $id === null ? null : $this->error($id, -32600, 'Invalid Request');
        }

        return match ($method) {
            'initialize' => $id === null
                ? null
                : $this->success($id, [
                    'protocolVersion' => self::PROTOCOL_VERSION,
                    'capabilities' => [
                        'tools' => new stdClass(),
                    ],
                    'serverInfo' => [
                        'name' => self::SERVER_NAME,
                        'version' => self::SERVER_VERSION,
                    ],
                ]),
            'notifications/initialized' => null,
            'ping' => $id === null ? null : $this->success($id, new stdClass()),
            'tools/list' => $id === null ? null : $this->success($id, [
                'tools' => [$this->toolDefinition()],
            ]),
            'tools/call' => $id === null ? null : $this->handleToolCall($id, $message['params'] ?? []),
            default => $id === null ? null : $this->error($id, -32601, sprintf('Method not found: %s', $method)),
        };
    }

    private function handleToolCall(int|string $id, mixed $params): array
    {
        if (!is_array($params)) {
            return $this->error($id, -32602, 'Invalid params');
        }

        $toolName = $params['name'] ?? null;
        $arguments = $params['arguments'] ?? null;

        if ($toolName !== 'evaluate_rpn' || !is_array($arguments)) {
            return $this->error($id, -32602, 'Invalid params');
        }

        $expression = $arguments['expression'] ?? null;
        if (!is_string($expression)) {
            return $this->error($id, -32602, 'The "expression" argument must be a string.');
        }

        try {
            $result = $this->calculator->evaluate($expression);
        } catch (InvalidArgumentException $exception) {
            return $this->success($id, [
                'content' => [[
                    'type' => 'text',
                    'text' => $exception->getMessage(),
                ]],
                'isError' => true,
            ]);
        }

        return $this->success($id, [
            'content' => [[
                'type' => 'text',
                'text' => json_encode([
                    'expression' => $expression,
                    'result' => $result,
                ], JSON_THROW_ON_ERROR),
            ]],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function toolDefinition(): array
    {
        return [
            'name' => 'evaluate_rpn',
            'description' => 'Evaluates a Reverse Polish Notation expression and returns the numeric result.',
            'inputSchema' => [
                'type' => 'object',
                'properties' => [
                    'expression' => [
                        'type' => 'string',
                        'description' => 'A space-separated Reverse Polish Notation expression, for example "5 1 2 + 4 * + 3 -".',
                    ],
                ],
                'required' => ['expression'],
                'additionalProperties' => false,
            ],
        ];
    }

    private function success(int|string $id, mixed $result): array
    {
        return [
            'jsonrpc' => '2.0',
            'id' => $id,
            'result' => $result,
        ];
    }

    private function error(int|string $id, int $code, string $message): array
    {
        return [
            'jsonrpc' => '2.0',
            'id' => $id,
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ];
    }
}
