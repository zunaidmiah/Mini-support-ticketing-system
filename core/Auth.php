<?php
class Auth {
    private static $tokenFile = 'storage/tokens.json';

    public static function generateToken($userId) {
        $token = bin2hex(random_bytes(16));
        $tokens = self::readTokens();
        $tokens[$token] = [
            'user_id' => $userId,
            'created_at' => time()
        ];
        file_put_contents(self::$tokenFile, json_encode($tokens));
        return $token;
    }

    public static function validateToken($token) {
        $tokens = self::readTokens();
        return isset($tokens[$token]) ? $tokens[$token]['user_id'] : false;
    }

    public static function deleteToken($token) {
        $tokens = self::readTokens();
        unset($tokens[$token]);
        file_put_contents(self::$tokenFile, json_encode($tokens));
    }

    private static function readTokens() {
        if (!file_exists(self::$tokenFile)) {
            file_put_contents(self::$tokenFile, '{}');
        }
        return json_decode(file_get_contents(self::$tokenFile), true);
    }
}