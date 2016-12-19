<?php
namespace App\Models;

use Peanut\Phalcon\Pdo\Mysql as Db;

class User
{
    public static function get($userSeq)
    {
        return Db::name('user')->get(
            '
                SELECT
                    *
                FROM
                    user
                WHERE
                    user_seq = :user_seq
            ', [
            ':user_seq' => $userSeq,
        ]);
    }

    public static function getById($userId)
    {
        return Db::name('user')->get(
            '
                SELECT
                    *
                FROM
                    user
                WHERE
                    user_id = :user_id
            ', [
            ':user_id' => $userId,
        ]);
    }

    public static function create($userId, $userName, $userPassword)
    {
        return Db::name('user')->setId(
            '
                INSERT INTO
                    user
                        (user_id, user_name, user_password)
                    VALUES
                        (:user_id, :user_name, :user_password)
            ', [
                ':user_id'       => $userId,
                ':user_name'     => $userName,
                ':user_password' => $userPassword,
            ]
        );
    }

    public static function deleteForceExpire($userSeq)
    {
        return Db::name('user')->get(
            '
                DELETE FROM
                    token
                WHERE
                    user_seq = :user_seq
                AND
                    expire_at < NOW()
            ', [
                ':user_seq' => $userSeq,
                ':token_id' => $tokenId,
            ]
        );
    }

    public static function hasForceExpire($userSeq, $tokenId)
    {
        return Db::name('user')->get(
            '
                SELECT
                    *
                FROM
                    token
                WHERE
                    user_seq = :user_seq
                AND
                    token_id = :token_id
                AND
                    is_force = 1
            ', [
                ':user_seq' => $userSeq,
                ':token_id' => $tokenId,
            ]
        );
    }

    public static function addForceExpire($userSeq, $tokenId)
    {
        return Db::name('user')->set(
            '
                UPDATE
                    token
                SET
                    is_force = 1,
                    force_expire_at = NOW()
                WHERE
                    user_seq = :user_seq
                AND
                    token_id = :token_id
            ', [
                ':user_seq' => $userSeq,
                ':token_id' => $tokenId,
            ]
        );
    }

    public static function addToken($userSeq, $tokenId, $expireAt)
    {
        return Db::name('user')->setId(
            '
                INSERT INTO
                    token
                        (user_seq, token_id, expire_at)
                    VALUES
                        (:user_seq, :token_id, :expire_at)
            ',
            [
                ':user_seq'  => $userSeq,
                ':token_id'  => $tokenId,
                ':expire_at' => $expireAt,
            ]
        );
    }
}
