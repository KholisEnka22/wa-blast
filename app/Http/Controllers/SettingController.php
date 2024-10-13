<?php

namespace App\Http\Controllers;

use App\Models\Server;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Setting',
            'server' => Server::all(),
        ];
        return view('data.setting',$data);
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'server' => 'required',
        ]);

        $validated['server'] = base64_decode($validated['server']);

        Server::create($validated + ['status' => 'Non Active','count' =>'0']);

        return redirect()->back()->with('message.type', 'success')
            ->with('message.content', 'Data berhasil ditambah.');
    }

    public function toggleServer($id)
    {
        $server = Server::findOrFail($id);
        if(Server::where('status','Active')->count() > 0){
            Server::where('status','Active')->update(['status'=>'Non Active']);
        }
        $server->status = $server->status === 'Active' ? 'Non Active' : 'Active';
        $server->save();

        return redirect()->back()->with('message.type', 'success')
            ->with('message.content', 'Status server berhasil diubah.');
    }
    
    public function destroy($id)
    {
        $server = Server::findOrFail($id);
        $server->delete();

        return redirect()->back()->with('message.type', 'success')
            ->with('message.content', 'Status server berhasil dihapus.');
    }
}
