<?php
/*
 * This handles the routing of the SAT
 * This implementation is loosely based on the following repo:
 * https://github.com/phprouter/main/blob/main/router.php
 * @author: Bashir Rahmah
*/

// might as well start the session.
session_start();
// mapping the route methods
function get($route, $path_to_include)
{
    if ($_SERVER['REQUEST_METHOD'] == 'GET')
    {
        route($route, $path_to_include);
    }
}
function post($route, $path_to_include)
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        route($route, $path_to_include);
    }
}
function put($route, $path_to_include)
{
    if ($_SERVER['REQUEST_METHOD'] == 'PUT')
    {
        route($route, $path_to_include);
    }
}
function patch($route, $path_to_include)
{
    if ($_SERVER['REQUEST_METHOD'] == 'PATCH')
    {
        route($route, $path_to_include);
    }
}
function delete($route, $path_to_include)
{
    if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
    {
        route($route, $path_to_include);
    }
}
function any($route, $path_to_include)
{
    route($route, $path_to_include);
}

function route($route, $path_to_include)
{
    // the app route ignores parent directories as this will likely not be the root of the app.
    // when teacher is marking, they will likely place my project in a subdirectory of htdocs.
    $endpointPathComponents = explode('/API/', $_SERVER['REQUEST_URI']);
    $endpointPath = count($endpointPathComponents) > 1 ? ('/' . $endpointPathComponents[1]) : '/';
    // don't bother doing anything if just error endpoint
    if (in_array($route, ['/404', '/403', '/401']))
    {
        include_once($path_to_include);
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
    if ($routeParts[0] == '' && count($requestUrlParts) == 0)
    {
        include_once($path_to_include);
        exit();
    }
    if (count($routeParts) != count($requestUrlParts))
    {
        return;
    }
    $parameters = [];
    for ($routePartIndex = 0;$routePartIndex < count($routeParts);$routePartIndex++)
    {
        $route_part = $routeParts[$routePartIndex];
        if (preg_match("/^[$]/", $route_part))
        {
            $route_part = ltrim($route_part, '$');
            array_push($parameters, $requestUrlParts[$routePartIndex]);
            $$route_part = $requestUrlParts[$routePartIndex];
        }
        else if ($routeParts[$routePartIndex] != $requestUrlParts[$routePartIndex])
        {
            return;
        }
    }
    include_once($path_to_include);
    exit();
}