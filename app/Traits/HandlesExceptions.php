<?php

namespace MiniTwitter\Traits;

use Exception;
use MiniTwitter\Core\Response;

trait HandlesExceptions
{
    private function handleException(Exception $e): void
    {
        $code = $e->getCode() ?: 400;
        error_log($code);
        Response::json(['error' => $e->getMessage()], $code);
    }
}