<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\Operations;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use App\Models\Note;

class MainController extends Controller
{
    public function index(){
        // dados do usuario
        $id = session('user.id');
        $notes = User::find($id)->notes()->whereNull('deleted_at')->get()->toArray();

        return view('home', ['notes' => $notes]);
    }

    public function newNoTe(Request $request){
        return view('new_note');
    }

    public function newNoTeSubmit(Request $request){
        $this->validaFormulario($request);
        $id = session('user.id');
        $note = new Note();
        $note->user_id = $id;
        $note->title = $request->text_title;
        $note->text = $request->text_note;
        $note->save();
        echo redirect()->route('home');
    }

    public function editNote($id){
        $id = Operations::decryptId($id);
        if($id == null){
            return redirect()->route('home');
        }
        $note = Note::find($id);
        return view('edit_note', ['note' => $note]);
    }

    public function editNoteSubmit(Request $request){
        $this->validaFormulario($request);
        if($request->note_id == null){
            redirect()->route('home');
        }
        // decripta id
        $id = Operations::decryptId($request->note_id);
        if($id == null){
            return redirect()->route('home');
        }
        // carrega nota
        $note = Note::find($id);
        // update nota
        $note->title = $request->text_title;
        $note->text = $request->text_note;
        $note->save();
        return redirect()->route('home');
    }

    public function deleteNote($id){
        $id = Operations::decryptId($id);
        if($id == null){
            return redirect()->route('home');
        }
        // carrega nota
        $note = Note::find($id);
        // redireciona para view de confirmação de deletar
        return view('delete_note', ['note' => $note]);
    }

    public function deleteNoteConfirm($id){
        $id = Operations::decryptId($id);
        if($id == null){
            return redirect()->route('home');
        }
        // carrega nota
        $note = Note::find($id);
        // 1. hard delete
        // $note->delete();
        // 2. soft delete
        // $note->deleted_at = date('Y-m-d H:i:s');
        // $note->save();
        // 3. soft delete propertie in model
        $note->delete();
        // 4. hard delete propertie in model
        // $note->forceDelete();
        // redireciona para home
        return redirect()->route('home');
    }

    private function validaFormulario(Request $request){
        $request->validate(
            // roles
            [
                'text_title' => 'required|min:3|max:200',
                'text_note' => 'required|min:3|max:3000',
            ],
            // erro messages
            [
                'text_title.required' => "Campo título é obrigatório",
                'text_title.min' => "O título deve ter pelo menos :min caracteres",
                'text_title.max' => "O título deve ter no maximo :max caracteres",
                
                'text_note.required' => "Campo nota é obrigatório",
                'text_note.min' => "A nota deve ter pelo menos :min caracteres",
                'text_note.max' => "A nota deve ter no maximo :max caracteres",
            ]
        );
    }

}
