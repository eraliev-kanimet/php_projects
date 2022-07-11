<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type: application/json; charset=utf-8');

class App
{
    /**
     * @var array
     */
    private array $routes = [];

    /**
     * @var array
     */
    private array $response = [
        'status' => 200
    ];

    /**
     * @var bool
     */
    private bool $response_active = true;

    /**
     * @var array
     */
    private array $request;

    /**
     * @var array|string[]
     */
    private array $db_config = [
        'db_host' => '',
        'db_port' => '',
        'db_name' => '',
        'db_user' => '',
        'db_password' => ''
    ];

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (isset($config['db'])) {
            $this->db_config['db_host'] = $config['db']['host'];
            $this->db_config['db_port'] = $config['db']['port'];
            $this->db_config['db_name'] = $config['db']['name'];
            $this->db_config['db_user'] = $config['db']['user'];
            $this->db_config['db_password'] = $config['db']['password'];
        }
    }

    /**
     * @return void
     */
    public function run(): void
    {
        $not_found = true;
        krsort($this->routes);
        foreach ($this->routes as $uri => $action)
        {
            if (preg_match('#^' . $uri . '$#', strtok(trim($_SERVER['REQUEST_URI'], '/'), '?'), $data)) {
                if (is_array($action)) {
                    $action[0] = new $action[0];
                }
                if (is_callable($action)) {
                    $this->request = $_REQUEST;
                    if (isset($data[1])) $this->request['id'] = $data[1];

                    call_user_func($action, $this);
                } else {
                    $this->response(['message' => 'Ошибка'], 405);
                }
                $not_found = false;
                break;
            }
        }
        if ($not_found) {
            $this->response(['message' => 'Не найдено'], 404);
        }
        $this->run_response();
    }

    /**
     * @return void
     */
    private function run_response(): void
    {
        print json_encode($this->response);
    }

    /**
     * @param array $data
     * @param int $status
     * @param bool $active
     * @return void
     */
    public function response(array $data, int $status = 200, bool $active = false): void
    {
        if ($this->response_active) {
            $this->response = $data;
            $this->response['status'] = $status;
            http_response_code($status);
        }
        $this->response_active = $active;
    }

    /**
     * @param string $uri
     * @param array|callable $action
     * @return void
     */
    public function get(string $uri, array|callable $action): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') $this->routes[str_replace(':id', '([0-9]+)', $uri)] = $action;
    }

    /**
     * @param string $uri
     * @param array|callable $action
     * @return void
     */
    public function post(string $uri, array|callable $action): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') $this->routes[str_replace(':id', '([0-9]+)', $uri)] = $action;
    }

    /**
     * @param string $uri
     * @param array|callable $action
     * @return void
     */
    public function put(string $uri, array|callable $action): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') $this->routes[str_replace(':id', '([0-9]+)', $uri)] = $action;
    }

    /**
     * @param string $uri
     * @param array|callable $action
     * @return void
     */
    public function delete(string $uri, array|callable $action): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') $this->routes[str_replace(':id', '([0-9]+)', $uri)] = $action;
    }

    /**
     * @param $search
     * @param mixed $default
     * @return mixed
     */
    public function get_request($search, mixed $default = false): mixed
    {
        if (isset($this->request[$search]) && $this->request[$search] != '') {
            return $this->request[$search];
        } else {
            return $default;
        }
    }

    /**
     * @return array
     */
    public function get_request_all(): array
    {
        return $this->request;
    }

    /**
     * @return PDO
     */
    public function db(): PDO
    {
        return new PDO('pgsql:host=' . $this->db_config['db_host'] . ';port=' . $this->db_config['db_port'] . ';dbname=' . $this->db_config['db_name'], $this->db_config['db_user'], $this->db_config['db_password'], []);
    }
}