<?php

namespace Deyji\Manage\Http\Controllers;

use App\Http\Controllers\Controller;
use Deyji\Manage\Models\AppConfigs;
use Illuminate\Http\Request;


class ConfigController extends Controller{

    public function save_config(Request $request){
        $config = [];

        // save it as json in key value pairs
        foreach(array_keys($request->all()) as $field){
            $config[$field] = $request[$field];
        }

        // save the file somewhere
        $path = base_path('resources/config.json');
        file_put_contents(base_path('resources/config.json'), stripslashes(json_encode($config, JSON_PRETTY_PRINT)));

        // Save to database
        $this->store_config($path);

    }

    private function store_config($data){
        // Store static path for now
        $config = new AppConfigs([
            "file_path"=> $data
        ]);

        $config->save();

        return response(["message" => "Config Saved to Database"]);
    }

    public function get_config(){
        return response(AppConfigs::all()->first());
    }
}