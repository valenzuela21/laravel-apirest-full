<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Clients;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{

    //*======= Show Register Course ======>*//
    public function index(Request $request)
    {
        $token = $request->header('Authorization');
        $clients = Clients::all();
        $json = [];

        foreach ($clients as $key => $value) {

            if ("Basic " . base64_encode($value['id_client'] . ":" . $value['key_secret']) === $token) {

                //$courses = Course::all();
                if(isset($_GET['page'])){
                    $courses = DB::table('course_table')
                        ->join('clients_table', 'course_table.id_author', '=', 'clients_table.id' )
                        ->select('course_table.*', 'clients_table.email',  'clients_table.first_name', 'clients_table.last_name')
                        ->paginate(10);
                }else{
                    $courses = DB::table('course_table')
                        ->join('clients_table', 'course_table.id_author', '=', 'clients_table.id' )
                        ->select('course_table.*', 'clients_table.email',  'clients_table.first_name', 'clients_table.last_name')
                        ->get();
                }

                if (!empty($courses)) {
                    $json = array(
                        'status' => 200,
                        'total_register' => count($courses),
                        'results' => $courses
                    );

                } else {

                    $json = array(
                        'status' => 200,
                        'total_register' => 0,
                        'results' => []
                    );

                }


                header("Content-type: application/json");
                echo json_encode($json);
                die();

            } else {
                $json = array(
                    'status' => 404,
                    'msg' => "You do not have authorization to view this information.",
                );
            }
        }
    }

    //*======= Create Register Course ======>*//

    public function store(Request $request)
    {
        $token = $request->header('Authorization');
        $clients = Clients::all();
        $json = [];

        foreach ($clients as $key => $value) {

            if ("Basic " . base64_encode($value['id_client'] . ":" . $value['key_secret']) === $token) {

                //Get Data
                $data = ["title" => $request->input('title'),
                    "image" => $request->input('image'),
                    "author" => $request->input('author'),
                    "description" => $request->input('description'),
                    "valor" => $request->input('valor'),
                ];

                if (!empty($data)) {

                    //Validate Data
                    $validator = Validator::make($data, [
                        'title' => 'required|string|max:255|unique:course_table',
                        'image' => 'required|string|max:255|unique:course_table',
                        'author' => 'required|string|max:255',
                        'description' => 'required|string|max:255',
                        'valor' => 'required|string|max:255',
                    ]);

                    if ($validator->fails()) {
                        $errors = $validator->errors();
                        $json = array(
                            'status' => 401,
                            'msg' => "Register not validate in params body!",
                            'errors' => $errors
                        );

                        header("Content-type: application/json");
                        echo json_encode($json);
                        die();

                    } else {

                        $course = new Course();
                        $course->title = $data['title'];
                        $course->image = $data['image'];
                        $course->author = $data['author'];
                        $course->description = $data['description'];
                        $course->valor = $data['valor'];
                        $course->id_author = $value['id'];

                        $course->save();
                        $json = array(
                            'status' => 200,
                            'msg' => "¡New course save success fully!",
                            'results' => $data,

                        );
                        header("Content-type: application/json");
                        echo json_encode($json);
                        die();

                    }


                } else {
                    $json = array(
                        'status' => 404,
                        'msg' => "Register not validate params empty!"
                    );
                    header("Content-type: application/json");
                    echo json_encode($json);
                    die();
                }

            }else{
                $json = array(
                    'status' => 404,
                    'msg' => "Register not validate params empty!"
                );

                header("Content-type: application/json");
                echo json_encode($json);
                die();
            }
        }



    }

    //*======= View One Course ======>*//
    public function show($id, Request $request)
    {
        $token = $request->header('Authorization');
        $clients = Clients::all();

        foreach ($clients as $key => $value) {

            if ("Basic " . base64_encode($value['id_client'] . ":" . $value['key_secret']) === $token) {

                $getCourse = Course::where('id', $id)->get();

                if(!empty($getCourse) && count($getCourse) > 0){

                    $json = array(
                        'status' => 200,
                        'result'=>$getCourse
                    );


                }else{
                    $json = array(
                        'status' => 401,
                        'msg' => "There are no courses!"
                    );
                }


                header("Content-type: application/json");
                echo json_encode($json) ;
                die();

            }else{
                $json = array(
                    'status' => 404,
                    'msg' => "You do not have authorization this actions.",
                );
                header("Content-type: application/json");
                echo json_encode($json) ;
                die();
            }
        }

    }
    //*======= Update Register Course ======>*//
    public function update($id, Request $request)
    {
        $token = $request->header('Authorization');
        $clients = Clients::all();


        foreach ($clients as $key => $value) {

            if ("Basic " . base64_encode($value['id_client'] . ":" . $value['key_secret']) === $token) {

                //Get Data
                $data = ["title" => $request->input('title'),
                    "image" => $request->input('image'),
                    "author" => $request->input('author'),
                    "description" => $request->input('description'),
                    "valor" => $request->input('valor'),
                ];

                if (!empty($data)) {

                    //Validate Data
                    $validator = Validator::make($data, [
                        'title' => 'required|string|max:255',
                        'image' => 'required|string|max:255',
                        'author' => 'required|string|max:255',
                        'description' => 'required|string|max:255',
                        'valor' => 'required|string|max:255',
                    ]);


                    if ($validator->fails()) {
                        $json = array(
                            'status' => 401,
                            'msg' => "Register not validate in param body!"
                        );

                        header("Content-type: application/json");
                        echo json_encode($json);
                        die();

                    } else {
                        $getCourse = Course::where('id', $id)->get();

                        if($value['id'] == $getCourse[0]['id_author']){

                            $modifyData = [
                                'title'=> $data['title'],
                                'image'=> $data['image'],
                                'author'=> $data['author'],
                                'description'=> $data['description'],
                                'valor'=> $data['valor'],
                            ];

                            Course::where('id', $id)->update($modifyData);

                            $json = array(
                                'status' => 200,
                                'msg' => "¡New course update success fully!",
                                'results' => $data,
                            );

                        }else{
                            $json = array(
                                'status' => 401,
                                'msg' => "¡Not authorized to save this data!",
                            );
                        }


                        header("Content-type: application/json");
                        echo json_encode($json);
                        die();


                    }

                }else{
                    $json = array(
                        'status' => 401,
                        'msg' => "Register not validate params empty!"
                    );
                    header("Content-type: application/json");
                    echo json_encode($json) ;
                    die();
                }

            }else{
                $json = array(
                    'status' => 404,
                    'msg' => "You do not have authorization this actions.",
                );
                header("Content-type: application/json");
                echo json_encode($json) ;
                die();

            }
        }
    }


    //*======= Delete Register Course ======>*//
    public function destroy($id, Request $request)
    {
        $token = $request->header('Authorization');
        $clients = Clients::all();

        foreach ($clients as $key => $value) {

            if ("Basic " . base64_encode($value['id_client'] . ":" . $value['key_secret']) === $token) {
                $getCourse = Course::where('id', $id)->get();
                if(!empty($getCourse) && count($getCourse) > 0) {

                    Course::where('id', $id)->delete();

                    $json = array(
                        'status' => 404,
                        'msg' => "The course has been deleted correctly!",
                    );

                    header("Content-type: application/json");
                    echo json_encode($json) ;
                    die();

                }else{
                    $json = array(
                        'status' => 404,
                        'msg' => "This course does not exist.",
                    );

                    header("Content-type: application/json");
                    echo json_encode($json) ;
                    die();
                }
            }else{
                $json = array(
                    'status' => 404,
                    'msg' => "You do not have authorization this actions.",
                );
                header("Content-type: application/json");
                echo json_encode($json) ;
                die();
            }
        }

    }

}

