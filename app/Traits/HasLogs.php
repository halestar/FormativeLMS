<?php

namespace App\Traits;

use App\Models\Utilities\LogItem;

trait HasLogs
{
    public function appendLog($msg, $type = null): void
    {
        $logField = static::$logField;
        $this->$logField = new LogItem(['type' => $type, 'msg' => $msg]);
        $this->save();
    }
}
