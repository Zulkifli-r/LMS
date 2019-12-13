<?php

use App\Exceptions\NotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;


if (!function_exists('apiResponse')) {
    function apiResponse($code, $data = null, $message = null, $force = false){
        // dd(Str::contains(Route::current()->uri(),'classroom'));
        $classroomStatus = [];

        if (Str::contains(Route::current()->uri(),'classroom') && $force == true ) {
            $classroomStatus = classroomStatus();
        }

        $res_data = separatePagingAndData($data);
        $res_diag = httpResponse($code, $message);
        return response(array_merge($res_data, $res_diag, $classroomStatus), 200);
    }
}

if (!function_exists('httpResponse')) {
    function httpResponse($code, $message){
        switch ($code) {
            case 200:
                $status = 'OK';
                break;
            case 400:
                $status = 'BAD REQUEST';
                break;
            case 401:
                $status = 'UNAUTORIZED';
                break;
            case 403:
                $status = 'FORBIDDEN';
                break;
            case 404:
                $status = 'NOT FOUND';
                break;
            case 422:
                $status = 'UNPROCESSABLE ENTITY';
                break;
            case 429:
                $status = 'TOO MANY ATTEMPT';
                break;
            case 500:
                $status = 'INTERNAL SERVER ERROR';
                break;
            case 502:
                $status = 'BAD GATEWAY';
                break;
        }

        return [
            'diagnostic' => [
                'status' => $status,
                'code' => $code,
                'message' => $message
            ]
        ];
    }
}

if (!function_exists('separatePagingAndData')) {
    function separatePagingAndData($data){
        if (isset($data->resource) && $data->resource instanceof LengthAwarePaginator) {
            if ($data->resource->lastPage() > 1) {
                $res['meta']['count'] = $data->resource->perPage();
                $res['meta']['total'] = $data->resource->total();
                $res['response'] = $data;
                $res['links']['first'] = $data->resource->url(1);
                $res['links']['last'] = $data->resource->url($data->resource->lastPage());
                $res['links']['next'] = $data->resource->nextPageUrl();
                $res['links']['prev'] = $data->resource->previousPageUrl();
                return $res;
            }
            else{
                return ['response' => $data];
            }
        }else{
            return ['response' => $data];
        }
    }
}

if (!function_exists('responseMessage')) {
    function responseMessage($type = 'save', $status = 'success'){
        switch ($status) {
            case 'success':
                $message['status'] = 'Data berhasil';
                break;
            case 'error':
                $message['status'] = 'Data gagal';
                break;
        }

        switch ($type) {
            case 'save':
                $message['type'] = 'di simpan';
                break;
            case 'update':
                $message['type'] = 'di update';
                break;
            case 'delete':
                $message['type'] = 'di hapus';
                break;
        }

        return implode(' ', $message);
    }
}

if (!function_exists('classroomStatus')) {
    function classroomStatus(){
        $classroom = \App\Classroom::where('slug',Route::current()->slug);
        return $classroom;
    }
}