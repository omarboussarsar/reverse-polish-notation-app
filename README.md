# Reverse Polish Notation App

A small Symfony 8 application for evaluating Reverse Polish Notation (RPN) expressions through a web UI and a JSON endpoint.

## Stack

- PHP 8.4
- Symfony 8
- Nginx
- Docker Compose
- PHPUnit via `symfony/phpunit-bridge`

## Features

- Web form at `/`
- JSON evaluation endpoint at `/evaluate`
- Async form submission with no full page reload
- Supported operators: `+`, `-`, `*`, `/`, `^`, `!`, `mod`
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

`POST /evaluate` accepts an `expression` form field and returns JSON like:

```json
{
  "expression": "3 4 +",
  "result": 7,
  "error": null
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
