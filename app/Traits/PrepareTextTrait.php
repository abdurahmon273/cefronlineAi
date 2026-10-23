<?php

namespace App\Traits;

use App\Models\PrepareText;

trait PrepareTextTrait
{
    public function GetPrepareTextByType($type)
    {
        $prepareText=PrepareText::where('type',$type)->first();

        return $prepareText->text;
    }
}
