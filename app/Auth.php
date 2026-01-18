<?php
namespace App;

use App\Exceptions\AuthException;
use App\Repositories\TeacherRepository;
use App\Traits\Singleton;

final class Auth
{
    use Singleton;

    public readonly int $id;
    public readonly string $firstName;
    public readonly string $lastName;
    public readonly string $email;
    public readonly string $contactNumber;
    public readonly string $address;
    public readonly int $authExpiry;

    protected static function initInstance(Auth $instance): void
    {
        $instance->id = (int) $_SESSION['id'];
        $instance->firstName = $_SESSION['firstName'];
        $instance->lastName = $_SESSION['lastName'];
        $instance->email = $_SESSION['email'];
        $instance->contactNumber = $_SESSION['contactNumber'];
        $instance->address = $_SESSION['address'];
        $instance->authExpiry = $_SESSION['authExpiry'];
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public static function isLoggedIn()
    {
        return isset($_SESSION['id']);
    }

    /**
     * Summary of login
     * @param array{email:string, password:string} $formData
     * @param mixed $redirect
     * @throws AuthException
     * @return void
     */
    public static function login(array $formData, ?string $redirect = null)
    {
        $email = trim($formData['email']);
        $password = $formData['password'];

        $teacherRepo = TeacherRepository::getInstance();
        $teacher = $teacherRepo->findByEmail($email);

        if (!$teacher) {
            throw new AuthException('User with this email does not exist.');
        }

        if (!password_verify($password, $teacher->passwordHash)) {
            throw new AuthException('Email or Password is incorrect.');
        }

        $_SESSION['id'] = $teacher->id;
        $_SESSION['firstName'] = $teacher->firstName;
        $_SESSION['lastName'] = $teacher->lastName;
        $_SESSION['email'] = $teacher->email;
        $_SESSION['contactNumber'] = $teacher->contactNumber;
        $_SESSION['address'] = $teacher->address;
        $_SESSION['authExpiry'] = time() + 3600;

        if ($redirect) {
            header('Location: ' . $redirect);
            exit(0);
        }
    }

    public static function logout(?string $redirect = null)
    {
        session_unset();
        session_destroy();

        if ($redirect) {
            header('Location: ' . $redirect);
            exit(0);
        }
    }

    // TODO: Currently not validating user input, will add soon.
    /**
     * Summary of signup
     * @param array{firstName: string, lastName: string, email: string, contactNumber: string, address: string, password: string} $formData
     * @param ?string $redirect
     * @throws AuthException
     * @return void
     */
    public static function signup(
        array $formData,
        ?string $redirect = null
    ) {
        $teacherRepo = TeacherRepository::getInstance();

        $email = $formData['email'];

        // check for existing user with this email.
        $existingTeacher = $teacherRepo->findByEmail($email);
        if ($existingTeacher) {
            throw new AuthException('A user with this email already exists.');
        }

        $password = $formData['password'];
        $passwordHash = static::hashPassword($password);

        $teacherData = [
            'firstName' => $formData['firstName'],
            'lastName' => $formData['lastName'],
            'email' => $formData['email'],
            'contactNumber' => $formData['contactNumber'] ?? null,
            'address' => $formData['address'] ?? null,
            'passwordHash' => $passwordHash,
        ];

        $teacherRepo->create($teacherData);

        if ($redirect) {
            header('Location: ' . $redirect);
            exit(0);
        }
    }
}