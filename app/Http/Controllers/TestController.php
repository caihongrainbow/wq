<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test(){
    	for($i=0;$i<100;$i++){
            echo $i;
        }
    }
}
