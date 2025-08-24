<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\MailSender;
use Illuminate\Http\Request;

class Users extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->all();
        $users = new User();

        if(@$filter['status_id'])
            $users = $users->where('status_id', $filter['status_id']);
        else
            $users = $users->where('status_id', User::STATUS_ACTIVE);

        $users = $users->get();

        return view('user.index', compact('users', 'filter'));
    }

    public function edit( $id = null )
    {
        $user = null;
        if( $id ) {
            $user = User::find( codeDecrypt($id) );
        }

        $profiles = User::PROFILES;

        return view('user.edit', compact('user', 'profiles'));
    }

    public function store( Request $request )
    { 
        try{
            if( $request->id ) {
                $user = User::find( codeDecrypt($request->id) );
            }
            else {
                $user = new User();
            }

            $user->storeOrUpdate($request);

            // $mailService = new MailSender();
            // $emailData = (object) [
            //     'view' => 'emails.welcome-email',
            //     'dataView' => (object) ['name' => $user->name,'password' => User::getStandardPassword(), 'email' => $user->email],
            //     'sendTo' => $user->email,
            //     'subject' => "Bem vindo a Tradooh!",
            //     'replyTo' => null
            // ];
            // $mailService->sendFromView($emailData);

            sessionMessage("success", "Usuário criado/atualizado com sucesso.");
            
            return redirect()->route('user-edit',codeEncrypt($user->id) );
        }catch (\Exception $err)
        {
            sessionMessage("error", "Erro ao buscar usuário: {$err->getMessage()}");

            return redirect()->back();
        }
    }



    // API
    // public function fetchUser($text)
    // {
    //     return User::select('id', 'email')->where('email', 'like', "%$text%")->where('profile_id', '<>', User::PROFILE_SUPER)->get();
    // }

    // public function assocUser(Request $request)
    // {
    //     try{
    //         $assoc = UserCompany::where('user_id', $request->user_id)->where('retail_id', $request->retail_id)->first();
    //         if(!$assoc){
    //             $assoc = new UserCompany();
    //         }
    //         $assoc->activate($request);
    //         return json_encode(true);

    //     }catch(\Exception $err)
    //     {
    //         return json_encode(false);
    //     }
    // }


    // public function removeUser(Request $request)
    // {
    //     try{
    //         $assoc = UserCompany::where('user_id', $request->user_id)->where('retail_id', $request->retail_id)->first();
    //         if($assoc) $assoc->inactivate($request);
    //         return json_encode(true);

    //     }catch(\Exception $err)
    //     {
    //         return json_encode(false);
    //     }
    // }

}
