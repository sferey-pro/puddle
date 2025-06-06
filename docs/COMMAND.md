bin/console translation:extract --force fr --format xlf20

PHP_CS_FIXER_IGNORE_ENV=1 php tools/bin/php-cs-fixer fix src
PHP_CS_FIXER_IGNORE_ENV=1 php tools/bin/twig-cs-fixer fix templates src

bin/console asset-map:compile
