<?php

namespace Onion\Infrastructure\Exceptions;

class PDOException extends \PDOException implements \Stringable
{
    public static function throw(\PDO $pdo): void
    {
        $errInfo = $pdo->errorInfo();
        throw new self("[$errInfo[0] $errInfo[1]]: $errInfo[2]", $errInfo[1]);
    }
}
