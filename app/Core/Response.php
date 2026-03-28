<?php

namespace MiniTwitter\Core;

class Response
{
    public static function json($data, int $status = 200): void
    {
        header("Content-Type: application/json");
        http_response_code($status);
        echo json_encode($data);
        return;
    }
}