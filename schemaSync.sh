#! /bin/bash
docker compose --file=../dnd-puzzles-environment/docker-compose.yml exec dnd-puzzles-app bin/console api:openapi:export --yaml --output=swagger_docs.yaml
npx openapi-typescript ./swagger_docs.yaml -o ./resources/typescript/schema/schema.d.ts
