<?php

namespace App\Http\Controllers\Escape;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Intervention\Image\Facades\Image;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param  Request  $request
     * @return View
     */
    public function index(Request $request)
    {
        $user = session('user');
        $profile = User::find($user['id'])->profile;
        if($profile && $profile->image_url) {
            $user->image_url = Storage::url($profile->image_url);
            session(['user' => $user]);
        }

        return view('escape.profile', compact(['user','profile']));
    }

    /**
     * Update profile user in storage.
     *
     * @param  Request  $request
     * @return View
     */
    public function editProfile(Request $request)
    {
        $user = session('user');
        $input = $request->all();

        $id = (int)$input['id'];
        $profile = null;

        if($id>0)
        $profile = Profile::where('id', $id)->first();
    else {
        $profile = new Profile;
        $profile->user_id = $user['id'];
    }
        if($input['name'] != null){
            $name = $input['name'];
            if($name != $user['name']) {
                $editUser = User::find($user['id']);
                $editUser->name = $name;
                $editUser->save();
                $user['name'] = $name;
                session(['user' => $user]);
            }
        }
        if($input['cellphone'] != null)
            $profile->cellphone = $input['cellphone'];
        else
            $profile->cellphone = null;
        if($input['telephone'] != null)
            $profile->telephone = $input['telephone'];
        else
            $profile->telephone = null;

        if($input['birthday'] != null)
            $profile->birthday = $input['birthday'];
        else
            $profile->birthday = null;

        if($input['address'] != null)
            $profile->address = $input['address'];
        else
            $profile->address = null;

        $profile->save();

        return back();
    }

    /**
     * Upload  image of user  in storage.
     *
     * @param  Request  $request
     * @return View
     */
    public function uploadImage(Request $request)
    {
        $file_path = null;
        $user = session('user');
        if($request->hasFile('progressbarTW_img')){
            $image = $request->file('progressbarTW_img');
            // Get filename with extension
            $filenameWithExt = $image->getClientOriginalName();

            // Get file path
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

            // Remove unwanted characters
            $filename = preg_replace("/[^A-Za-z0-9 ]/", '', $filename);
            $filename = preg_replace("/\s+/", '-', $filename);

            // Get the original image extension
            $extension = $image->getClientOriginalExtension();

            // Create unique file name
            $fileNameToStore = $filename.'_'.time().'.'.$extension;
            //$file_path = $image->store('public/photo');
            $save = $this->resizeImage($image, $fileNameToStore);
            $file_path = 'public/photo/'.$fileNameToStore;
        } else {
            return back();
        }
        //$path = Storage::url($file_path);
        //dd(asset($path ));
        $p = Profile::where('user_id', $user['id'])->first();
        if($p == null) {
            $p = new Profile;
            $p->user_id = $user['id'];
            $p->image_url = $file_path;
            $p->save();
        } else {
            if($p->image_url) {
                //$file = 'CE8iBq2YfWLSsk4hV8teR3EZR9RqI8CyhHMPcA8R.jpeg';
                //$storagePath  = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
                $myPath = $p->image_url;
                $exists = Storage::disk('local')->exists($myPath);
                if($exists) {
                    Storage::disk('local')->delete($myPath);
                }
            }
            $p->image_url = $file_path;
            $p->save();
        }

        session(['user' => $user]);
        return back();
    }
    /**
     * Resizes a image using the InterventionImage package.
     *
     * @param object $file
     * @param string $fileNameToStore
     * @author Niklas Fandrich
     * @return bool
     */
    public function resizeImage($file, $fileNameToStore) {
        // Resize image
        $resize = Image::make($file)->resize(512, null, function ($constraint) {
            $constraint->aspectRatio();
        })->encode('jpg');

        // Create hash value
        $hash = md5($resize->__toString());

        // Prepare qualified image name
        $image = $hash."jpg";

        // Put image to storage
        $save = Storage::put("public/photo/{$fileNameToStore}", $resize->__toString());

        if($save) {
            return true;
        }
        return false;
    }
}
