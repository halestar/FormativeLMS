<?php

namespace App\Models\Utilities;

class LogItem
{
    private string $data;
    public readonly string $type;
    public readonly string $msg;

    public function __construct(array $data)
    {
        $this->data = $data;
        if(isset($data['type']))
            $this->type = $data['type'];
        else
            $this->type = __('common.unknown');

        if(isset($data['msg']))
            $this->msg = $data['msg'];
        else
            $this->msg = print_r($data, true);
    }

    public function toJson():array
    {
        return ['type' => $this->type, 'msg' => $this->msg];
    }
}
