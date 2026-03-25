# Reverse Polish Notation App

A small Symfony 7.4 application for evaluating Reverse Polish Notation (RPN) expressions through a web UI and a JSON endpoint.

## Stack

- PHP 8.5
- Symfony 7.4
- Nginx
- Docker Compose
- PHPUnit via `symfony/phpunit-bridge`

## Features

- Web form at `/`
- JSON evaluation endpoint at `/evaluate`
- MCP server over `stdio` for AI clients
- Async form submission with no full page reload
- Supported operators: `+`, `-`, `*`, `/`, `^`, `!`, `mod`
- Parenthesized grouping support for RPN sub-expressions
- Error handling for empty input, invalid expressions, unknown operators, division by zero, and integer overflow

## Requirements

- Docker Engine
- Docker Compose

## Local Run

Start the app:

```sh
make up
```

Install PHP dependencies inside the running PHP container:

```sh
make install
```

The app is available at `http://localhost:8080`.

## Usage

Open `http://localhost:8080` and enter a space-separated RPN expression such as:

```text
5 1 2 + 4 * + 3 -
```

Example expressions:

- `3 4 +`
- `9 3 /`
- `2 3 ^`
- `5 !`
- `7 3 mod`
- `( 2 3 + ) 4 *`

`POST /evaluate` accepts an `expression` form field and returns JSON like:

```json
{
  "expression": "3 4 +",
  "result": 7,
  "error": null
}
```

## MCP Server

The project also exposes the calculator as an MCP server over `stdio`.

Start it locally:

```sh
php bin/mcp-server.php
```

The server exposes one tool:

- `evaluate_rpn`: evaluates a space-separated RPN expression passed as the `expression` argument, including grouped sub-expressions like `( 2 3 + ) 4 *`

Example client config:

```json
{
  "mcpServers": {
    "rpn": {
      "command": "php",
      "args": ["bin/mcp-server.php"],
      "cwd": "${workspaceFolder}"
    }
  }
}
```

Once connected from an MCP-capable AI client, you can ask it to call `evaluate_rpn` with inputs like `5 1 2 + 4 * + 3 -` or `8 5 +` (which returns `13`).

Example call:

```json
{
  "tool": "evaluate_rpn",
  "arguments": {
    "expression": "8 5 +"
  }
}
```

Example response:

```json
{
  "expression": "8 5 +",
  "result": 13,
  "error": null
}
```

VS Code example:

```json
{
  "servers": {
    "rpn": {
      "type": "stdio",
      "command": "docker",
      "args": [
        "compose",
        "--project-directory",
        "${workspaceFolder}",
        "-f",
        "${workspaceFolder}/docker-compose.yml",
        "exec",
        "-T",
        "php",
        "php",
        "/var/www/html/bin/mcp-server.php"
      ]
    }
  }
}
```

## Make Targets

```sh
make up
make down
make build
make logs
make sh
make composer ARGS=install
make console ARGS=cache:clear
make test
```

## Tests

Run the test suite:

```sh
make test
```

## Stop

```sh
make down
```
