<?php
namespace App\Traits;

use App\Models\User as Usermodel;

trait Auth
{
    public $key           = 'secret';
    public $alg           = 'HS256';
    public $expire        = '30 minutes';
    public $refreshExpire = '1 day';
    public $serverName    = 'local.com';

    public function getToken($userSeq, $scopes = [], $payload = [])
    {
        $Issuer        = $this->serverName;
        $tokenId       = $this->uuid();
        $issuedAt      = time();
        $notBefore     = $issuedAt;

        $expire        = $this->expire;
        $refreshExpire = $this->refreshExpire;

        $expire        = strtotime('+'.$expire, $notBefore);
        $refreshExpire = strtotime('+'.$refreshExpire, $notBefore);

        $token = [
            'iat' => $issuedAt,  // Issued at: time when the token was generated
            'jti' => $tokenId,   // Json Token Id(unique identifier)
            'iss' => $Issuer,    // Issuer
            'nbf' => $notBefore, // Not before
            'exp' => $expire,    // Expire
            'uid' => $userSeq,   // user id
            'scp' => $scopes,    // scope
            'pld' => $payload,   // payload
        ];

        $refreshtoken = [
            'uid' => $userSeq,   // user id
            'iat' => $issuedAt,  // Issued at: time when the token was generated
            'jti' => $tokenId,   // Json Token Id(unique identifier)
            'iss' => $Issuer,    // Issuer
            'nbf' => $notBefore, // Not before
            'exp' => $expire,    // Expire
        ];

        return [
            $token,
            $refreshtoken,
        ];
    }

    public function validate($authorization)
    {
        list($token)   = sscanf($authorization, 'Bearer %s');

        if ($token) {
            try {
                $decoded = object2array($this->auth::decode($token, $this->key, [$this->alg]));

                if ($decoded) {
                    $userSeq = $decoded['uid'];

                    //ForceExpire 조사
                    if (Usermodel::hasForceExpire($userSeq, $decoded['jti'])) {
                        return $this->response->content([
                            'status'  => '403',
                            'message' => 'The token has been expired',
                        ]);
                    } else {
                        return $this->response->content([
                            'status'  => '200',
                            'message' => 'Success',
                        ]);
                    }
                } else {
                    return $this->response->content([
                        'status'  => '401',
                        'message' => 'Unauthorized',
                    ]);
                }
            } catch (\DomainException $e) {
                // Syntax error, malformed JSON
                return $this->response->content([
                    'status'  => '500',
                    'message' => 'Failed to verify token', //$e->getMessage(),
                ]);
            } catch (\PDOException $e) {
                return $this->response->content([
                    'status'  => '500',
                    'message' => $e->getMessage(),
                ]);
            } catch (\Exception $e) {
                return $this->response->content([
                    'status'  => '500',
                    'message' => $e->getMessage(),
                ]);
            }
        } else {
            return $this->response->content([
                'status'  => '400',
                'message' => 'Failed to extract the token from the header.',
            ]);
        }
    }

    public function login($userId, $userPassword)
    {
        try {
            $user = Usermodel::getById($userId);

            if ($user) {
                if (password_verify($userPassword, $user['user_password'])) {
                    $scopes  = [];
                    $payload = [];

                    list($token, $refreshToken) = $this->getToken($user['user_seq'], $scopes, $payload);

                    // 새로운 jti 입력
                    Usermodel::addToken($user['user_seq'], $token['jti'], date('Y-m-d H:i:s', $token['exp']));

                    return $this->response->content([
                        'status'  => '200',
                        'message' => 'success',
                        'results' => [
                            'token'        => $this->auth::encode($token, $this->key, $this->alg),
                            'refreshtoken' => $this->auth::encode($refreshToken, $this->key, $this->alg),
                            'refresh'      => false,
                        ],
                    ]);
                } else {
                    return $this->response->content([
                        'status'  => '401',
                        'message' => 'Unauthorized',
                    ]);
                }
            } else {
                return $this->response->content([
                    'status'  => '404',
                    'message' => 'User could not be found.',
                ]);
            }
        } catch (\PDOException $e) {
            return $this->response->content([
                'status'  => '500',
                'message' => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            return $this->response->content([
                'status'  => '500',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function refresh($refresh)
    {
        try {
            // 리프레시 토큰의 만료시간은 토큰의 만료시간과 같다. 그러므로 refreshExpire타임 만큼 여지를 두고 체크
            $this->auth::$leeway = strtotime('+'.$this->refreshExpire, 0);
            $decoded             = object2array($this->auth::decode($refresh, $this->key, [$this->alg]));

            if (true === isset($decoded['uid'])) {
                $userSeq = $decoded['uid'];
                $user    = Usermodel::get($userSeq);

                if ($user) {
                    //time expired된 token 삭제
                    Usermodel::deleteForceExpire($userSeq);
                    //ForceExpire 조사
                    if (Usermodel::hasForceExpire($userSeq, $decoded['jti'])) {
                        return $this->response->content([
                            'status'  => '403',
                            'message' => 'The token has been expired',
                        ]);
                    } else {
                        list($newToken, $refreshToken) = $this->getToken($user['user_seq'], []);

                        // 리프레시 가능 기간내에 기존토큰으로 새 토큰을 교환받을수 있다. 새토큰을 받아도 expire 시간은 최초 발급 시점이다.
                        $newToken['iat']     = $decoded['iat'];
                        $newToken['nbf']     = $decoded['nbf'];
                        $newToken['exp']     = $decoded['exp'];

                        $refreshToken['iat'] = $decoded['iat'];
                        $refreshToken['nbf'] = $decoded['nbf'];
                        $refreshToken['exp'] = $decoded['exp'];

                        Usermodel::addForceExpire($userSeq, $decoded['jti']);
                        Usermodel::addToken($user['user_seq'], $newToken['jti'], date('Y-m-d H:i:s', $newToken['exp']));

                        return $this->response->content([
                            'status'  => '200',
                            'message' => 'success',
                            'results' => [
                                'token'        => $this->auth::encode($newToken, $this->key, $this->alg),
                                'refreshtoken' => $this->auth::encode($refreshToken, $this->key, $this->alg),
                                'refresh'      => true,
                            ],
                        ]);
                    }
                } else {
                    return $this->response->content([
                        'status'  => '404',
                        'message' => 'User could not be found.',
                    ]);
                }
            } else {
                return $this->response->content([
                    'status'  => '400',
                    'message' => 'uid does not exist within the refresh token.',
                ]);
            }
        } catch (\DomainException $e) {
            // Syntax error, malformed JSON
           return $this->response->content([
               'status'  => '500',
               'message' => 'Failed to verify token', //$e->getMessage(),
           ]);
        } catch (\PDOException $e) {
            return $this->response->content([
               'status'  => '500',
               'message' => $e->getMessage(),
           ]);
        } catch (\Exception $e) {
            return $this->response->content([
                'status'  => '500',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function register($userId, $userName, $userPassword)
    {
        try {
            $user = Usermodel::getById($userId);

            if ($user) {
                return $this->response->content([
                    'status'  => '409',
                    'message' => 'Account Already Exists',
                ]);
            } else {
                $hashAndSalt = password_hash($userPassword, PASSWORD_BCRYPT);
                Usermodel::create($userId, $userName, $hashAndSalt);

                return $this->response->content([
                    'status'  => '201',
                    'message' => 'Success',
                ]);
            }
        } catch (\PDOException $e) {
            return $this->response->content([
                'status'  => '500',
                'message' => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            return $this->response->content([
                'status'  => '500',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function uuid()
    {
        return uuid_create(UUID_TYPE_TIME);
    }
}
