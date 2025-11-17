<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

declare(strict_types=1);

namespace Lucca\Bundle\CoreBundle\Doctrine\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

class RegExpReplace extends FunctionNode
{
    public $field = null;
    public $pattern = null;
    public $replacement = null;

    /**
     * @throws QueryException
     */
    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER); // REGEXP_REPLACE
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $this->field = $parser->StringPrimary(); // premier param : champ
        $parser->match(TokenType::T_COMMA);

        $this->pattern = $parser->StringPrimary(); // pattern regex
        $parser->match(TokenType::T_COMMA);

        $this->replacement = $parser->StringPrimary(); // replacement
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        return sprintf(
            'REGEXP_REPLACE(%s, %s, %s)',
            $this->field->dispatch($sqlWalker),
            $this->pattern->dispatch($sqlWalker),
            $this->replacement->dispatch($sqlWalker)
        );
    }
}
