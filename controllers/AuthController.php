<?php

/**
 * Handles user registration, authentication, session state, and logout.
 *
 * Authentication is implemented with PHP sessions and Bcrypt password hashing.
 * A seven-day browser cookie is also maintained to display the previous
 * successful login timestamp on the login page.
 */
class AuthController extends Controller
{
    /** @var User User model used for account and credential operations. */
    private User $users;

    /**
     * Creates the controller and initializes its User model dependency.
     *
     * @return void
     */
    public function __construct()
    {
        $this->users = new User();
    }

    /**
     * Displays the login form.
     *
     * Generates a CSRF token for the login form if one does not already exist.
     *
     * @return void
     */
    public function loginForm(): void
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $this->view('auth/login', [
            'title' => 'Login',
            'csrf_token' => $_SESSION['csrf_token']
        ]);
    }

    /**
     * Displays the registration form.
     *
     * @return void
     */
    public function registerForm(): void
    {
        $this->view('auth/register', ['title' => 'Create Account']);
    }

    /**
     * Validates registration input, creates a new user, and starts a session.
     *
     * Names are restricted to letters, the email must be valid and unique,
     * and the password is stored only after Bcrypt hashing.
     *
     * @return void
     */
    public function register(): void
    {
        $data = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'email' => strtolower(trim($_POST['email'] ?? '')),
            'password' => $_POST['password'] ?? '',
            'location' => trim($_POST['location'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'occupation' => trim($_POST['occupation'] ?? ''),
        ];

        $errors = [];

        // Server-side validation is required even when browser validation is enabled.
        if (!preg_match('/^[A-Za-z]{1,50}$/', $data['first_name'])) {
            $errors[] = 'First name must contain letters only (max 50).';
        }

        if (!preg_match('/^[A-Za-z]{1,50}$/', $data['last_name'])) {
            $errors[] = 'Last name must contain letters only (max 50).';
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }

        if (strlen($data['password']) < 8) {
            $errors[] = 'Password must contain at least 8 characters.';
        }

        if (strlen($data['location']) > 100 || strlen($data['occupation']) > 100) {
            $errors[] = 'Location and occupation must be at most 100 characters.';
        }

        if (strlen($data['description']) > 5000) {
            $errors[] = 'Description must be at most 5000 characters.';
        }

        if (!$errors && $this->users->emailExists($data['email'])) {
            $errors[] = 'This email is already registered.';
        }

        if ($errors) {
            $oldData = $data;
            $oldData['password'] = '';
            $_SESSION['old'] = $oldData;
            $_SESSION['errors'] = $errors;
            $this->redirect('/register');
        }

        // Never store the raw password in the database.
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        $userId = $this->users->create($data);

        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $data['first_name'];

        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'Account created successfully. Welcome to Alzikrayat!'
        ];

        $this->redirect('/gallery');
    }

    /**
     * Authenticates a user and establishes the session state.
     *
     * The previous last-login cookie is copied into the session for display,
     * then a new seven-day cookie is issued for the current successful login.
     *
     * @return void
     */
    public function login(): void
    {
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $errors = [];

        // Validate the email before querying the database with it.
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }

        if ($password === '' || strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }

        $user = null;

        if (!$errors) {
            $user = $this->users->findByEmail($email);

            if (!$user || !password_verify($password, $user['password'])) {
                $errors[] = 'Invalid email or password.';
            }
        }

        if ($errors) {
            $_SESSION['errors'] = $errors;
            $this->redirect('/login');
        }

        // Regenerate the session ID after successful authentication to prevent session fixation.
        session_regenerate_id(true);

        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = $user['first_name'];

        // Preserve the previous cookie value so the login page can show the last successful login.
        $lastLogin = $_COOKIE['alzikrayat_last_login'] ?? null;

        if ($lastLogin) {
            $_SESSION['last_login_context'] = $lastLogin;
        }

        $timezone = new DateTimeZone('Africa/Khartoum');
        $now = new DateTime('now', $timezone);
        $timestamp = $now->format('Y-m-d H:i:s');

        // The cookie is valid for exactly seven days and is protected from JavaScript access.
        setcookie('alzikrayat_last_login', $timestamp, [
            'expires' => time() + (7 * 24 * 60 * 60),
            'path' => '/',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'Welcome back, ' . $user['first_name'] . '!'
        ];

        $this->redirect('/gallery');
    }

    /**
     * Destroys the authenticated session and starts a fresh session for the logout message.
     *
     * @return void
     */
    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'] ?? '',
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        session_start();

        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'You have been logged out.'
        ];

        $this->redirect('/login');
    }
}
