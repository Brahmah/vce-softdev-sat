<?php
/**
 * This handles the routing of the SAT
 *
 * Parts of this implementation is loosely based on the following repo:
 * https://github.com/phprouter/main/blob/main/router.php
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */

// declare required permissions stuff
enum RequiredPermission
{
    case authenticated;
    case superuser;
}

// might as well start the session.
session_start();

// set csrf token
// ---NOTE: this was never actually implemented. I was advised it's out of the scope of this school assignment.
// --- Obviously, this alone isn't enough to prevent CSRF attacks.
try {
    $_SESSION["csrf"] = bin2hex(random_bytes(32));
} catch (Exception $e) {
    $_SESSION["csrf"] = bin2hex(openssl_random_pseudo_bytes(32));
}

// mapping the route methods
function get($route, $path_to_include, array $required_permissions)
{
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        route($route, $path_to_include, $required_permissions);
    }
}

function post($route, $path_to_include, $required_permissions)
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        route($route, $path_to_include, $required_permissions);
    }
}

function put($route, $path_to_include, $required_permissions)
{
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        route($route, $path_to_include, $required_permissions);
    }
}

function patch($route, $path_to_include, $required_permissions)
{
    if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
        route($route, $path_to_include, $required_permissions);
    }
}

function delete($route, $path_to_include, $required_permissions)
{
    if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        route($route, $path_to_include, $required_permissions);
    }
}

function any($route, $path_to_include, $required_permissions)
{
    route($route, $path_to_include, $required_permissions);
}

// actual route function
function route($route, $path_to_include, $required_permissions)
{
    // the app route ignores parent directories as this will likely not be the root of the app.
    // when teacher is marking, they will likely place my project in a subdirectory of htdocs.
    $endpointPathComponents = explode('/API/', $_SERVER['REQUEST_URI']);
    $endpointPath = count($endpointPathComponents) > 1 ? ('/' . $endpointPathComponents[1]) : '/';
    // don't bother doing anything if just error endpoint
    if (in_array($route, ['/404', '/403', '/401'])) {
        assertAuthenticated($required_permissions);
        require_once($path_to_include);
        exit();
    }
    // continue if not error endpoint
    $requestUri = filter_var($endpointPath, FILTER_SANITIZE_URL);
    $requestUri = rtrim($requestUri, '/');
    $requestUri = strtok($requestUri, '?');
    $routeParts = explode('/', $route);
    $requestUrlParts = explode('/', $requestUri);
    array_shift($routeParts);
    array_shift($requestUrlParts);
    if ($routeParts[0] == '' && count($requestUrlParts) == 0) {
        assertAuthenticated($required_permissions);
        require_once($path_to_include);
        exit();
    }
    if (count($routeParts) != count($requestUrlParts)) {
        return;
    }
    $parameters = [];
    for ($routePartIndex = 0; $routePartIndex < count($routeParts); $routePartIndex++) {
        $route_part = $routeParts[$routePartIndex];
        if (preg_match("/^[$]/", $route_part)) {
            $route_part = ltrim($route_part, '$');
            array_push($parameters, $requestUrlParts[$routePartIndex]);
            $$route_part = $requestUrlParts[$routePartIndex];
        } else if ($routeParts[$routePartIndex] != $requestUrlParts[$routePartIndex]) {
            return;
        }
    }
    assertAuthenticated($required_permissions);
    require_once($path_to_include);
    exit();
}

/**
 * @param $required_permissions - an array of required permissions to be checked.
 * @return void - this does not return anything, merely responds to the request with the appropriate error and exits.
 */
function assertAuthenticated($required_permissions)
{
    // Check base authentication if required
    if (in_array(RequiredPermission::authenticated, $required_permissions)) {
        $isSessionProperlySet = isset($_SESSION["authenticated"]) && isset($_SESSION["user_id"]) && isset($_SESSION["username"]) && isset($_SESSION["role"]);
        $isValidSession = false;
        $isSessionExpired = false;
        if ($isSessionProperlySet) {
            $isValidSession = $_SESSION["authenticated"]
                && $_SESSION["user_id"] != null
                && $_SESSION["username"] != null
                && $_SESSION["role"] != null;
            $isSessionExpired = time() - $_SESSION["login_timestamp"] > 1209600000; // 2 weeks
            if ($isSessionExpired) {
                $isValidSession = false;
            }
        }
        if (!$isValidSession) {
            header('Content-Type: application/json');
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode([
                'success' => false,
                'message' => $isSessionExpired ? 'Your session has expired. Please login again.' : 'You are not authenticated.',
                'isSessionExpired' => $isSessionExpired,
            ]);
            exit();
        }
    }
    // check superuser if required
    if (in_array(RequiredPermission::superuser, $required_permissions)) {
        if (!in_array(RequiredPermission::authenticated, $required_permissions)) {
            header('Content-Type: application/json');
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode([
                'success' => false,
                'message' => 'Internal Server Error.',
                'details' => 'Endpoint must be configured to require base authentication before superseding levels.',
            ]);
            exit();
        }
        if ($_SESSION["role"] !== "superuser") {
            header('Content-Type: application/json');
            header('HTTP/1.1 403 Forbidden');
            echo json_encode([
                'success' => false,
                'message' => 'You are not authorized to perform this action.',
                'details' => 'You must be a superuser to perform this action.',
            ]);
            exit();
        }
    }
}