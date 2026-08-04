<?php
namespace App\Debug;

use Bitrix\Main\Diag\ExceptionHandlerFormatter;
use Bitrix\Main\Diag\FileExceptionHandlerLog;

class Log extends FileExceptionHandlerLog 
{

    /**
     * Запись в лог
     *
     * @param           $message
     * @param   false   $clear
     * @param   string  $fileName
     * @param   true    $timeVersion
     *
     * @return void
     */
    public static function addLog($message, bool $clear = false, string $fileName = 'custom', $timeVersion = true) : void  {
        $logFile = $_SERVER["DOCUMENT_ROOT"].'/local/logs/'.$fileName;
        
        if ($timeVersion) {
            $logFile .= '_'.date("d.m.Y");
        }
        $logFile .= '.log';

        $vMessage = date("d.m.Y H:i:s");
        if ($message!="") {
            $vMessage .= "\n";
            $vMessage .= print_r($message, true);
            $vMessage .= "\n";
            $vMessage .= "---";
        }
        $vMessage .= "\n";


        if ($clear){
            file_put_contents($logFile, $vMessage);
        }
        else {
            file_put_contents($logFile, $vMessage, FILE_APPEND);
        }

    }

    /**
     * Очистка лога
     *
     * @param   string  $fileName
     *
     */
    public static function cleanLog(string $fileName = 'custom') {
        $logFile = $_SERVER['DOCUMENT_ROOT'] . '/local/logs/' . $fileName . '.log';
        if (file_exists($logFile)) {
            file_put_contents($logFile,'');
        }
    }

    /**
	 * @param \Throwable $exception
	 * @param int $logType
	 */
	public function write($exception, $logType)
	{
		$text = ExceptionHandlerFormatter::format($exception, false, $this->level);

		$context = [
			'type' => static::logTypeToString($logType),
		];

		$logLevel = static::logTypeToLevel($logType);

		$message = "OTUS {date} - Host: {host} - {type} - {$text}\n";

		$this->logger->log($logLevel, $message, $context);
	}

}