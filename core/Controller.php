<?php

/**
 * Abstract base controller for the Application/Business Tier.
 *
 * Provides shared view rendering, redirects, and authentication protection
 * used by concrete controllers.
 */
abstract class Controller
{
    /**
     * Loads a view file and exposes the supplied data as local variables.
     *
     * The method resolves the requested view path, verifies that the file
     * exists, extracts the supplied data for use by the view, and includes
     * the view file. A RuntimeException is thrown when the view cannot be found.
     *
     * @param string $view Relative view name without the .php extension.
     * @param array<string, mixed> $data Data passed from the controller to the view.
     * @return void
     * @throws RuntimeException When the requested view file does not exist.
     */
    protected function view(string $view, array $data = []): void
    {
        $viewPath = __DIR__ . '/../views/' . $view . '.php';

        if (!is_file($viewPath)) {
            throw new RuntimeException('View not found: ' . $view);
        }

        extract($data, EXTR_SKIP);
        require $viewPath;
    }

    /**
     * Redirects the browser to an application-relative path and stops execution.
     *
     * Converts the supplied application path into a complete URL, sends an
     * HTTP Location header, and terminates the current request immediately.
     *
     * @param string $path Application route beginning with /.
     * @return never This method always terminates execution after sending the redirect header.
     */
    protected function redirect(string $path): never
    {
        header('Location: ' . url($path));
        exit;
    }

    /**
     * Requires an authenticated session before allowing a protected action.
     *
     * If no authenticated user ID exists in the session, an appropriate
     * warning message is stored in the session and the user is redirected
     * to the login page. Authenticated requests continue normally.
     *
     * @return void Returns normally for authenticated users; otherwise execution
     *              is terminated by redirecting to the login page.
     */
    protected function requireLogin(): void
    {
        if (empty($_SESSION['user_id'])) {
            $_SESSION['flash'] = [
                'type' => 'warning',
                'message' => 'Please login to continue.'
            ];

            $this->redirect('/login');
        }
    }
}
