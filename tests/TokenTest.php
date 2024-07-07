<?php
require ('../vendor/autoload.php');

use PHPUnit\Framework\TestCase;
use Firebase\JWT\JWT;

class TokenTest extends TestCase
{
    private $secretKey = '454gasfwe3sf24a23W4233453423dfsdfw';

    public function testTokenGeneration()
    {
        $userId = 'user123';
        $exp = time() + 3600;
        $token = JWT::encode(['userId' => $userId, 'exp' => $exp], $this->secretKey);

        $this->assertNotEmpty($token);

        $decoded = JWT::decode($token, $this->secretKey, ['HS256']);
        $this->assertEquals($userId, $decoded->userId);
        $this->assertEquals($exp, $decoded->exp);
    }

    public function testTokenExpiration()
    {
        $userId = 'user123';
        $exp = time() - 3600; // Token already expired
        $token = JWT::encode(['userId' => $userId, 'exp' => $exp], $this->secretKey);

        $this->expectException(\Firebase\JWT\ExpiredException::class);
        JWT::decode($token, $this->secretKey, ['HS256']);
    }
}
