<?php

namespace App\Exception;

class ErrorCode
{
    public static array $authError = [
        'statusCode' => 101,
        'message' => '계정생성에 실패했습니다.'
    ];
}