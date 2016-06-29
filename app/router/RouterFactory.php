<?php

namespace App;

use Nette,
	Nette\Application\Routers\RouteList,
	Nette\Application\Routers\Route,
	Nette\Application\Routers\SimpleRouter,
	Nette\DI\Container,
	Tracy\Debugger;


/**
 * Router factory.
 */
class RouterFactory
{

	/**
	 * @return \Nette\Application\IRouter
	 */
	public static function createRouter(Nette\DI\Container $container)
	{
		$router = new RouteList();
		# AdminModule route
    	$router[] = new Route('admin1896/<presenter>/<action>[/<id>]', array(
            'module'    => 'Admin',
            'presenter' => 'Slides',
            'action'    => 'list',
            'id'        => null
    	));	
    	
		$router[] = new Route('', 'Homepage:default');
		$router[] = new Route('clanky/<id>', 'Articles:article');

		$router[] = new Route('aktuality/programy-pro-verejnost', 'Actuality:sunday');		
		$router[] = new Route('aktuality/<actuality_id>', array(
			'presenter' => 'Actuality',
			'action' => 'detail',
			'actuality_id' => array(
				Route::FILTER_OUT => function ($id) use($container) { return $container->getService('actuality')->getTitleById($id);},
				Route::FILTER_IN => function ($url) use($container) { return $container->getService('actuality')->getIdByTitle($url);},
			),
		));
		/* routs for section photos */
		
		$router[] = new Route('fotky/<galery_id>', array(
			'presenter' => 'Galery',
			'action' => 'photos',
			'galery_id' => array(
				Route::FILTER_OUT => function ($id) use($container) { return $container->getService('galery')->getTitleById($id);},
				Route::FILTER_IN => function ($url) use($container) { return $container->getService('galery')->getIdByTitle($url);},
			),
		));
		
		$router[] = new Route('fotky', 'Galery:galeries');
		
		/* routs for section audio */

		$router[] = new Route('audio/nejnovejsi', 'Audio:latest');		
		$router[] = new Route('audio/autori', 'Audio:interprets');
		$router[] = new Route('audio/roky', 'Audio:years');
		$router[] = new Route('audio/rok/<year>?seskupit=<group_by>', array(
			'presenter' => 'Audio',
			'action' => 'year',
			'group_by' => array(
				Route::FILTER_TABLE => array(
					'autoru' => 'interpret_id',
					'knihy' => 'book_id',
				)
			),
		));
		
		$router[] = new Route('audio/knihy', 'Audio:books');
		$router[] = new Route('audio/<interpret_id>?seskupit=<group_by>', array(
			'presenter' => 'Audio',
			'action' => 'interpret',
			'interpret_id' => array(
				Route::FILTER_OUT => function ($id) use($container) { return $container->getService('interpret')->getTitleById($id);},
				Route::FILTER_IN => function ($url) use($container) { return $container->getService('interpret')->getIdByTitle($url);},
			),
			'group_by' => array(
				Route::FILTER_TABLE => array(
					'knihy' => 'book_id',
					'cas_pridani' => 'time_created',
					'casu' => 'audio_year'
				)
			),
		));
		
		/*
		$router[] = new Route('audio/knihy/<book_id>', array(
			'presenter' => 'Audio',
			'action' => 'book',
			'book_id' => array(
				Route::FILTER_OUT => function ($id) use($container) { return $container->getService('book')->getTitleById($id);},
				Route::FILTER_IN => function ($url) use($container) { return $container->getService('book')->getIdByTitle($url);},
			),
		));
		*/
		/* routs for section audio end */
						
		$router[] = new Route('<presenter>[/<action>][/<id>]', array(
			'presenter' => array(
				Route::FILTER_TABLE => array(
					'aktuality' => 'Actuality',
					'clanky' => 'Articles',
					'fotky' => 'Galery',
					'o-nas' => 'About',
					'odkazy' => 'Links'
				)
			),
			'action' =>	'default',
		));
		return $router;
	}

}
