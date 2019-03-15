<?php

namespace ttm4135\webapp\models;

class User
{
    const INSERT_QUERY = "INSERT INTO users(id, username, password, email, bio, isadmin) VALUES(:id, :username, :password, :email, :bio, :isadmin)";
    const UPDATE_QUERY = "UPDATE users SET username=:username, password=:password, email=:email, bio=:bio, isadmin=:isadmin WHERE id=:id";
    const DELETE_QUERY = "DELETE FROM users WHERE id=:id";
    const FIND_BY_NAME_QUERY = "SELECT * FROM users WHERE username=:username";
    const FIND_BY_ID_QUERY = "SELECT * FROM users WHERE id=:id";

    protected $id = null;
    protected $username;
    protected $password;
    protected $email;
    protected $bio = 'Bio is empty.';
    protected $isAdmin = 0;

    static $app;

    /**
     * Generate a unique number for use as user ID
     */
    static function genUUID()
    {
        return hexdec(bin2hex(openssl_random_pseudo_bytes(4)));
    }

    static function make($id, $username, $password, $email, $bio, $isAdmin )
    {
        $user = new User();
        $user->id = $id;
        $user->username = $username;
        $user->password = $password;
        $user->email = $email;
        $user->bio = $bio;
        $user->isAdmin = $isAdmin;

        return $user;
    }

    static function makeEmpty()
    {
        return new User();
    }

    static function isLegalUsername($username)
    {
        return htmlspecialchars($username) === $username;
    }

    /**
     * Insert or update a user object to db.
     */
    function save()
    {
        if ($this->id === null) {
            $this->id = self::genUUID();
            $query = self::$app->db->prepare(self::INSERT_QUERY);
            $query->bindParam(':username', $this->username);
            $query->bindParam(':password', $this->password);
            $query->bindParam(':email', $this->email);
            $query->bindParam(':bio', $this->bio);
            $query->bindParam(':isadmin', $this->isAdmin);
            $query->bindParam(':id', $this->id);
        } else {
            $query = self::$app->db->prepare(self::UPDATE_QUERY);
            $query->bindParam(':username', $this->username);
            $query->bindParam(':password', $this->password);
            $query->bindParam(':email', $this->email);
            $query->bindParam(':bio', $this->bio);
            $query->bindParam(':isadmin', $this->isAdmin);
            $query->bindParam(':id', $this->id);
        }

        return $query->execute();
    }

    function delete()
    {
        $query = self::$app->db->prepare(self::DELETE_QUERY);
        $query->bindParam(':id', $this->id);
        return $query->execute();
    }

    function getId()
    {
        return $this->id;
    }

    function getUsername()
    {
        return $this->username;
    }

    function getPassword()
    {
        return $this->password;
    }

    function getEmail()
    {
        return $this->email;
    }

    function getBio()
    {
        return $this->bio;
    }

    function isAdmin()
    {
        return $this->isAdmin === "1";
    }

    function setId($id)
    {
        $this->id = $id;
    }

    function setUsername($username)
    {
        $this->username = $username;
    }

    function setPassword($password)
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }

    function setEmail($email)
    {
        $this->email = $email;
    }

    function setBio($bio)
    {
        $this->bio = $bio;
    }

    function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
    }


    /**
     * Get user in db by userid
     *
     * @param string $userid
     * @return mixed User or null if not found.
     */
    static function findById($userid)
    {
        $query = self::$app->db->prepare(self::FIND_BY_ID_QUERY);
        $query->bindParam(':id', $userid);
        if($query->execute()) {
            $row = $query->fetch();
            if($row) {
                return User::makeFromSql($row);
            }
        }

        return null;
    }

    /**
     * Find user in db by username.
     *
     * @param string $username
     * @return mixed User or null if not found.
     */
    static function findByUser($username)
    {
        $query = self::$app->db->prepare(self::FIND_BY_NAME_QUERY);
        $query->bindParam(':username', $username);
        if($query->execute()) {
            $row = $query->fetch();
            if($row) {
                return User::makeFromSql($row);
            }
        }

        return null;
    }


    static function all()
    {
        $query = "SELECT * FROM users";
        $results = self::$app->db->query($query);

        $users = [];

        foreach ($results as $row) {
            $user = User::makeFromSql($row);
            array_push($users, $user);
        }

        return $users;
    }

    static function makeFromSql($row)
    {
        return User::make(
            $row['id'],
            $row['username'],
            $row['password'],
            $row['email'],
            $row['bio'],
            $row['isadmin']
        );
    }

}


User::$app = \Slim\Slim::getInstance();

