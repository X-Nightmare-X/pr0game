# pr0game.com
> An open source space strategy game

## Battle Engines

* SteemNova_Array - Basic array based calculation. Memory hungry
* SteemNova - Advanced data structures. A bit less Memory hungry, requires PHP-DS
* SteemNova_Julia - Extremly fast and efficient calculation based on programming language Julia, requires [Julia](https://julialang.org/) incl. HTTP and JSON package

## Development

You need Git to work with the repository.

```
git clone https://codeberg.org/pr0game/pr0game.git
```

### ddev

ddev is a local development environment system based on Docker and docker-compose.

#### Prerequisites

* [Docker 18.06+](https://www.docker.com/products/docker-desktop)
* [ddev](https://ddev.readthedocs.io/en/stable/#installation)

#### Usage

To start the project for development purposes, you just need one command:

Since ddev removed phpmyadmin from scratch, you just need run one command (maybe you have to run it after the fallowing ddev start and combine it with ddev restart ):

```
ddev get ddev/ddev-phpmyadmin
```

```
ddev start
```

To install the project for development purposes, you just need one command:

```
ddev composer install
```

Before committing you should run PHP-CS-Fixer:
```
ddev composer exec php-cs-fixer fix
```

The project will be available at: https://pr0game.ddev.site

To enable the installtool you can use the custom function:

```sh
# Enable
ddev installtool
# or
ddev installtool enable

# Disable
ddev installtool disable
```
