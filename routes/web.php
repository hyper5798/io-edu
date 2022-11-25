<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', 'Admin\UserController@index');



/*Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['web']], function () {

    Route::get('/login', 'LoginController@login');
});*/

use Illuminate\Support\Facades\Route;
Route::group(['prefix' => 'node', 'as' => 'node.', 'namespace' => 'Node', 'middleware' => ['web']], function () {
    Route::get('/lineChart', 'AppController@lineChart');
});


Route::group(['namespace' => 'Admin', 'middleware' => ['web']], function () {
    Route::get('/login', 'LoginController@login');//For get & post
    Route::post('/postLogin', 'LoginController@postLogin');//For get & post
    Route::get('/register', 'LoginController@register');
    Route::get('/redirect/{provider}', 'SocialAuthController@redirect');
    Route::get('/{provider}/callback', 'SocialAuthController@callback');
    //Route::get('/', 'IndexController@index');
    Route::get('/', 'IndexController@index');
    Route::get('/resend-mail', 'LoginController@resendEmail');
    Route::get('/show-verify-email', 'LoginController@showVerifyEmail');
    Route::get('/active-account', 'LoginController@activeAccount');
    Route::get('/forgot-password', 'LoginController@forgotPassword');
    Route::get('/token-send', 'LoginController@tokenVerify');
    Route::post('/token-check', 'LoginController@tokenCheck');
    Route::post('/forgot-password-check', 'LoginController@forgotPasswordCheck');
});

//
Route::group(['namespace' => 'Admin', 'middleware' => ['web','admin.login']], function () {
    Route::get('/backend', 'IndexController@backend');
    Route::get('/module', 'IndexController@module');
    Route::get('/develop', 'IndexController@develop');
    Route::get('/logout', 'LoginController@quit');
    Route::any('/pass', 'IndexController@pass'); //For get & post
    Route::get('/reports', 'ReportController@Index');
    Route::get('/logs', 'LogController@Index');
    Route::delete('/deleteLogs', 'LogController@deleteLogs');
    Route::delete('/delReports', 'ReportController@destroy');
    Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
        Route::get('users', 'UserController@index');
        Route::put('users', 'UserController@update');
        Route::delete('users', 'UserController@destroy');
        Route::get('userCourses', 'UserController@userCourses');
        Route::put('updateUserCourses', 'UserController@updateUserCourses');
        Route::get('cps', 'CompanyController@index');
        Route::put('cps', 'CompanyController@update');
        Route::delete('cps', 'CompanyController@destroy');
        Route::get('classes', 'ClassController@index');
        Route::get('roles', 'RoleController@index');
        Route::put('roles', 'RoleController@update');
        Route::delete('roles', 'RoleController@destroy');
        Route::get('courses', 'CourseController@index');
        Route::get('/course/create', 'CourseController@create')->name('course.create');
        Route::post('/course/store', 'CourseController@store')->name('course.store');
        Route::get('/course/{course}/edit', 'CourseController@edit')->name('course.edit');
        Route::put('/course/{course}', 'CourseController@update')->name('course.update');
        Route::delete('/course/{course}', 'CourseController@destroy')->name('course.destroy');
        Route::put('editCourse', 'CourseController@editCourse');
        Route::delete('delCourse', 'CourseController@delCourse');
        Route::get('videos', 'VideoController@index');
        Route::post('uploadVideo', 'VideoController@uploadVideo');
        Route::post('editVideo', 'VideoController@editVideo');
        Route::delete('delVideo', 'VideoController@delVideo');
        //For chapter add video
        Route::get('video/create', 'VideoController@create')->name('video.create');
        Route::get('chapter', 'ChapterController@index');
        Route::get('/chapter/create', 'ChapterController@create')->name('chapter.create');
        Route::post('/chapter/store', 'ChapterController@store')->name('chapter.store');
        Route::get('/chapter/{chapter}/edit', 'ChapterController@edit')->name('chapter.edit');
        Route::put('/chapter/{chapter}', 'ChapterController@update')->name('chapter.update');
        Route::delete('/chapter/{chapter}', 'ChapterController@destroy')->name('chapter.destroy');
        Route::put('editChapter', 'ChapterController@editChapter');
        Route::delete('delChapter', 'ChapterController@delChapter');
        Route::get('tutorial', 'TutorialController@index');
        Route::get('categories', 'CategoryController@index');
        Route::put('editCategory', 'CategoryController@editCategory');
        Route::delete('delCategory', 'CategoryController@delCategory');
        Route::get('/announce', 'AnnounceController@index')->name('announce.index');
        Route::get('/announce/create', 'AnnounceController@create')->name('announce.create');
        Route::post('/announce/store', 'AnnounceController@store')->name('announce.store');
        Route::get('/announce/{announce}/edit', 'AnnounceController@edit')->name('announce.edit');
        Route::put('/announce/{announce}', 'AnnounceController@update')->name('announce.update');
        Route::delete('/announce/{announce}', 'AnnounceController@destroy')->name('announce.destroy');
        //Route::resource('question', 'AnnounceController');
    });
});


Route::group(['prefix' => 'node', 'as' => 'node.', 'namespace' => 'Node', 'middleware' => ['web','admin.login']], function () {

    Route::get('apps/change', 'AppController@change');
    Route::get('types', 'TypeController@index');
    Route::put('types', 'TypeController@update');
    Route::delete('types', 'TypeController@destroy');
    Route::post('uploadTypeImage', 'TypeController@uploadTypeImage');
    Route::get('devices', 'DeviceController@devices');
    Route::put('devices', 'DeviceController@editDevice');
    Route::get('myDevices', 'DeviceController@myDevices');
    Route::put('editDevice', 'DeviceController@update');
    Route::delete('delDevice', 'DeviceController@destroy');
    Route::get('commands', 'CommandController@index');
    Route::put('commands', 'CommandController@update');
    Route::delete('commands', 'CommandController@destroy');
    Route::get('commandList', 'CommandController@commandList');
    Route::get('commandList/{device_id}', 'CommandController@commandKeys');
    Route::get('apps', 'AppController@index');
    Route::get('apps/channel', 'AppController@channel');
    Route::put('apps/updateChannel', 'AppController@update');
    Route::put('apps', 'AppController@update');
    Route::delete('apps', 'AppController@destroy');
    Route::delete('delReports', 'AppController@delReports');
    Route::delete('delReport', 'AppController@delReport');
    Route::delete('apps/delReports', 'AppController@delReports');
    Route::get('admin', 'AppController@admin');
    Route::get('apps/reports', 'AppController@reports');
    Route::put('apps/reports', 'AppController@gaugeSetting');
    Route::get('apps/APIkey', 'AppController@APIkey');
    Route::get('products', 'ProductController@index');
    Route::put('products', 'ProductController@update');
    Route::post('import', 'ProductController@import');
    Route::delete('products', 'ProductController@destroy');
    Route::get('setCp', 'AdminController@setCp');
    Route::post('editCp', 'AdminController@editCp');
    Route::post('editClass', 'AdminController@editClass');
    Route::delete('delClass', 'AdminController@delClass');
    Route::get('accounts', 'AdminController@accounts');
    Route::post('editAccount', 'AdminController@editAccount');
    Route::delete('delAccount', 'AdminController@delAccount');
    Route::post('editBatchAccount', 'AdminController@editBatchAccount');
    Route::get('map', 'MapController@index');
    Route::put('editSetting', 'MapController@editSetting');
    Route::delete('delSetting', 'MapController@delSetting');
    Route::get('viewControl', 'MapController@viewControl');
    Route::get('paramTest', 'MapController@paramTest');
    //Route::get('myCommand', 'CommandController@myCommand');
    //Route::put('editCommand', 'CommandController@editCommand');
    //Route::delete('delMyCommand', 'CommandController@delMyCommand');
    Route::get('/apps/{id}/widget/{key}', 'AppController@widget');

});


Route::group( ['prefix' => 'module', 'as' => 'module.', 'namespace' => 'Module', 'middleware' => ['web','admin.login']], function () {
    //Add dor node script
    Route::get('nodeScript', 'NodeController@script');
    Route::get('nodeDevice', 'NodeController@devices');
    Route::get('nodeFlow', 'NodeController@flow');
    Route::post('editNodeDevice', 'NodeController@editNodeDevice');
    Route::post('editNodeRule', 'NodeController@editNodeRule');
    Route::post('editNodeRelation', 'NodeController@editNodeRelation');
    Route::post('editNodeFlow', 'NodeController@editNodeFlow');
    Route::delete('delNodeRule', 'NodeController@delNodeRule');
    Route::delete('delScript', 'NodeController@delScript');
    Route::get('nodeStatus', 'NodeController@status');
    Route::get('lineSetting', 'NodeController@lineSetting');
    Route::get('monitor', 'MonitorController@index');
    Route::get('nodeReports', 'NodeController@reports');
    Route::put('editSetting', 'NodeController@editSetting');
    Route::delete('delReports', 'NodeController@delReports');
});

Route::group( ['prefix' => 'escape', 'as' => 'escape.', 'namespace' => 'Escape', 'middleware' => ['web','admin.login']], function () {
    Route::get('admin', 'EscapeController@admin');
    Route::get('personal', 'EscapeController@personal');
    Route::get('rank', 'RecordController@rank');
    Route::delete('delRecord', 'RecordController@delRecord');
    Route::get('teamRecords', 'RecordController@teamRecords');
    Route::get('profile', 'ProfileController@index');
    Route::post('uploadImage', 'ProfileController@uploadImage');
    Route::post('editProfile', 'ProfileController@editProfile');
    Route::get('editGame', 'EscapeController@temp');
    Route::get('knowledgeSource', 'EscapeController@temp');
    Route::get('teams', 'TeamController@setTeam');
    Route::post('editTeam', 'TeamController@editTeam');
    Route::post('editTeamUsers', 'TeamController@editTeamUsers');
    Route::delete('delTeam', 'TeamController@delTeam');
    Route::get('roomRecord', 'RecordController@roomRecord');
    //Route::get('setSecurity', 'MissionController@setSecurity');
    //Route::post('editSecurity', 'MissionController@editSecurity');
    //Route::get('carousel', 'ManageController@carousel');
});

Route::group(['prefix' => 'learn', 'as' => 'learn.', 'namespace' => 'Learn', 'middleware' => ['web','admin.login']], function () {
    Route::resource('question', 'QuestionController');
    Route::get('test-create', 'TestController@testCreate');
    Route::post('test-record', 'TestController@testRecord');
    Route::get('self-test', 'TestController@selfTest');
    //Route::post('test-verify', 'TestController@testVerify');
    Route::get('test-analyze', 'TestController@testAnalyze');
    Route::get('radar', 'TestController@radar');

});

Route::group(['prefix' => 'learn', 'as' => 'learn.', 'namespace' => 'Admin', 'middleware' => ['web','admin.login']], function () {
    Route::get('allCourses', 'CourseController@allCourses');
    Route::get('courseVideo', 'CourseController@courseVideo');
    Route::get('chapterVideo', 'CourseController@chapterVideo');
    Route::get('comment-replay', 'CourseController@commentReply');
});

Route::group(['prefix' => 'room', 'as' => 'room.', 'namespace' => 'Room', 'middleware' => ['web','admin.login']], function () {
    Route::get('setCp', 'ManageController@setCp');
    Route::get('setGroup', 'ManageController@setGroup');
    Route::post('editCp', 'ManageController@editCp');
    Route::delete('delCp', 'ManageController@delCp');
    Route::post('editGroup', 'ManageController@editGroup');
    Route::delete('delGroup', 'ManageController@delGroup');
    Route::get('accounts', 'ManageController@accounts');
    Route::post('editAccount', 'ManageController@editAccount');
    Route::post('editGroupUser', 'ManageController@editGroupUser');
    Route::delete('delAccount', 'ManageController@delAccount');
    Route::delete('delGroupUser', 'ManageController@delGroupUser');
    Route::post('editBatchAccount', 'ManageController@editBatchAccount');
    Route::get('setRoom', 'MissionController@setRoom');
    Route::get('setMission', 'MissionController@setMission');
    Route::post('editRoom', 'MissionController@editRoom');
    Route::post('editSequence', 'MissionController@editSequence');
    Route::post('editMission', 'MissionController@editMission');
    Route::post('editScript', 'MissionController@editScript');
    Route::delete('delRoom', 'MissionController@delRoom');
    Route::delete('delMission', 'MissionController@delMission');
    Route::delete('delScript', 'MissionController@delScript');
    Route::post('uploadScriptImage', 'MissionController@uploadScriptImage');
    Route::get('/index', 'RoomController@index');
    //thruster
    Route::get('/thruster', 'UsvController@thruster');
    //USV
    Route::get('/usv', 'UsvController@index');
    Route::get('/history/{id}', 'UsvController@history');
    Route::put('/editSetting', 'UsvController@editSetting');
    Route::delete('/delSetting', 'UsvController@delSetting');
    Route::get('/viewControl', 'UsvController@viewControl');
    //Agriculture
    Route::post('/editFarmSetting', 'FarmController@editFarmSetting');
    Route::delete('/deleteFarmSetting', 'FarmController@deleteFarmSetting');
    Route::get('/agriBot', 'FarmController@index');
    Route::get('/webrtc/{id}', 'FarmController@webrtc');



    Route::get('/develop/{id}', 'RoomController@developShow');
    Route::get('/module/{id}', 'RoomController@moduleShow');

    Route::get('/profile', 'ProfileController@index');
    Route::post('/uploadImage', 'ProfileController@uploadImage');
    Route::post('/editProfile', 'ProfileController@editProfile');
    Route::get('/userBinding', 'RoomController@userBinding');
    Route::post('/editUserRoom', 'RoomController@editUserRoom');
    Route::post('/editUserDevice', 'RoomController@editUserDevice');
    Route::delete('/delUserDevice', 'RoomController@delUserDevice');
    Route::post('/editUserDevice', 'RoomController@editUserDevice');
    Route::post('/bindingUser', 'RoomController@bindingUser');
    Route::delete('/delBindUser', 'RoomController@delBindUser');

    Route::get('setEmail', 'RoomController@setEmail');
    Route::post('editEmail', 'RoomController@editEmail');
});
