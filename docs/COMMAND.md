bin/console translation:extract --force fr --format xlf20

PHP_CS_FIXER_IGNORE_ENV=1 php tools/bin/php-cs-fixer fix src && php tools/bin/twig-cs-fixer fix templates src

bin/console asset-map:compile

## Création de Structure 

mkdir -p src/Module/Sales/Application/Command
mkdir -p src/Module/Sales/Application/DTO
mkdir -p src/Module/Sales/Application/Event
mkdir -p src/Module/Sales/Application/Projector
mkdir -p src/Module/Sales/Application/Query
mkdir -p src/Module/Sales/Application/ReadModel/Repository
mkdir -p src/Module/Sales/Domain/Event
mkdir -p src/Module/Sales/Domain/Enum
mkdir -p src/Module/Sales/Domain/Exception
mkdir -p src/Module/Sales/Domain/Specification
mkdir -p src/Module/Sales/Domain/Repository
mkdir -p src/Module/Sales/Domain/ValueObject
mkdir -p src/Module/Sales/Infrastructure/Doctrine/Mapping
mkdir -p src/Module/Sales/Infrastructure/Doctrine/Repository
mkdir -p src/Module/Sales/Infrastructure/ReadModel/Mapping
mkdir -p src/Module/Sales/Infrastructure/ReadModel/Repository
mkdir -p src/Module/Sales/Infrastructure/Symfony/DependencyInjection
mkdir -p src/Module/Sales/Infrastructure/Symfony/Resources/config
mkdir -p src/Module/Sales/UI/Controller
mkdir -p src/Module/Sales/UI/Form
mkdir -p src/Module/Sales/UI/Resources/templates/components
mkdir -p src/Module/Sales/UI/Twig/Components


