<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Http\JsonResponse;
class AjaxController extends Controller
{

    const MSG_COMPLETE = 'Your application has been successfully sent! You will be contacted shortly!';

    const MESSAGES = [
        'name.required' => 'A name is required',
        'phone.required' => 'A phone is required',
        'phone.max'    => 'The number must contain no more than 15 characters',
        'phone.min'    => 'The number must contain at least 10 characters',
    ];

    const RULES = [
        'name' => 'required|max:255',
        'phone' => 'required|min:10|max:17',
    ];

    public function messages(): array
    {
        return [
            'name.required' => 'A name is required',
            'phone.required'  => 'A phone is required',
        ];
    }

    public function test(Request $request) {

        if (!$request->isMethod('post')){
            return json_encode(['error'=>'true']);
        }

        $returnJson['msg'] = self::MSG_COMPLETE;
        $returnJson['cleanForm'] = true;
        
        return response($returnJson)->withHeaders(["Access-Control-Allow-Origin"=>"*"]);
    }

    public function getObjInfo(Request $request): JsonResponse {

        if (!$request->isMethod('post')){
            return json_encode(['error' => 'true']);
        }

        $validate = Validator::make($request->all(), self::RULES, self::MESSAGES);

        if ($validate->fails()) {
            $returnJson['error'] = true;
            $returnJson['errorsArray'] = $validate->errors();

            return response()->json($returnJson);
        }

        $returnJson['msg'] = self::MSG_COMPLETE;
        $returnJson['cleanForm'] = true;

        return response()->json($returnJson);
    }
}
