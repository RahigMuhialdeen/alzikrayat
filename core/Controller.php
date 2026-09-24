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
     * @param string $path Application route beginning with /.
     * @return never This method terminates execution after sending the header.
     */
    protected function redirect(string $path): never
    {
        header('Location: ' . url($path));
        exit;
    }

    /**
     * Requires an authenticated session before allowing a protected action.
     *
     * @return void
     * @throws Never This method redirects unauthenticated users and therefore does not return in that case.
     */
    protected function requireLogin(): void
    {
        if (empty($_SESSION['user_id'])) {
            $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Please login to continue.'];
            $this->redirect('/login');
        }
    }
}
