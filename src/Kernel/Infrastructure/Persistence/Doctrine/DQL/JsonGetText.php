<?php

declare(strict_types=1);

namespace Kernel\Infrastructure\Persistence\Doctrine\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

/**
 * Ajoute la prise en charge de la syntaxe PostgreSQL "column ->> 'key'" en DQL.
 *
 * Usage en DQL: JSON_GET_TEXT(p.context, 'identifier_value')
 */
final class JsonGetText extends FunctionNode
{
    private Node $jsonField;
    private Node $jsonKey;

    public function getSql(SqlWalker $sqlWalker): string
    {
        // Ceci génère la syntaxe "table_alias.column_name ->> 'key_name'"
        return
            $this->jsonField->dispatch($sqlWalker) . ' ->> ' .
            $this->jsonKey->dispatch($sqlWalker)
        ;
    }

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER); // Nom de la fonction
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $this->jsonField = $parser->StringPrimary(); // Le champ (ex: p.context)
        $parser->match(TokenType::T_COMMA);
        $this->jsonKey = $parser->ArithmeticPrimary();  // La clé (ex: 'identifier_value')
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}
