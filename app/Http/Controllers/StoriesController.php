<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Log;

class StoriesController extends Controller
{
    //
    public function getStories(Request $request){
    	$data['stories'] = DB::table('story_table')
    					->get();

        Log::info("sending data : ");
        Log::info($data);

    	return $data;
    }

    public function getStory(Request $request){
    	$storyid = $request->storyid;
    	$pageNo = $request->pageno;
    	$type = $request->type;
        $optionid = $request->optionid;
        $userid = 0;

    	if($type=="number"){
    		$where = [['story_table.story_id','=',$storyid],['pages.page_no','=',$pageNo]];
    	} else {
    		$where = [['story_table.story_id','=',$storyid],['pages.page_id','=',$pageNo]];

            $data = ['storyid'=>$storyid,'pageid'=>$pageNo,'selected_option'=>$optionid,'userid'=>$userid];

            DB::table('story_answers')->insert($data);
    	}
    	

    	// $data['storyid'] = $request->storyid;
    	// $data['pageno'] = $request->pageno;

    	$data['story'] = DB::table('story_table')
    					->join('pages','story_table.story_id','=','pages.story_id')
                        ->join('story_authors','story_authors.story_id','=','story_table.story_id')
                        ->join('users','users.user_id','=','story_authors.author')
    					->where($where)
                        ->select('*','story_table.date_created as dateCreated')
    					->get();

    	$data['options'] = DB::table('story_options')
    					->join('page_link','page_link.option_id','=','story_options.option_id')
    					->where('page_id','=',$data['story'][0]->page_id)
                        ->select('*','story_options.option_id as optionid')
    					->get();


        Log::info("sending data : ");
        Log::info($data);



    	return $data;

    	// select * from story_options join page_link on story_options.option_id = page_link.option_id where page_id = 'P105';

    	// select * from story_table st join pages p on st.story_id = p.story_id where st.story_id = 'S3' and p.page_no = '106';
    }
}
