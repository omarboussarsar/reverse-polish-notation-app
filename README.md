# Reverse Polish Notation App

Simple Symfony app for evaluating Reverse Polish Notation (RPN) expressions.

## Requirements
- Docker + Docker Compose (recommended)
- Optional local setup: PHP 8.4+, Composer

## Install
```sh
make build
make install
```

## Run
```sh
make up
```

App is served at `http://localhost:8080`.

To stop containers:
```sh
make down
```

## Tests
```sh
make test
```
