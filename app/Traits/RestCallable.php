<?php

namespace App\Traits;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

trait RestCallable {


    public function makeGetRequest($url){
        
        $token = session('token');
        $response = Http::withToken($token)->withOptions($this->getOptions())->get($this->api_root.'/'.$url);

        if($response->failed() && !$response->clientError()){
            $this->logResponse($response, $url);
            return '';
        }
        
        return $this->sendResponse($response);

    }

    public function makePostRequest(String $url, Array $data){

        $token = session('token');
        
        $response = Http::withToken($token)->withOptions($this->getOptions())->post($this->api_root.'/'.$url, $data);

        if($response->failed() && !$response->clientError()){
            // Log details and return empty string;
            $this->logResponse($response, $url, $data);
            return '';
        }

        return $this->sendResponse($response);
        
        

    }

    public function makePostRequestWithoutToken(String $url, Array $data){
        
        $response = Http::withOptions($this->getOptions())->post($this->api_root.'/'.$url, $data);

        if($response->failed() && !$response->clientError()){
            // Log details and return empty string;
            $this->logResponse($response, $url, $data);
            return '';
        }

        return $this->sendResponse($response);
        
        

    }


    private function sendResponse(\Illuminate\Http\Client\Response $response){
        return [
            'responseJson' => $response->json(),
            'statusCode' => $response->status()
        ];
    }

    private function getOptions(){
        return (env('APP_ENV') === 'local')?['verify' => false]:[];
    }

    private function logResponse(\Illuminate\Http\Client\Response $response, String $url, Array $data = []){
        $fileName = 'error-'.Carbon::now()->valueOf().'.html';
        Log::info('===');
        Log::info('Failed Response Log:');
        Log::info('Status: '. $response->status());
        Log::info('URL: ' . $url);
        Log::info('Data: ', $data);
        Log::info('Error Detail saved in ' . $fileName . ' file at storage/app');
        Storage::disk('local')->put($fileName, $response->body());
    }
    

}
