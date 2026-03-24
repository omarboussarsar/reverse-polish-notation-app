<?php

declare(strict_types=1);

namespace App\Tests\Mcp;

use App\Mcp\ProtocolHandler;
use App\Service\ReversePolishNotation;
use PHPUnit\Framework\TestCase;

final class ProtocolHandlerTest extends TestCase
{
    public function testInitializeReturnsServerMetadata(): void
    {
        $handler = new ProtocolHandler(new ReversePolishNotation());

        $response = $handler->handle([
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'initialize',
            'params' => [
                'protocolVersion' => '2024-11-05',
                'capabilities' => [],
                'clientInfo' => ['name' => 'test-client', 'version' => '1.0.0'],
            ],
        ]);

        self::assertSame('2.0', $response['jsonrpc']);
        self::assertSame(1, $response['id']);
        self::assertSame('2024-11-05', $response['result']['protocolVersion']);
        self::assertSame('reverse-polish-notation-mcp', $response['result']['serverInfo']['name']);
        self::assertArrayHasKey('tools', $response['result']['capabilities']);
    }

    public function testToolsListReturnsEvaluateTool(): void
    {
        $handler = new ProtocolHandler(new ReversePolishNotation());

        $response = $handler->handle([
            'jsonrpc' => '2.0',
            'id' => 2,
            'method' => 'tools/list',
        ]);

        self::assertSame('evaluate_rpn', $response['result']['tools'][0]['name']);
        self::assertSame(['expression'], $response['result']['tools'][0]['inputSchema']['required']);
    }

    public function testToolCallReturnsStructuredJsonText(): void
    {
        $handler = new ProtocolHandler(new ReversePolishNotation());

        $response = $handler->handle([
            'jsonrpc' => '2.0',
            'id' => 3,
            'method' => 'tools/call',
            'params' => [
                'name' => 'evaluate_rpn',
                'arguments' => [
                    'expression' => '3 4 +',
                ],
            ],
        ]);

        self::assertSame(3, $response['id']);
        self::assertFalse($response['result']['isError'] ?? false);
        self::assertSame(
            '{"expression":"3 4 +","result":7}',
            $response['result']['content'][0]['text'],
        );
    }

    public function testToolCallReturnsToolErrorForInvalidExpression(): void
    {
        $handler = new ProtocolHandler(new ReversePolishNotation());

        $response = $handler->handle([
            'jsonrpc' => '2.0',
            'id' => 4,
            'method' => 'tools/call',
            'params' => [
                'name' => 'evaluate_rpn',
                'arguments' => [
                    'expression' => '',
                ],
            ],
        ]);

        self::assertTrue($response['result']['isError']);
        self::assertSame('Expression cannot be empty.', $response['result']['content'][0]['text']);
    }

    public function testUnknownMethodReturnsJsonRpcError(): void
    {
        $handler = new ProtocolHandler(new ReversePolishNotation());

        $response = $handler->handle([
            'jsonrpc' => '2.0',
            'id' => 5,
            'method' => 'unknown/method',
        ]);

        self::assertSame(-32601, $response['error']['code']);
    }
}
