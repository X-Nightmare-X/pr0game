# pr0game.com
> An open source space strategy game

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

To run the project for development purposes, you just need one command:

```
ddev start
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
