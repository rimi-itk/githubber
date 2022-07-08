# GitHubber

Start the show:

```sh
docker-compose up --detach
```

Access the site:

```sh
open http://$(docker-compose port nginx 80)
```

## Development

### Coding standards

```sh
docker-compose exec phpfpm composer coding-standards-check
```

```sh
docker-compose exec phpfpm composer coding-st
andards-apply
```

```sh
docker-compose run --rm node yarn --cwd /app install
docker-compose run --rm node yarn --cwd /app coding-standards-check
```

### Code analysis

```sh
docker-compose exec phpfpm composer code-analysis
```
