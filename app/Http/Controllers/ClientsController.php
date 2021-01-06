<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Clients;

class ClientsController extends Controller
{

    public function index()
    {
        $json = array(
            'status' => 401,
            'msg' => "No found!"
        );

        header("Content-type: application/json");
        echo json_encode($json) ;
        die();
    }

    /*====== Create Register =====*/
    public function store(Request $request)
    {

        $key = env('KEY_PASS');

        //Get Data
        $data = ["first_name" => $request->input('first_name'),
            "last_name" => $request->input('last_name'),
            "email" => $request->input('email')];
        if (!empty($data)) {

            //Validate Data
            $validator = Validator::make($data, [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:clients_table',
            ]);

            //Validations Fails
            if ($validator->fails()) {
                $errors = $validator->errors();
                $json = array(
                    'status' => 401,
                    'msg' => "Register not validate in params body!",
                    'errors'=>$errors
                );

                header("Content-type: application/json");
                echo json_encode($json) ;
                die();

            } else {
                $idClient = Hash::make($data["first_name"] . $data["last_name"] . $data["email"]);
                $secretKey = Hash::make($data["first_name"] . $data["last_name"] . $data["email"] . $key);

                $replaceClient = str_replace('$', 'a', $idClient);
                $replaceKey = str_replace('$', 'o', $secretKey);

                $client = new Clients();
                $client->first_name = $data['first_name'];
                $client->last_name = $data['last_name'];
                $client->email = $data['email'];
                $client->id_client = $replaceClient;
                $client->key_secret = $replaceKey;
                $client->save();

                $json = array(
                    'status' => 200,
                    'msg' => "The new token has been generated!",
                    'credentials' => ["ID_client" => $replaceClient, "ApiKey" => $replaceKey]
                );

                header("Content-type: application/json");
                echo json_encode($json) ;
                die();

            }

        } else {
            $json = array(
                'status' => 401,
                'msg' => "Register not validate params empty!"
            );
            header("Content-type: application/json");
            echo json_encode($json) ;
            die();
        }
    }


}
