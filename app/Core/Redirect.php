<?php namespace Dcms\Core;

class Redirect
{
    private $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function back()
    {
        $this->url = $_SERVER['HTTP_REFERER'] ?? '/';
        return $this;
    }

    public function route(string $name, array $options = [])
    {
        $this->url = route($name, $options);
        return $this;
    }

    public function with(string $name, $message)
    {
        $_SESSION[$name] = $message;
        return $this;
    }

    public function __destruct()
    {
        header('Location: ' . $this->url);
        exit;
    }
}