<?php declare(strict_types=1);

namespace App\Model;

use App\Db\Model;

class User extends Model
{
    const SESSION_NAME='user';

    /**
     * @validation required|email
     * @persist
     */
    public string $email;

    /**
     * @validation required
     * @persist
     */
    public string $passwordHash;

    /**
     * @validation required|boolean
     * @persist
     */
    public string $status;

    /**
     * @param string $value
     */
    public function setPassword(string $value)
    {
        $this->passwordHash = password_hash($value,  PASSWORD_BCRYPT);
    }

    /**
     * @param string $password
     * @return bool
     */
    public function checkPassword(string $password): bool
    {
        return password_verify($password, $this->passwordHash);
    }

    /**
     * @param string $email
     * @return mixed
     * @throws \Exception
     */
    public static function getUserByEmail(string $email): ?User
    {
        return self::hydrateOne("email=:email",['email'=>$email]);
    }

    /**
     * @return User|mixed|null
     * @throws \Exception
     */
    public static function getFromSession()
    {
        if (!isset($_SESSION[self::SESSION_NAME])) {
            return null;
        }
        else {
            return self::getUserByEmail($_SESSION[self::SESSION_NAME]);
        }
    }

    /**
     * @param string $email
     * @param string $password
     * @return User|false|mixed
     * @throws \Exception
     */
    public static function login(string $email, string $password)
    {
        $user = self::getFromSession();
        if (!$user) {
            $user = self::getUserByEmail($email);
            if ($user && $user->checkPassword($password)) {
                $_SESSION[self::SESSION_NAME] = $email;
            }
            else {
                return false;
            }
        }
        return $user;
    }

    /**
     *
     */
    public function logout()
    {
        unset($_SESSION[self::SESSION_NAME]);
    }
}
