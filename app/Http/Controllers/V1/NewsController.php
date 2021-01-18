<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\News;
use App\Transformers\NewsTransformer;
use Exception;
use Illuminate\Support\Facades\DB;


class NewsController extends Controller
{
    public function create(Request $request) 
    {    
        DB::beginTransaction();
        try {
            $validator = validator($request->all(), [ 
                'title'                         => 'required',
                'description'                   => 'required|min:5',
            ]); 
    
            if ($validator->fails()) {
                return $this->errorResponse($validator->errors());
            }
            $news = News::updateOrCreate(
                [
                    "user_id"       => user()->id,  
                    "title"         => $request->title
                ],
                [
                    "user_id"       => user()->id,
                    "title"         => $request->title,
                    "description"   => $request->description
                ]
            );

            DB::commit();

            return $this->showResultV2('data created', $this->item($news, new NewsTransformer(), 'user'));

        } catch(Exception $e) {
            DB::rollBack();
            return $this->realErrorResponse($e);
        }
        
    }

    public function getAll()
    {
        $news = News::whereUserId(user()->id)->get();
        
        return $this->showResultV2('data found', $this->collection($news, new NewsTransformer()));
    }

    public function show($id)
    {
        $news = News::whereId($id)->whereUserId(user()->id)->first();

        if (!$news) {
            return $this->errorResponse('Data not found', 404);
        }

        return $this->showResultV2('data found', $this->item($news, new NewsTransformer(), 'user'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $news = News::whereId($id)->whereUserId(user()->id)->first();

            if (!$news) {
                return $this->errorResponse('Data not found', 404);
            }

            $validator = validator($request->all(), [ 
                'title'                         => 'required',
                'description'                   => 'required|min:5',
            ]); 

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors());
            }

            $news->title        = $request->title;
            $news->description  = $request->description;
            $news->save();
            
            DB::commit();
            return $this->showResultV2('data found', $this->item($news, new NewsTransformer(), 'user'));
        } catch (Exception $e) {
            DB::rollBack();
            return $this->realErrorResponse($e);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $news = News::whereId($id)->whereUserId(user()->id)->first();

            if (!$news) {
                return $this->errorResponse('Data not found', 404);
            }
            $news->delete();

            DB::commit();
            return $this->showResultV2('data deleted', []);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->realErrorResponse($e);
        }
    }

} 

