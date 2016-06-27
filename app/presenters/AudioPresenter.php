<?php
namespace App\Presenters;

use Nette,
	App\Model;
use Tracy\Debugger;
use Nette\Application\Responses\FileResponse;
use Nette\Environment;
use FileDownloader\FileDownload;
use Nette\Database\SqlLiteral;

class AudioPresenter extends BasePresenter	{
	
    /** @var object */
    private $model;
    
    protected function startup()  {
        parent::startup();
		$this->model = $this->audio;
	}
	
	public function beforeRender() {
		parent::beforeRender();
		$this->template->addFilter('czPlural', function ($count) {
			if($count == 1)
				return $count." přednáška";
			elseif($count >= 2 && $count <= 4)
				return $count." přednášky";
			else 
				return $count." přednášek";
		});										   
	}
	
	public function renderLatest() {
		$detect = new Mobile_Detect;
		$this->template->isMobile = $detect->isMobile();		
		$this->template->back = "interprets";
			
		$this->template->lectures = $this->audio->findAll()
												->where(new SqlLiteral("`time_created` > '2016-03-01 00:00:00'"))
												->order('time_created DESC')
												->limit(20);
	}
	
	public function renderInterprets()	{
		$this->template->interprets = $this->interpret->getAll();	
    }
	
	public function renderYears()	{
		$this->template->years = $this->audio->findAll()
											 ->select('audio_year AS year, COUNT(*) AS count')
											 ->group('audio_year')
											 ->order('audio_year DESC');
	}
	
	public function renderYear($year, $group_by = 'audio_interpret_id') {
		$groups = $this->audio->findBy(['audio_year' => $year])
							  ->group($group_by);
		$lectures = [];
									  
		foreach($groups as $group) {
			$lectures[$group->id] = $this->audio->findBy(['audio_year' => $year, 
														  $group_by => $group->$group_by
														])
												->order('audio_year DESC');
		}
		
		if($group_by == 'book_id') {
			$this->template->unclasified = $this->audio->findBy(['audio_year' => $year])
									   				   ->where('book_id IS NULL AND seminar = ? AND sankirtan = ?', array(0, 0));
									   				   
			$this->template->seminars = $this->audio->findBy(['audio_year' => $year])
													->where('seminar = ? AND sankirtan = ?', array(1, 0));
													
			$this->template->sankirtan = $this->audio->findBy(['audio_year' => $year])
													 ->where('seminar = ? AND sankirtan = ?', array(0, 1));

		}
		
		$this->template->groups = $groups;
		$this->template->lectures = $lectures;
		$this->template->year = $year;
		$this->template->group_by = $group_by;
		$detect = new Mobile_Detect;
   		$this->template->isMobile = $detect->isMobile();
   		
   		$this->template->back = "years";				
	}

	public function renderBooks()	{
		$this->template->bg = $this->book->findBy(['abbreviation' => 'BG'])
										 ->fetch();

		$this->template->sb = $this->audio->findAll()
										  ->where("book.abbreviation LIKE 'SB%'")
										  ->count();

		$this->template->cc = $this->audio->findAll()
										  ->where("book.abbreviation LIKE 'CC%'")
										  ->count();

		$this->template->seminar = $this->audio->findBy(['seminar' => 1])
											   ->count();
												
		$this->template->sankirtan = $this->audio->findBy(['sankirtan' => 1])
												 ->count();

										 
		$this->template->unclasified = $this->audio->findAll()
												   ->where('book_id IS NULL AND seminar = ? AND sankirtan = ?', array(0, 0))
												   ->count();
	}
	
	public function renderBook($book_id, $group_by = 'audio_interpret_id') {
		$groups = $this->audio->findBy(['book_id' => $book_id])
							  ->group($group_by)
  							  ->order('audio_year DESC');
		$lectures = [];
									  
		foreach($groups as $group) {
			$lectures[$group->id] = $this->audio->findBy(['book_id' => $book_id, 
														  $group_by => $group->$group_by
														]);
			if($group_by == "audio_interpret_id")
				$lectures[$group->id]->order('chapter ASC, verse ASC');
			else {
				$lectures[$group->id]->order('audio_year DESC, audio_month DESC, audio_day DESC');
			}
				
		}
		
		$this->template->groups = $groups;
		$this->template->lectures = $lectures;
		$book = $this->book->get($book_id);
		if($book) {
			if(strpos($book->abbreviation, "ŚB") === 0) {
				$this->template->back = "sb";
			}
			elseif(strpos($book->abbreviation, "CC") === 0) {
				$this->template->back = "cc";
			}
			else {
				$this->template->back = "books";
			}
		}
		else {
			$this->template->back = "books";
		}
		
		$this->template->book = $book;
		$this->template->group_by = $group_by;
   		$detect = new Mobile_Detect;
   		$this->template->isMobile = $detect->isMobile();
	}

	/* Pro semináře a sankírtanové lekce */
	public function renderByType($type = "seminar", $group_by = 'audio_interpret_id') {
		$groups = $this->audio->findBy([$type => 1])
							  ->group($group_by);
							  
		if($group_by == 'audio_year')
			$groups->order('audio_year DESC');							  
							  
		$lectures = [];
									  
		foreach($groups as $group) {
			$lectures[$group->id] = $this->audio->findBy([$type => 1,
														  $group_by => $group->$group_by])
												->order('audio_year DESC, audio_month DESC, audio_day DESC');
		}

		if($group_by == 'book_id') {
			$this->template->unclasified = $this->audio->findBy([$type => 1])
									   				   ->where('book_id IS NULL');
		}

		$this->template->groups = $groups;
		$this->template->lectures = $lectures;
		$this->template->type = $type;
		$this->template->group_by = $group_by;
   		$detect = new Mobile_Detect;
   		$this->template->isMobile = $detect->isMobile();
	}
	
	public function renderUnclasified($group_by = 'audio_interpret_id') {
		$groups = $this->audio->findAll()
							  ->where('book_id IS NULL AND seminar = ? AND sankirtan = ?', array(0, 0))
							  ->group($group_by);
  							  
		if($group_by == 'audio_year')
			$groups->order('audio_year DESC');	
			
		$lectures = [];
									  
		foreach($groups as $group) {
			$lectures[$group->id] = $this->audio->findBy([$group_by => $group->$group_by])
												->where('book_id IS NULL AND seminar = ? AND sankirtan = ?', array(0, 0))
												->order('audio_year DESC, audio_month DESC, audio_day DESC');
		}
		
		$this->template->groups = $groups;
		$this->template->lectures = $lectures;
		$this->template->book = false;
		$this->template->back = "books";
		$this->template->group_by = $group_by;
   		$detect = new Mobile_Detect;
   		$this->template->isMobile = $detect->isMobile();
	}
	
	public function renderSb() {
		$this->template->books = $this->book->findAll()
											 ->where("book.abbreviation LIKE 'SB%'");
	}
	

	public function renderCc() {
		$this->template->books = $this->book->findAll()
											 ->where("book.abbreviation LIKE 'CC%'");
	}	
	
	public function renderChooseType($interpret_id) {
		$interpret = $this->interpret
 					      ->get($interpret_id);
 							    
		$live_count = $interpret->related("audio", "audio_interpret_id")
						  	    ->where(array("type" => "live"))
							  	->count();							  	

		$records_count = $interpret->related("audio", "audio_interpret_id")
							  	   ->where(array("type" => "record"))
							  	   ->count();
		
		if($live_count == 0) {
			$this->redirect('audioFolder', $interpret_id, "record");
		}
		
		if($records_count == 0) {
			$this->redirect('audioFolder', $interpret_id, "live");
		}
		
		$this->template->interpret = $interpret;
		$this->template->live_count = $live_count;
		$this->template->records_count = $records_count;
	}
	
	public function renderInterpret($interpret_id, $group_by = "audio_year") {
		$groups = $this->audio->findBy(['audio_interpret_id' => $interpret_id]);
  							  
		if($group_by == "audio_year") {
			$groups->order('audio_year DESC')
				   ->group($group_by);
		}			
		elseif($group_by == "book_id") {
			$groups->order('book.id ASC')
				   ->group($group_by);
		}
		elseif($group_by == "time_created") {
			$groups->order('time_created DESC');
			$groups->group('YEAR(time_created)');
		}					
			
		$lectures = [];
		
									  
		foreach($groups as $group) {			
			if($group_by == 'time_created') {
				$lectures[$group->id] = $this->audio->findBy(['audio_interpret_id' => $interpret_id, 
											  				  "YEAR(".$group_by.")" => date('Y', strtotime($group->$group_by))])
											  		->order('time_created DESC'); 
			}
			else {
				$lectures[$group->id] = $this->audio->findBy(['audio_interpret_id' => $interpret_id, 
											  				  $group_by => $group->$group_by])
											  		->order('audio_year DESC, audio_month DESC, audio_day DESC'); 
			}
		}
		
		
		if($group_by == 'book_id') {
			$this->template->unclasified = $this->audio->findBy(['audio_interpret_id' => $interpret_id])
									   				   ->where('book_id IS NULL AND seminar = ? AND sankirtan = ?', array(0, 0));
									   				   
			$this->template->seminars = $this->audio->findBy(['audio_interpret_id' => $interpret_id])
													->where('seminar = ? AND sankirtan = ?', array(1, 0));
													
			$this->template->sankirtan = $this->audio->findBy(['audio_interpret_id' => $interpret_id])
													 ->where('seminar = ? AND sankirtan = ?', array(0, 1));
		}
		
		$this->template->groups = $groups;
		$this->template->lectures = $lectures;
		$this->template->interpret = $this->interpret->get($interpret_id);
		$this->template->group_by = $group_by;
		$detect = new Mobile_Detect;
   		$this->template->isMobile = $detect->isMobile();
   		
   		$this->template->back = "interprets";
	}
	
	public function actionDownloadMp3($id) {
		Debugger::fireLog($id);
		if (!$audio = $this->audio->get($id))
            throw new Nette\Application\BadRequestException;

		$this->sendResponse(new FileResponse("./mp3/".$audio->audio_interpret->mp3_folder."/".$audio->mp3_file));
	}
}
