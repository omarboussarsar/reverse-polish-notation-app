<?php

declare(strict_types=1);

namespace App\Mcp;

use RuntimeException;

final class StdioServer
{
    /**
     * @param resource $input
     * @param resource $output
     */
    public function run($input, $output, ProtocolHandler $handler): void
    {
        while (($message = $this->readMessage($input)) !== null) {
            $decoded = json_decode($message, true);
            if (!is_array($decoded)) {
                $response = [
                    'jsonrpc' => '2.0',
                    'id' => null,
                    'error' => [
                        'code' => -32700,
                        'message' => 'Parse error',
                    ],
                ];

                $this->writeMessage($output, $response);
                continue;
            }

            $response = $handler->handle($decoded);
            if ($response === null) {
                continue;
            }

            $this->writeMessage($output, $response);
        }
    }

    /**
     * @param resource $stream
     */
    private function readMessage($stream): ?string
    {
        $contentLength = null;

        while (($line = fgets($stream)) !== false) {
            $trimmed = rtrim($line, "\r\n");
            if ($trimmed === '') {
                break;
            }

            if (str_starts_with(strtolower($trimmed), 'content-length:')) {
                $contentLength = (int) trim(substr($trimmed, strlen('content-length:')));
            }
        }

        if ($contentLength === null) {
            return null;
        }

        $body = stream_get_contents($stream, $contentLength);
        if (!is_string($body) || strlen($body) !== $contentLength) {
            throw new RuntimeException('Failed to read the full MCP message body.');
        }

        return $body;
    }

    /**
     * @param resource $stream
     * @param array<string, mixed> $payload
     */
    private function writeMessage($stream, array $payload): void
    {
        $body = json_encode($payload, JSON_THROW_ON_ERROR);
        fwrite($stream, sprintf("Content-Length: %d\r\n\r\n%s", strlen($body), $body));
        fflush($stream);
    }
}
