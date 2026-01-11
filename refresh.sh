#! /bin/bash

echo "Running in production"

echo "Pulling git"
git pull

echo "Rebuilding Authoritative container"
composer dump-autoload

echo "Exporting Schema"
./bin/console api:openapi:export --yaml --output=swagger_docs.yaml

echo "Clearing Cache"
./bin/console cache:clear



echo "Running Build"
npm run build


echo "Syncing Schema"
npx openapi-typescript ./swagger_docs.yaml -o ./resources/typescript/schema/schema.d.ts

echo "All done. Now restart PHP"
