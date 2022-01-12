<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;


/**
 * Class Name: ErrorLogs
 *
 * @package App\Command
 */
class ErrorLogs extends Command
{

    private $log_path = "./log/";

    private $log_fileName = '';


    /**
     * construct
     *
     */
    public function __construct()
    {
        $this->log_fileName = $this->log_path.date('Y-m-d')."-errors.log";
    }

    /**
     * @param $errorMessage  string
     *
     * @return int
     */
    public function errorLog(string $errorMessage) :int
    {
        $logger = new Logger('ConsoleApp');

        $logger->pushHandler(new StreamHandler($this->log_fileName, Logger::DEBUG));
        $logger->error($errorMessage);

        return true;
    }
}