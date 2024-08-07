<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContentStoreRequest;
use App\Http\Requests\ContentUpdateRequest;
use App\Models\Content;
use App\Models\ContentType;
use App\Models\FacultyDiscipline;
use App\Models\Material;
use App\Models\Session;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function index(Request $request)
    {
        $contents = Content::all();

        return view('content.index', compact('contents'));
    }

    function data(Content $content){


        $material_type = old('type', $content->type) == 2 ? 2:1;
        $material_id = old('material_id', $content->material_id);

        $sessions = Session::get( );
        $topics = Topic::query( )->get( );
        
        $content_types = ContentType::get();
 
        
        $material = Material::query()->where('type', $material_type)->find($material_id);


        $faculty_disciplines = FacultyDiscipline::get();

        $content_type = old( 'content_type', $content->content_type ?? '' );

        return compact( 'sessions', 'topics', 'content_type', 'content_types', 'faculty_disciplines','material'  );
    }


    public function show_material( $material_type, $material_id ){
        $material = Material::query()->where('type', $material_type)->find($material_id);

        if( !$material ) {
            return 'Not Found!';
        }

        return view('content.materials', compact( 'material' ) );
    }

    public function create(Request $request)
    {
        return view('content.create', $this->data(new Content()));
    }

    public function store( ContentStoreRequest $request )
    {
        $content = Content::create( $request->validated( ) );

        $request->session()->flash('content.id', $content->id);

        return redirect()->route('contents.index');
    }

    public function show(Request $request, Content $content)
    {
        return view('content.show', compact('content'));
    }

    public function edit(Request $request, Content $content)
    {
        return view('content.edit' , $this->data( $content ) + compact('content'));
    }

    public function update(ContentUpdateRequest $request, Content $content)
    {
        $content->update($request->validated());

        $request->session()->flash('content.id', $content->id);

        return redirect()->route('contents.index');
    }

    public function destroy(Request $request, Content $content)
    {
        $content->delete();

        return redirect()->route('contents.index');
    }
}
