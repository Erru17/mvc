<?php

declare(strict_types=1);

/**
 * Functions.
 */


/**
 * Get the route path representing the page being requested.
 *
 * @return string with the route path requested.
 */
function getRoutePath(): string
{
    $offset = strlen(dirname($_SERVER["SCRIPT_NAME"]));
    $path   = substr($_SERVER["REQUEST_URI"], $offset);

    return $path;
}



/**
 * Render the view and return its rendered content.
 *
 * @param string $template to use when rendering the view.
 * @param array  $data     send to as variables to the view.
 *
 * @return string with the route path requested.
 */
function renderView(
    string $template,
    array $data = []
): string {
    extract($data);

    ob_start();
    require INSTALL_PATH . "/view/$template";
    $content = ob_get_contents();
    ob_end_clean();

    return ($content ? $content : "");
}



/**
 * Send a response to the client.
 *
 * @param int    $status   HTTP status code to send to client.
 *
 * @return void
 */
function sendResponse(string $body, int $status = 200): void
{
    http_response_code($status);
    echo $body;
}



/**
 * Redirect to an url.
 *
 * @param string $url where to redirect.
 *
 * @return void
 */
function redirectTo(string $url): void
{
    http_response_code(200);
    header("Location: $url");
}





/**
 * Get the base url from the request, relative to the htdoc/ directory.
 *
 * @return string as the base url.
 */
function getBaseUrl()
{
    static $baseUrl = null;

    if ($baseUrl) {
        return $baseUrl;
    }

    $scriptName = rawurldecode($_SERVER["SCRIPT_NAME"]);
    $path = rtrim(dirname($scriptName), "/");

    // Prepare to create baseUrl by using currentUrl
    $parts = parse_url(getCurrentUrl());

    // Build the base url from its parts
    $siteUrl = "{$parts["scheme"]}://{$parts["host"]}"
        . (isset($parts["port"])
            ? ":{$parts["port"]}"
            : "");
    $baseUrl = $siteUrl . $path;

    return $baseUrl;
}



/**
 * Get the current url of the request.
 *
 * @return string as current url.
 */
function getCurrentUrl(): string
{
    $scheme = $_SERVER["REQUEST_SCHEME"];
    $server = $_SERVER["SERVER_NAME"];

    $port  = $_SERVER["SERVER_PORT"];
    $port  = ($port === "80")
        ? ""
        : (($port === 443 && $_SERVER["HTTPS"] === "on")
            ? ""
            : ":" . $port);

    $uri = rtrim(rawurldecode($_SERVER["REQUEST_URI"]), "/");

    $url  = htmlspecialchars($scheme) . "://";
    $url .= htmlspecialchars($server)
        . $port . htmlspecialchars(rawurldecode($uri));

    return $url;
}



/**
 * Destroy the session.
 *
 * @return void
 */
function destroySession(): void
{
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();
}

function resetScore(): void
{
    if (!isset($_SESSION["win"])) {
        $_SESSION["win"] = 0;
    }
    if (!isset($_SESSION["lose"])) {
        $_SESSION["lose"] = 0;
    }
}