<?php

namespace App\Http\Controllers\Api;

use App\Constant\UploadConstant;
use App\Constant\UserConstant;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\User;
use App\Repositories\CommentRepository;
use App\Repositories\ScoreRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Mews\Purifier\Facades\Purifier;

class UploadApiController extends Controller
{
    static $upload_path = 'public/question/';
    /**
     * Upload  image of user  in storage.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function uploadImage(Request $request)
    {
        $file_path = null;
        $user = session('user');
        //避免 XSS 攻击的通用解决方案 HTML Purifier
        Purifier::clean($request->post('upload'));
        if($request->hasFile('upload')){
            $image = $request->file('upload');
            // Get filename with extension
            $filenameWithExt = $image->getClientOriginalName();

            //$file_path = $image->store('public/photo');
            $save = $this->resizeImage($image, $filenameWithExt, UploadConstant::QUESTION_IMAGE_SIZE);
            $file_path = UploadConstant::QUESTION_UPLOAD_PATH.$filenameWithExt;
            $url = Storage::url($file_path);
            //CKEditor返回特定JSON格式
            return response()->json([
                'fileName' => $filenameWithExt,
                'uploaded' => true,
                'url' => url($url),
                ]);
        } else {
            return response()->json([
                'fileName' => '',
                'uploaded' => false,
                'url' => '',
            ]);
        }

    }

    public function uploadCourseImage(Request $request)
    {
        $input = $request->all();
        if(array_key_exists('token', $input )) {
            $token = $input['token'];
            $user = User::where('remember_token', $token )->get();
            if(count($user) == 0) {
                return response('驗證失敗!', 401);
            }
        } else {
            return response('驗證失敗!', 401);
        }
        $success = 0;
        $new_name = time().'.png';

        if(array_key_exists('file_name', $input )) {
            $new_name = $input['file_name'];
        }
        $option = 'c1';
        $category_id = $request->input('category_id', 1);

        $path = public_path().'/photo/';
        if(isset($_POST['img'])){

            $base64 = $_POST['img'];
            $tmp = 'data:image/png;base64,';
            if(str_contains($base64, 'jpeg') ){
                $tmp = 'data:image/jpeg;base64,';
            } else if (str_contains($base64, 'png') ){
                $tmp = 'data:image/png;base64,';
            } else {
                $tmp = 'data:image/gif;base64,';
            }
            $img = str_replace($tmp, '', $base64);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);
            //$dir = storage_path('app\\public\\UAV');
            $dir = storage_path('app/public/photo/c'.$category_id);
            $dir2 = 'public/photo/c'.$category_id;
            if(!Storage::exists($dir2)) {
                Storage::makeDirectory($dir2); //creates directory
            }
            $file = $dir.'/'.$new_name;
            $file2 = $dir2.'/'.$new_name;
            $file_url = url(Storage::url($file2 ));
            $success = file_put_contents($file, $data);
        }
        $result = array("file"=>$file_url, "length"=>$success);

        return response($result , 200);
    }

    public function uploadChapterImage(Request $request)
    {
        $input = $request->all();
        if(array_key_exists('token', $input )) {
            $token = $input['token'];
            $user = User::where('remember_token', $token )->get();
            if(count($user) == 0) {
                return response('驗證失敗!', 401);
            }
        } else {
            return response('驗證失敗!', 401);
        }
        $success = 0;
        $new_name = time().'.png';

        if(array_key_exists('file_name', $input )) {
            $new_name = $input['file_name'];
        }
        $category_id = $request->input('category_id', 1);
        $course_id = $request->input('course_id', 1);


        $path = public_path().'/photo/';
        if(isset($_POST['img'])){

            $base64 = $_POST['img'];
            $tmp = 'data:image/png;base64,';
            if(str_contains($base64, 'jpeg') ){
                $tmp = 'data:image/jpeg;base64,';
            } else if (str_contains($base64, 'png') ){
                $tmp = 'data:image/png;base64,';
            } else {
                $tmp = 'data:image/gif;base64,';
            }
            $img = str_replace($tmp, '', $base64);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);
            //$dir = storage_path('app\\public\\UAV');
            $dir = storage_path('app/public/photo/c'.$category_id.'/o'.$course_id);
            $dir1 = 'public/photo/c'.$category_id;
            if (!Storage::exists($dir1)) {
                Storage::makeDirectory($dir1);
            }
            $dir2 = 'public/photo/c'.$category_id.'/o'.$course_id;
            if(!Storage::exists($dir2)) {
                Storage::makeDirectory($dir2); //creates directory
            }
            $file = $dir.'/'.$new_name;
            $file2 = $dir2.'/'.$new_name;
            $file_url = url(Storage::url($file2 ));
            $success = file_put_contents($file, $data);
        }
        $result = array("file"=>$file_url, "length"=>$success);

        return response($result , 200);
    }


    /**
     * Resizes a image using the InterventionImage package.
     *
     * @param object $file
     * @param string $fileNameToStore
     * @author Niklas Fandrich
     * @return bool
     */
    public function resizeImage($file, $fileNameToStore, $size) {
        // Resize image
        $resize = Image::make($file)->resize($size, null, function ($constraint) {
            $constraint->aspectRatio();
        })->encode('jpg');

        // Create hash value
        $hash = md5($resize->__toString());

        // Prepare qualified image name
        //$image = $hash."jpg";
        $file = UploadConstant::QUESTION_UPLOAD_PATH.$fileNameToStore;

        // Put image to storage
        $save = Storage::put($file , $resize->__toString());

        if($save) {
            return true;
        }
        return false;
    }

    public function courseScore(Request $request, ScoreRepository $scoreRepository, UserRepository $userRepository)
    {
        $input = $request->all();
        if(array_key_exists('token', $input )) {
            $token = $input['token'];
            $user = User::where('remember_token', $token )->get();
            if(count($user) == 0) {
                return response('驗證失敗!', 401);
            }
        } else {
            return response('驗證失敗!', 401);
        }
        $attribute =  $request->input('attribute', null);
        $result = array('source'=>'course-score');
        if($attribute == null) {
            $result['data'] = null;
            return response($result , 200);
        }
        $attribute = (array)json_decode($attribute);
        $scope = $scoreRepository->create($attribute);
        $user = $userRepository->find($attribute['user_id']);
        $scope['user_name'] = $user->name;
        $scope['date'] = $scope->created_at->toDateString();
        $result['data'] =$scope;
        return response($result , 200);
    }

    public function courseComment(Request $request, CommentRepository $commentRepository, UserRepository $userRepository)
    {
        $input = $request->all();
        if(array_key_exists('token', $input )) {
            $token = $input['token'];
            $user = User::where('remember_token', $token )->get();
            if(count($user) == 0) {
                return response('驗證失敗!', 401);
            }
        } else {
            return response('驗證失敗!', 401);
        }
        $attribute =  $request->input('attribute', null);
        $result = array('source'=>'course-comment');
        if($attribute == null) {
            $result['data'] = null;
            return response($result , 200);
        }
        $attribute = (array)json_decode($attribute);
        $comment = $commentRepository->create($attribute);
        $user = $userRepository->find($attribute['user_id']);
        $comment->user_name = $user->name;
        $result['data'] = $comment;
        return response($result , 200);
    }

    public function courseCommentReply(Request $request, CommentRepository $commentRepository, UserRepository $userRepository)
    {
        $input = $request->all();
        if(array_key_exists('token', $input )) {
            $token = $input['token'];
            $user = User::where('remember_token', $token )->get();
            if(count($user) == 0) {
                return response('驗證失敗!', 401);
            }
        } else {
            return response('驗證失敗!', 401);
        }
        $targetId =  $request->input('targetId', null);
        if($targetId) {
            $result = $commentRepository->update($targetId, ["status"=>1]);
        }
        $attribute =  $request->input('attribute', null);
        if($attribute) {
            $result = array('source'=>'course-comment-reply');
            if($attribute == null) {
                $result['data'] = null;
                return response($result , 200);
            }
            $attribute = (array)json_decode($attribute);
            $comment = $commentRepository->create($attribute);
            $user = $userRepository->find($attribute['user_id']);
            $comment->user_name = $user->name;
            $result['data'] = $comment;
            return response($result , 200);
        } else {
            return response(null , 200);
        }
    }

    public function disableComment(Request $request, UserRepository $userRepository)
    {
        $input = $request->all();
        if(array_key_exists('token', $input )) {
            $token = $input['token'];
            $user = User::where('remember_token', $token )->get();
            if(count($user) == 0) {
                return response('驗證失敗!', 401);
            }
        } else {
            return response('驗證失敗!', 401);
        }
        $userId =  (int)$request->input('userId', null);
        if($userId) {
            $result = $userRepository->update($userId, ["active"=>UserConstant::DISABLE_STATUS]);
        }

        return response(null , 200);

    }

    public function removeAllComment(Request $request)
    {
        $input = $request->all();
        if(array_key_exists('token', $input )) {
            $token = $input['token'];
            $user = User::where('remember_token', $token )->get();
            if(count($user) == 0) {
                return response('驗證失敗!', 401);
            }
        } else {
            return response('驗證失敗!', 401);
        }
        $arrStr =  $request->input('arrStr', null);
        if($arrStr) {
            $arr = json_decode($arrStr);
            Comment::whereIn('id', $arr)->delete();
        }

        return response(null , 200);

    }
}
