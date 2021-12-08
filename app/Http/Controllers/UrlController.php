<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Url;
class UrlController extends Controller
{
    //
    public function store(Request $request) {
        $response = [];
        $input = $request->get('url');
        if (empty($input)) {
            $response['errors'] = 'Поле не может быть пустым';
            $response['success'] = 'false';
            return response()->json($response);
        }
        if (!filter_var($input, FILTER_VALIDATE_URL)) { 
            $response['errors'] = 'В данное поле можно ввести только ссылку';
            $response['success'] = 'false';
            return response()->json($response);

        }
        if (empty($errors)){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_USERAGENT, "google");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $input);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            $output = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($httpcode != '200') {
                $response['errors'] = 'Данная ссылка не работает';
                $response['success'] = 'false';
                return response()->json($response);
            }
        }
        if (empty($response)) {
            $url = new Url;
            $code = $url->generateCode();
            $url->create(array('shortcode'=>$code, 'origin_url' => $input));
            $response['success'] = 'true';
            $generatedUrl = env("APP_URL"). '/i/' .$code;
            $response['result'] = $generatedUrl;
            return response()->json($response);
        }
    
    }
    public function redirect($code) {
        $url = new Url;
        $result = $fullUrl = $url->where('shortcode',$code)->first();
        if ($result !== null) {
            return redirect()->to($result->origin_url);
        } else {
            return redirect()->to('/');
        };
    }
}
