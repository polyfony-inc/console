<?php

/*
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 * Don't look at the source code of this class
 *  It is utterly disgusting, vomit may occur
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 *
 */

namespace Polyfony;

use Doctrine\Common\Inflector\Inflector;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestSuite;
use PHPUnit\TextUI\TestRunner;
use Symfony\Component\Finder\Finder;

// the class itself
class Console {

	// cute numbers in ascii art
	protected static $_ascii_numbers = [
			1=>[
				" . ",
				"'| ",
				" ' "
			],
			2=>[
				".-.",
				".''",
				"`--"
			],
			3=>[
				"-. ",
				"-| ",
				"-' "
			],
			4=>[
				". .",
				"`-|",
				"  '"
			],
			5=>[
				".-.",
				"``.",
				"--'"
			],
			6=>[
				".-.",
				"|-.",
				"`-'"
			],
			7=>[
				".-.",
				" .'",
				"'  "
			],
			8=>[
				".-.",
				")-(",
				"`-'"
			],
			9=>[
				".-.",
				"`-|",
				"`-'"
			],
			0=>[
				".-.",
				"|\|",
				"`-'"
			],
			'.'=>[
				"   ",
				"   ",
				":: "
			]
		];


	// the root path of the project
	protected static $_root_path = '';

	// the cache folders relative to root path
	protected static $_cache_folders = [
		'Private/Storage/Cache/',
		'Private/Storage/Cache/Assets/Js/',
		'Private/Storage/Cache/Assets/Css/'
	];

	// list of available command, syntax, configs, and usage
	protected static $_commands 		= [
		'help'			=>[
			'usage'			=>'Console help',
			'description'	=>'Display this help message'
		],
		'sync'					=>[
			// usage for that command
			'usage'			=>'Console sync [up/down] [cleancache:yes/no]',
			// description for that command
			'description'	=>'Synchronizes your project from or to a remote server via SSH and cleans its remote cache',
			// mapping of cli arguments to their name
			'arguments'		=>[
				'Direction',
				'CleanCache'
			],
			// configs element for that command
			'configs'=>[
				'rsync_options'	=>'-avzlp --delete --chmod=ugo+rwX --exclude=".DS_Store" -e "ssh -p _port_" ',
				'folders'		=>[]
			]
		],
		'backup-database'		=>[
			'usage'			=>'Console backup-database [dev/prod]',
			'description'	=>'Creates a compressed copy of the local database (you must specify the current environment)',
			// mapping of cli arguments to their name
			'arguments'		=>[
				'Where'
			],
			// configs element for that command
			'configs'=>[
				'create_archive_folder_command'=>'mkdir -p Private/Storage/Data/Backups/Database/',
				'archive_command'		=>'tar -cJf __destination__ __relative_database_path__ > /dev/null'
			]
		],
		//'check-config'			=>[
		//	'usage'			=>'Console check-config',
		//	'description'	=>'Checks if the configuration of the framework is optimal'
		//],
		//'vacuum-database'		=>[
		//	'usage'			=>'Console vacuum-database',
		//	'description'	=>'Executes a vacuum command on the database to free up space'
		//],
		'clean-cache'			=>[
			'usage'			=>'Console clean-cache',
			'description'	=>'Empties the Private/Storage/Cache folder and it subdirectories'
		],
		'clean-remote-cache'	=>[
			'usage'			=>'Console clean-remote-cache',
			'description'	=>'Empties the Private/Storage/Cache folder and it subdirectories on the remote server'
		],
		'generate-symlinks'		=>[
			'usage'			=>'Console generate-syminks',
			'description'	=>'Generates symlinks from Private/Bundles/{$1}/Assets/{$2} to Public/Assets/{$2}/{$1}'
		],
		'run-tests'		=>[
			'usage'			=>'Console run-tests',
			'description'	=>'Runs all available tests in Private/Tests/'
		],
		'run-test'		=>[
			'usage'			=>'Console run-test [test-name]',
			'description'	=>'Runs a specific test',
			'arguments'		=>[
				'Test'
			]
		],
		'generate-bundle'		=>[
			'usage'			=>'Console generate-bundle [bundle-name]',
			'description'	=>'Generate a bundle with full CRUD capabilities based on current database tables',
			'arguments'		=>[
				'Bundle'
			]
		],
		// 'generate-models'		=>[
		// 	'usage'			=>'Console generate-models',
		// 	'description'	=>'Generates all the Private/Models/{Table} based on current database tables'
		// ],
		'generate-model'		=>[
			'usage'			=>'Console generate-model [TableName]',
			'description'	=>'Generates a model file for a given table name',
			'arguments'		=>[
				'Table'
			]
		],
		// 'generate-controllers'	=>[
		// 	'usage'			=>'Console generate-controllers [bundle-name]',
		// 	'description'	=>'Generates all controllers in a bundle based on current database tables',
		// 	'arguments'		=>[
		// 		'Bundle'
		// 	]
		// ],
		'generate-controller'	=>[
			'usage'			=>'Console generate-controller [BundleName] [TableName]',
			'description'	=>'Generates a controller for a given table name',
			'arguments'		=>[
				'Bundle',
				'Table'
			]
		],
		'generate-views'		=>[
			'usage'			=>'Console generate-views [BundleName] [TableName]',
			'description'	=>'Generates all views for a given table name',
			'arguments'		=>[
				'Table',
				'Bundle'
			]
		],
		'generate-routes'		=>[
			'usage'			=>'Console generate-routes [BundleName]',
			'description'	=>'Generates CRUD routes for all tables in a given bundle',
			'arguments'		=>[
				'Bundle'
			]
		],
		'generate-route'		=>[
			'usage'			=>'Console generate-route [BundleName] [TableName]',
			'description'	=>'Generates CRUD route given table name and bundle',
			'arguments'		=>[
				'Table',
				'Bundle'
			]
		],
		// 'generate-view'			=>[
		// 	'usage'			=>'Console generate-view [index/edit/delete/create] [table-name] [bundle-name]',
		// 	'description'	=>'Generates a view for a given table name and action',
		// 	'arguments'		=>[
		// 		'Action',
		// 		'Table',
		// 		'Bundle'
		// 	]
		// ]

	];

	public static function run() {

		// if a command is provided and is incorrect, or if no command is provided
		if(
			!isset($_SERVER['argv'][1]) || 
			(
				isset($_SERVER['argv'][1]) && 
				!array_key_exists($_SERVER['argv'][1], self::$_commands)
			)
		) {
			
			// show the help
			self::help();

			// stop execution
			return;

		}
		// a command is provided and it exists
		elseif(
			isset($_SERVER['argv'][1]) && 
			array_key_exists($_SERVER['argv'][1], self::$_commands)
		) {

			// get the command
			$command = $_SERVER['argv'][1];

			// get the total number of arguments required by that command
			$required_arguments_count = isset(self::$_commands[$command]['arguments']) ? 
				count(self::$_commands[$command]['arguments']) : 0;

			// if that command requires parameters and that we don't have enough
			if(
				$required_arguments_count && 
				count($_SERVER['argv']) < ($required_arguments_count + 2)
			) {

				// show the usage for that specific command
				Console\Format::line('  Not enough arguments provided' , 'red', null, ['bold']);

				// show that command's info
				Console\Format::raw('  '.self::$_commands[$command]['usage'] . ' : ', 'green');
				Console\Format::raw(self::$_commands[$command]['description'], 'white', null, ['italic']);
				// clean new line
				echo "\n";


			}
			// the arguments seem ok
			else {

				// we have a valid command, we can start working
				self::defineProjectRootPath();

				// depending on the command
				switch($command) {

					case 'clean-cache':

						self::cleanCacheCommand();

					break;

					// this should be renamed to clean-cache 
					// and local/remote should become a parameter
					case 'clean-remote-cache':

						self::cleanRemoteCacheCommand();

					break;

					case 'backup-database':

						// if the direction is valid and cache cleaning if valid
						if(
							in_array($_SERVER['argv'][2], ['dev','prod'])
						) {
							// actually backup
							self::backupDatabaseCommand(
								// pass the environment
								$_SERVER['argv'][2]
							);
						}
						// the direction is invalid
						else {
							// only show the help
							self::help();
						}

					break;

					case 'sync':

						// if the direction is valid and cache cleaning if valid
						if(
							in_array($_SERVER['argv'][2], ['up','down']) && 
							in_array($_SERVER['argv'][3], ['yes','no'])
						) {
							// actually sync
							self::syncCommand(
								// pass the direction
								$_SERVER['argv'][2], 
								// pass the cache cleaning option
								$_SERVER['argv'][3]
							);
						}
						// the direction is invalid
						else {
							// only show the help
							self::help();
						}

					break;

					case 'check-config':

						self::checkConfigCommand();

					break;

					case 'vacuum-database':

						self::vacuumDatabaseCommand();

					break;

					case 'generate-symlinks':

						self::generateSymlinksCommand();

					break;

					case 'run-tests':

						self::runTests();

					break;

					case 'run-test':

						self::runTest(
							$_SERVER['argv'][2]
						);

					break;

					case 'generate-bundle':

						self::generateBundle(
							$_SERVER['argv'][2]
						);

					break;

					case 'generate-controllers':

					break;

					case 'generate-controller':

						self::generateController(
							$_SERVER['argv'][2],
							$_SERVER['argv'][3]
						);
						
					break;

					case 'generate-models':

						// read the database schema, then proceed to generate all models

					break;

					case 'generate-model':

						self::generateModel(
							$_SERVER['argv'][2]
						);

					break;

					case 'generate-views':

						self::generateViews(
							$_SERVER['argv'][2],
							$_SERVER['argv'][3]
						);

					break;

					case 'generate-view':

					break;

					case 'generate-routes':

					break;

					case 'generate-route':

						self::generateRoute(
							$_SERVER['argv'][2],
							$_SERVER['argv'][3]
						);

					break;

					case 'help':
					default:

						self::help();

					break;

				}

			}

		}

	}

	private static function printArtwork() {

		Console\Format::line(
			"   _           _           \n".
			"  |_) _  |   _|_ _  ._     \n".
			"  |  (_) | \/ | (_) | | \/ \n".
			"           /            /  ",
			'cyan',
			null
		);

		Console\Format::line(
			'  version: '. self::getFrameworkVersion()."\n",
			'yellow',
			'bold'
		);


	}

	private static function getFrameworkVersion() :string {

		// set the path of the composer.json file
		$composer_file_path = self::$_root_path.'composer.json';
		// we have that file available
		if(file_exists($composer_file_path)) {
			// parse that file
			$composer_manifest = json_decode(
				file_get_contents(
					$composer_file_path
				)
			);
			// return the version number
			return (string) $composer_manifest->version;
		}
		// we do not have that file, most likely we are in production and it has not been pushed
		else {
			// we have no clue
			return 'unknown';
		}

	} 

	private static function help() {
		
		// greatings
		self::printArtwork();

		// main usage
		Console\Format::line('Usage' , 'white', null, ['bold']);
		Console\Format::line('  command [arguments]' , 'green', null);
		Console\Format::line('');

		// available commands
		Console\Format::line('Available commands' , 'white', null, ['bold']);

		// build the list of available commands
		foreach(self::$_commands as $command => $infos) {

			// show that command's info
			Console\Format::raw('  '. str_pad($command,22,' '), 'green');
			Console\Format::raw($infos['description'], 'white', null, ['italic']);
			echo "\n";
		}

		// skip a line
		Console\Format::line('');

	}

	private static function deduceTableSchema(
		string $table_name
	) :array {

		// case conversion
		$table_name = Inflector::classify(
			$table_name
		);
		// plural conversion
		$object_singular 	= Inflector::classify(
			Inflector::singularize(
				$table_name
			)
		);
		// get a slug version (mostly for routes)
		$table_slug = str_replace(
			'_', 
			'-', 
			Inflector::tableize($table_name)
		);

		// read the configuration file
		$full_ini = array_replace(
			parse_ini_file(self::$_root_path.'Private/Config/Config.ini', true),
			parse_ini_file(self::$_root_path.'Private/Config/Dev.ini', true)
		);

		// get the database path
		$database_path = realpath(
			self::$_root_path . 
			'Public/'.
			$full_ini['database']['database']
		);

		$columns = [];
		$database = new \PDO('sqlite:'.$database_path);

		foreach(
			$database->query('PRAGMA table_info(  ' . $table_name.' )') 
			as $column
		) {
			
			$columns[$column['name']]['type'] 		= $column['type'];
			$columns[$column['name']]['is_select'] 	= false;
			$columns[$column['name']]['is_date'] 	= false;
			$columns[$column['name']]['is_numeric']	= false;
			$columns[$column['name']]['is_disabled']= false;
			$columns[$column['name']]['is_multiple']= false;

			// if it's a primary keys
			if($column['pk']) {
				$columns[$column['name']]['is_disabled'] = true;
			}

			// if it's a relation column
			if(
				stripos($column['name'], 'id_') === 0 || 
				stripos($column['name'], 'is_') === 0 
			) {

				// the foreign model requires case translation

				// local constant

				$columns[$column['name']]['is_select'] = true;
			}
			// if it's a date
			elseif(
				stripos($column['name'], '_date') !== false 
			) {
				$columns[$column['name']]['is_date'] = true;
			}
			if(
				$column['type'] == 'numeric' || 
				$column['type'] == 'integer'
			) {
				$columns[$column['name']]['is_numeric'] = true;
			}
			if(
				stripos($column['name'], '_array') !== false 
			) {
				$columns[$column['name']]['is_multiple'] = true;
			}
		}

		return [
			$columns,
			$table_name,
			$object_singular,
			$table_slug
		];

	}

	// check weither or not all test pass
	private static function doTestsPass() :bool {

		/*

		// instanciate a PHPUnit test suite
		$test = new TestSuite;

		// add our test to the testsuite
		$test->addTestSuite(\Tests\FuckTest::class);

		// run said test
		$result = $test->run();

		// display the results somehow
		// $result->wasSuccessful()

		*/

	}

	private static function getAvailableTests() :iterable {
		// instanciate a files finder
		$finder = new Finder;
		// find all files in the current directory
		$finder->files()->in(self::$_root_path.'Private/Tests/');
		// return the list of found files
		return $finder;
	}

	// run test and display in the CLI
	private static function runTests() {

		// pretty introduction
		Console\Format::block(
			'Running all available tests', 
			'cyan', 
			null, 
			['bold']
		);

		// for each found testset
		foreach(self::getAvailableTests() as $file) {
			// run those tests
			self::runTest($file->getRelativePathname());
		}

	}

	// run test and display in the CLI
	private static function runTest(
		string $test_name
	) {

		// rename the test so that it understand namespaces
		$test_name = str_replace(['/','.php'],['\\',''], $test_name);

		// pretty introduction
		Console\Format::block(
			'Testing '.$test_name, 
			'yellow', 
			null, 
			[]
		);

		// instanciate the framework environment
		new Front\Test;
		
		// instanciate a test suite
		$suite = new TestSuite;

		// add the desired test to the suite
		$suite->addTestSuite('\Tests\\'.$test_name);
		
		// instanciate a test runner
		$runner = new TestRunner;

		// run the testsuite (and display the results directly)
		$runner->run($suite, [], false);

	}

	private static function generateBundle(
		string $bundle_name
	) {

		// case convert the bundle name
		$bundle_name = Inflector::classify($bundle_name);

		// get a list of all tables available
		// read the configuration file
		$full_ini = array_replace(
			parse_ini_file(self::$_root_path.'Private/Config/Config.ini', true),
			parse_ini_file(self::$_root_path.'Private/Config/Dev.ini', true)
		);

		// get the database path
		$database_path = realpath(
			self::$_root_path . 
			'Public/'.
			$full_ini['database']['database']
		);

		$database = new \PDO('sqlite:'.$database_path);

		foreach(
			$database->query('SELECT name FROM sqlite_sequence;') 
			as $table
		) {
			$table_name = $table['name'];

			// generate all models
			self::generateModel($table_name);

			// generate all bundle's controllers
			self::generateController(
				$bundle_name,
				$table_name
			);

			// generate all bundle's views
			self::generateViews(
				$bundle_name,
				$table_name
			);

			// generate all bundle's route
			self::generateRoute(
				$bundle_name,
				$table_name
			);

		}

	}

	private static function generateRoute(
		string $bundle_name, 
		string $table_name
	) {

		// case convert the bundle name
		$bundle_name = Inflector::classify($bundle_name);
		
		// get some table informations
		list(
			$table_schema,
			$table_name,
			$object_singular,
			$table_slug
		) = self::deduceTableSchema(
			$table_name
		);

		// pretty introduction
		Console\Format::block(
			'Generating ' . $table_name . ' routes in the '.$bundle_name.' bundle', 
			'cyan', 
			null, 
			['bold']
		);

		// get the template
		$template = file_get_contents(
			self::$_root_path . 
			'Private/Vendor/polyfony-inc/console/templates/Route.php'
		);

		// customize it
		$template = str_replace(
			[
				'__Bundle__',
				'__bundle__',
				'__Table__',
				'__table__',
				'__Singular__',
				'__singular__',
				'__datetime__'
			],
			[
				$bundle_name,
				strtolower($bundle_name),
				$table_name,
				$table_slug,
				$object_singular,
				strtolower($object_singular),
				date('d/m/Y h:i')
			],
			$template
		);

		// the folder containing that controller
		$route_root_path = self::$_root_path . 
			'Private/Bundles/'.$bundle_name.
			'/Loader/';

		// if it doesn't exist yet
		if(!is_dir($route_root_path)) {
			// create it
			mkdir($route_root_path, 0777, true);
		}

		$route_path = $route_root_path . 'Route.php';

		if(file_exists($route_path)) {
			// use existing routes
			$template  = 
				file_get_contents($route_path) . 
				"\n" . 
				$template;
			// remove existing route file
			unlink($route_path);
		}

		// save the file
		file_put_contents(
			$route_path, 
			$template
		);
		// some feedback
		Console\Format::line(
			'✓ the '.$table_name.' route file has been generated in bundle '.$bundle_name, 
			'green', 
			null
		);


	}

	private static function generateController(
		string $bundle_name,
		string $table_name
	) {

		// case convert the bundle name
		$bundle_name = Inflector::classify($bundle_name);
		
		// get some table informations
		list(
			$table_schema,
			$table_name,
			$object_singular,
			$table_slug
		) = self::deduceTableSchema(
			$table_name
		);

		// pretty introduction
		Console\Format::block(
			'Generating the ' . $table_name . 'Controller', 
			'cyan', 
			null, 
			['bold']
		);

		// get the template
		$template = file_get_contents(
			self::$_root_path . 
			'Private/Vendor/polyfony-inc/console/templates/Controller.php'
		);
		

		// customize it
		$template = str_replace(
			[
				'__Table__',
				'__table__',
				'__Singular__',
				'__singular__',
				'__datetime__',
				'__Bundle__'
			],
			[
				$table_name,
				$table_slug,
				$object_singular,
				strtolower($object_singular),
				date('d/m/Y h:i'),
				$bundle_name
			],
			$template
		);

		// the folder containing that controller
		$controller_root_path = self::$_root_path . 
			'Private/Bundles/'.$bundle_name.
			'/Controllers/';

		// if it doesn't exist yet
		if(!is_dir($controller_root_path)) {
			// create it
			mkdir($controller_root_path, 0777, true);
		}

		$controller_path = $controller_root_path . 
			$table_name.'.php';

		if(file_exists($controller_path)) {
			// some feedback
			Console\Format::line(
				'X the '.$table_name.'Controller already exist in Bundles/'.$bundle_name, 
				'red', 
				null
			);
		}
		else {
			// save the file
			file_put_contents(
				$controller_path, 
				$template
			);
			// some feedback
			Console\Format::line(
				'✓ the '.$table_name.'Controller has been generated in Bundles/'.$bundle_name, 
				'green', 
				null
			);
		}

	}

	private static function generateViews(
		string $bundle_name, 
		string $table_name
	) {

		// I'm so ashamed of having to produce such poor quality code...
		// we should really, REALLY use an actual framework
		// instead of doing this messy stuff

		// case convert the bundle name
		$bundle_name = Inflector::classify($bundle_name);

		// get some table informations
		list(
			$table_schema,
			$table_name,
			$object_singular,
			$table_slug
		) = self::deduceTableSchema(
			$table_name
		);

		// pretty introduction
		Console\Format::block(
			'Generating views for '.$table_name.' in the '.$bundle_name . ' bundle', 
			'cyan', 
			null, 
			['bold']
		);

		$views_root_folder = 
			self::$_root_path . 
			'Private/Bundles/' . 
			$bundle_name . 
			'/Views/' . 
			$table_name. '/';

		// if the views folder doesn't exist yet
		if(!is_dir($views_root_folder)) {
			// create it
			mkdir($views_root_folder, 0777, true);
		}

		// the index view, for listing and searching
		$index_view_path 		= $views_root_folder . 'Index.php';
		$index_view_template 	= file_get_contents(
			self::$_root_path . 
			'Private/Vendor/polyfony-inc/console/templates/Views/Index.php'
		);
		
		// the edit view, for editing and creating new records
		$edit_view_path 		= $views_root_folder . 'Edit.php';
		$edit_view_template 	= file_get_contents(
			self::$_root_path . 
			'Private/Vendor/polyfony-inc/console/templates/Views/Edit.php'
		);
		
		// the delete view, for confirming deletion
		$delete_view_path 		= $views_root_folder . 'Delete.php';
		$delete_view_template 	= file_get_contents(
			self::$_root_path . 
			'Private/Vendor/polyfony-inc/console/templates/Views/Delete.php'
		);

		// build the table's legend
		$index_columns_legend 	= [];
		$index_columns_filters 	= [];
		$index_columns_values 	= [];
		$edit_form_fields = [];
		foreach($table_schema as $name => $properties) {
			
			

			$index_columns_legend[] = str_replace(
				'__column__',
				$name,
				file_get_contents(
					self::$_root_path . 
					'Private/Vendor/polyfony-inc/console/templates/Views/Index/Legend.php'
				)
			);

			if($properties['is_select']) {

				$relation = Inflector::classify(
					str_replace(['id_','is_'],'',$name)
				);


				$edit_form_fields[] = str_replace(
					[
						'__column__',
						'__Table__',
						'__table__',
						'__Singular__',
						'__singular__',
						'__Relation__',
						'__Singular__'
					],
					[
						$name,
						$table_name,
						$table_slug,
						$object_singular,
						strtolower($object_singular),
						$relation,
						$object_singular
					],
					file_get_contents(
						self::$_root_path . 
						'Private/Vendor/polyfony-inc/console/templates/Views/Edit/Select.php'
					)
				);


				// FILTERS
				$index_columns_filters[] = str_replace(
					[
						'__column__',
						'__Table__',
						'__table__',
						'__Singular__',
						'__singular__',
						'__Relation__'
					],
					[
						$name,
						$table_name,
						$table_slug,
						$object_singular,
						strtolower($object_singular),
						$relation
					],
					file_get_contents(
						self::$_root_path . 
						'Private/Vendor/polyfony-inc/console/templates/Views/Index/Select.php'
					)
				);


				// VALUES
				$index_columns_values[] = str_replace(
					[
						'__column__',
						'__Table__',
						'__table__',
						'__Singular__',
						'__singular__',
						'__Relation__'
					],
					[
						$name,
						$table_name,
						$table_slug,
						$object_singular,
						strtolower($object_singular),
						$relation
					],
					file_get_contents(
						self::$_root_path . 
						'Private/Vendor/polyfony-inc/console/templates/Views/Index/ColumnRelation.php'
					)
				);
			}
			elseif($properties['is_multiple']) {

				$relation = Inflector::classify(
					str_replace(['_array'],'',$name)
				).'Labels';

				$edit_form_fields[] = str_replace(
					[
						'__column__',
						'__Table__',
						'__table__',
						'__Relation__',
						'__Singular__',
						'__singular__'
					],
					[
						$name,
						$table_name,
						$table_slug,
						$relation,
						$object_singular,
						strtolower($object_singular)
					],
					file_get_contents(
						self::$_root_path . 
						'Private/Vendor/polyfony-inc/console/templates/Views/Edit/Select.php'
					)
				);

				// FILTERS
				$index_columns_filters[] = str_replace(
					[
						'__column__',
						'__Table__',
						'__table__',
						'__Relation__',
						'__Singular__',
						'__singular__'
					],
					[
						$name,
						$table_name,
						$table_slug,
						$relation,
						$object_singular,
						strtolower($object_singular)
					],
					file_get_contents(
						self::$_root_path . 
						'Private/Vendor/polyfony-inc/console/templates/Views/Index/Select.php'
					)
				);

				$index_columns_values[] = str_replace(
					[
						'__column__',
						'__Table__',
						'__table__',
						'__Relation__',
						'__Singular__',
						'__singular__'
					],
					[
						$name,
						$table_name,
						$table_slug,
						$relation,
						$object_singular,
						strtolower($object_singular)
					],
					file_get_contents(
						self::$_root_path . 
						'Private/Vendor/polyfony-inc/console/templates/Views/Index/ColumnRelation.php'
					)
				);
			}
			else {

				if(!$properties['is_disabled']) {

					// EDIT
					$edit_form_fields[] = str_replace(
						[
							'__column__',
							'__Table__',
							'__table__',
							'__Singular__',
							'__singular__',
							'__AdditionnalClasses__'
						],
						[
							$name,
							$table_name,
							$table_slug,
							$object_singular,
							strtolower($object_singular),
							$properties['is_date'] ? 'date' : ''
						],
						file_get_contents(
							self::$_root_path . 
							'Private/Vendor/polyfony-inc/console/templates/Views/Edit/Input.php'
						)
					);

				}

				// FILTERS
				$index_columns_filters[] = str_replace(
					[
						'__Table__',
						'__table__',
						'__Singular__',
						'__singular__',
						'__column__',
					],
					[
						$table_name,
						$table_slug,
						$object_singular,
						strtolower($object_singular),
						$name
					],
					file_get_contents(
						self::$_root_path . 
						'Private/Vendor/polyfony-inc/console/templates/Views/Index/Input.php'
					)
				);

				// VALUES
				$index_columns_values[] = str_replace(
					[
						'__column__',
						'__Table__',
						'__table__',
						'__Singular__',
						'__singular__',
					],
					[
						$name,
						$table_name,
						$table_slug,
						$object_singular,
						strtolower($object_singular)
					],
					file_get_contents(
						self::$_root_path . 
						'Private/Vendor/polyfony-inc/console/templates/Views/Index/Column.php'
					)
				);
			}
		}

		$index_columns_legend 	= implode("\n ", $index_columns_legend);
		$index_columns_filters 	= implode("\n ", $index_columns_filters);
		$index_columns_values 	= implode("\n ", $index_columns_values);
		$edit_form_fields		= implode("\n", $edit_form_fields);

		// customize index
		$index_view_template = str_replace(
			[
				'__Table__',
				'__table__',
				'__Singular__',
				'__singular__',
				'__Legend__',
				'__Filters__',
				'__Columns__',
				'__table__'
			],
			[
				$table_name,
				$table_slug,
				$object_singular,
				strtolower($object_singular),
				$index_columns_legend,
				$index_columns_filters,
				$index_columns_values,
				strtolower($table_name)
			],
			$index_view_template
		);


		// customize edit
		$edit_view_template = str_replace(
			[
				'__Table__',
				'__Singular__',
				'__singular__',
				'__Fields__',
				'__table__'
			],
			[
				$table_name,
				$object_singular,
				strtolower($object_singular),
				$edit_form_fields,
				$table_slug
			],
			$edit_view_template
		);

		// customize delete
		$delete_view_template = str_replace(
			[
				'__Table__',
				'__table__',
				'__Singular__',
				'__singular__',
			],
			[
				$table_name,
				$table_slug,
				$object_singular,
				strtolower($object_singular),
			],
			$delete_view_template
		);

		// prevent overiding
		if(file_exists($index_view_path)) {
			// some feedback
			Console\Format::line('X the Index view already exist', 'red', null);
		}
		else {
			// save the view
			file_put_contents(
				$index_view_path, 
				$index_view_template
			);
			// some feedback
			Console\Format::line('✓ the Index view has been generated', 'green', null);
		}

		// prevent overiding
		if(file_exists($edit_view_path)) {
			// some feedback
			Console\Format::line('X the Edit view already exist', 'red', null);
		}
		else {
			// save the view
			file_put_contents(
				$edit_view_path, 
				$edit_view_template
			);
			// some feedback
			Console\Format::line('✓ the Edit view has been generated', 'green', null);
		}

		// prevent overiding
		if(file_exists($delete_view_path)) {
			// some feedback
			Console\Format::line('X the Delete view already exist', 'red', null);
		}
		else {
			// save the view
			file_put_contents(
				$delete_view_path, 
				$delete_view_template
			);
			// some feedback
			Console\Format::line('✓ the Delete view has been generated', 'green', null);
		}


	}

	private static function generateModel(
		string $table_name
	) {

		// get some table informations
		list(
			$table_schema,
			$table_name,
			$object_singular,
			$table_slug
		) = self::deduceTableSchema(
			$table_name
		);

		// pretty introduction
		Console\Format::block(
			'Generating the ' . $table_name . ' Model', 
			'cyan', 
			null, 
			['bold']
		);

		// get the template
		$template = file_get_contents(
			self::$_root_path . 
			'Private/Vendor/polyfony-inc/console/templates/Model.php'
		);
		
		// set the destination file
		$destination_path = self::$_root_path . 
			'Private/Models/'.$table_name.'.php';

		// customize it
		$template = str_replace(
			[
				'__Table__',
				'__table__',
				'__Singular__',
				'__singular__',
				'__datetime__'
			],
			[
				$table_name,
				str_replace('_', '-', Inflector::tableize($table_name)),
				$object_singular,
				strtolower($object_singular),
				date('d/m/Y h:i')
			],
			$template
		);

		if(file_exists($destination_path)) {
			// some feedback
			Console\Format::line('X the '.$table_name.' Model already exist', 'red', null);
		}
		else {
			
			// save the file
			file_put_contents(
				$destination_path, 
				$template
			);

			// some feedback
			Console\Format::line('✓ the '.$table_name.' Model has been generated', 'green', null);

		}

	}

	private static function generateSymlinksCommand() {
		// pretty introduction
		Console\Format::block(
			'Generating assets symlinks', 
			'cyan', 
			null, 
			['bold']
		);
		// define the bundles dir
		$bundles_dir = 'Private/Bundles/';
		// for each bundle
		foreach(scandir($bundles_dir) as $bundle_name) {
			// bundle assets folder
			$bundles_assets_dir = $bundles_dir . $bundle_name .'/Assets/';
			// if the is no assets folder in that bundle
			if(
				!is_dir($bundles_assets_dir) || 
				in_array($bundle_name,['.','..'])
			) {
				continue;
			} 
			Console\Format::line(
				'Bundles/'.$bundle_name, 
				'white', 
				null, 
				['bold']
			);
			// get assets for that bundle
			foreach(scandir($bundles_assets_dir) as $assets_type) {
				// the directory for theses assets in this bundle
				$bundle_assets_type_dir = $bundles_assets_dir . $assets_type . '/';
				// skip non valid directories
				if(
					!is_dir($bundle_assets_type_dir) || 
					in_array($assets_type,['.','..'])
				) {
					continue;
				}
				// set the root path
				$assets_root_path = "Public/Assets"."/{$assets_type}/";
				// if it doesn't already exist 
				if(!is_dir($assets_root_path)) {
					// create the path
					if(!@mkdir($assets_root_path, 0777, true)) {
						// feedback
						Console\Format::line(
							' └── X Public/Assets/'.$assets_type.'/', 
							'red', 
							null
						);
					}
				}
				// remove previous symlink 
				$assets_symbolic_path = $assets_root_path . $bundle_name;
				// if a symlink already exists
				if(is_link($assets_symbolic_path)){
					// remove it
					if(!@unlink($assets_symbolic_path)){
						// feedback
						Console\Format::line(
							'  └── X '.$assets_symbolic_path.'/', 
							'red', 
							null
						);
					}
				}
				// if the symlink does not already exists
				if(!is_link($assets_symbolic_path)) {
					// try creating the syminls
					if(@symlink(
						'../../../' . $bundle_assets_type_dir . '/', 
						$assets_symbolic_path
					)) {
						// feedback
						Console\Format::line(
							'  └── ✓ Public/Assets/'.$assets_type.'/'.$bundle_name.'/', 
							'green', 
							null
						);
					}
					// the symlink creation failed
					else {
						// feedback
						Console\Format::line(
							'  └── X Public/Assets/'.$assets_type.'/'.$bundle_name.'/', 
							'red', 
							null
						);
					}
				}
			}
		}
	}

	private static function syncCommand(
		string $direction, 
		string $clean_cache
	) {

		// read the configuration file
		$full_ini = parse_ini_file(self::$_root_path.'Private/Config/Config.ini', true);

		// if our section doesn't exist
		if(!array_key_exists('sync', $full_ini)) {
			// we can't proceed
			return Console\Format::block('Missing [sync] section in Config.ini','red');
		}

		// if any or the required configuration is missing
		if(
			!isset($full_ini['sync']['remote_host']) || 
			!isset($full_ini['sync']['remote_port']) || 
			!isset($full_ini['sync']['remote_user']) || 
			!isset($full_ini['sync']['remote_path']) || 
			!isset($full_ini['sync']['local_path'])) {
			// we can't proceed
			return Console\Format::block(
				'Missing key=value in the [sync] section of Config.ini',
				'red'
			);
		}

		// set the local root
		$full_ini['sync']['local_path'] 	= '/' . trim($full_ini['sync']['local_path'], '/') . '/';
		// set the remote root
		$full_ini['sync']['remote_path'] 	= '/' . trim($full_ini['sync']['remote_path'], '/') . '/';
		// set the options and port
		self::$_commands['sync']['configs']['rsync_options']	= str_replace(
			'_port_', 
			$full_ini['sync']['remote_port'], 
			self::$_commands['sync']['configs']['rsync_options']
		);

		// folder to sync without confirmation
		if(
			isset($full_ini['sync']['always_sync_folders']) && 
			is_array($full_ini['sync']['always_sync_folders'])
		) {
			// for each of these folders
			foreach($full_ini['sync']['always_sync_folders'] as $folder) {
				// set the folder path
				$folder_path = trim($folder, '/') . '/';
				// spool the folder
				self::$_commands['sync']['configs']['folders'][$folder_path] = [
					'local'		=> $full_ini['sync']['local_path'] . 	$folder_path,
					'remote'	=> $full_ini['sync']['remote_path'] . 	$folder_path,
					'relative'	=> $folder_path,
					'confirm'	=> false
				];
			}
		}
		// folder to sync with confirmation
		if(
			isset($full_ini['sync']['ask_sync_folders']) && 
			is_array($full_ini['sync']['ask_sync_folders'])
		) {
			// for each of these folders
			foreach($full_ini['sync']['ask_sync_folders'] as $folder) {
				// set the folder path
				$folder_path = trim($folder, '/') . '/';
				// spool the folder
				self::$_commands['sync']['configs']['folders'][$folder_path] = [
					'local'		=> $full_ini['sync']['local_path'] . 	$folder_path,
					'remote'	=> $full_ini['sync']['remote_path'] . 	$folder_path,
					'relative'	=> $folder_path,
					'confirm'	=> true
				];
			}
		}

		// local end to production
		if($direction == 'up') {
			// pretty introduction
			Console\Format::line(
				'  Direction is UP', 
				'red', 
				null, 
				['bold']
			);
			Console\Format::line(
				'  Your project will be uploaded to production', 
				'red', 
				null, 
				['italic']
			);
			Console\Format::line(
				'  ___________________________________________', 
				'red', 
				null, 
				['italic']
			);
			Console\Format::line('');
			Console\Format::line(
				'  127.0.0.1:'.$full_ini['sync']['local_path'].' -- >>> -- '.$full_ini['sync']['remote_user'].'@'.$full_ini['sync']['remote_host'].':'.$full_ini['sync']['remote_path'] , 
				'white'
			);
			Console\Format::line('');
			Console\Format::line(
				'  Are you sure ?', 
				'white', 
				null, 
				['bold']
			);
			//Console\Format::line('  Type strictly "UP" to confirm', 'white', null, ['italic']);
			$confirm = readline('  Type "up" to confirm ');
			// if the confirmation is not strictely correct
			if($confirm != 'up') {
				// some feedback
				Console\Format::line('');
				// and stop
				return Console\Format::line(
					'  Cancelled', 
					'red', 
					null
				);

			}
			// for each folder to sync
			foreach(self::$_commands['sync']['configs']['folders'] as $folder => $more) {

				// feedback
				Console\Format::raw(
					'  > '.str_pad($folder,28,' '), 
					'green', 
					null
				);
				// the rsync command for that folder
				$rsync_command = 'rsync '.self::$_commands['sync']['configs']['rsync_options'].$more['local'].'* '.$full_ini['sync']['remote_user'].'@'.$full_ini['sync']['remote_host'] . ':' . $more['remote'];
				// the mkdir command for that folder
				$mkdir_command = 'ssh -p '.$full_ini['sync']['remote_port'].' '.$full_ini['sync']['remote_user'].'@'.$full_ini['sync']['remote_host'].' "mkdir -m 0777 -p '.$more['remote'].'"';

				// if the folder doesn't even exist locally
				if(!is_dir($more['local'])) {
					// skip to the next
					continue;
				}
				// if the folder is subject to a manual confirmation
				if($more['confirm']) {
					// ask for it 
					Console\Format::raw(
						' Really ? [y/(n)] ', 
						'white', 
						null
					);
					// read the answer
					$confirm_folder = readline('');
					// if the confirmation is valid
					if($confirm_folder == 'y') {
						// create the local folder if needed
						shell_exec($mkdir_command);
						// wait to make sure it's done
						usleep(125);
						// rsync down
						shell_exec($rsync_command);
					}
					else {
						// feedback to know that we ignored/skipped it
						Console\Format::raw(
							"    └── ignored \n", 
							'yellow', 
							null
						);
					}
				}
				else {

					// create the local folder if needed
					shell_exec($mkdir_command);
					// wait to make sure it's done
					usleep(125);
					// execute
					shell_exec($rsync_command);
					// clean line
					Console\Format::raw("\n");
				}

			}

			// if we want to clean the cache
			if($clean_cache == 'yes') {
				// now clean the cache
				self::cleanRemoteCacheCommand();
			}
			
			// skip a line
			Console\Format::line('');

		}
		// the direction is from production to dev
		else {
			// pretty introduction
			Console\Format::line(
				'  Direction is DOWN', 
				'red', 
				null, 
				['bold']
			);
			Console\Format::line(
				'  Your project will be downloaded locally', 
				'red', 
				null, 
				['italic']
			);
			Console\Format::line(
				'  _______________________________________', 
				'red', 
				null, 
				['italic']
			);
			Console\Format::line('');
			Console\Format::line(
				'  127.0.0.1:'.$full_ini['sync']['local_path'].' -- <<< -- '.$full_ini['sync']['remote_user'].'@'.$full_ini['sync']['remote_host'].':'.$full_ini['sync']['remote_path'] , 
				'white'
			);
			Console\Format::line('');
			Console\Format::line(
				'  Are you sure ?', 
				'white', 
				null, 
				['bold']
			);
			// read the confirmation
			$confirm = readline('  Type "down" to confirm ');
			// if the confirmation is not correct
			if($confirm != 'down') {
				// clean line
				Console\Format::line('');
				// feedback to the user
				return Console\Format::line(
					'  Cancelled.', 
					'red', 
					null
				);

			}
			// for each folder to sync
			foreach(self::$_commands['sync']['configs']['folders'] as $folder => $more) {
				// feedback
				Console\Format::raw('  < '.str_pad($folder,28,' '), 'green', null);
				// the command to create a local folder
				$mkdir_command = 'mkdir -m 0777 -p '.$more['local'];
				// the command to actually sync
				$rsync_command = 'rsync '.self::$_commands['sync']['configs']['rsync_options'].
					$full_ini['sync']['remote_user'].'@'.$full_ini['sync']['remote_host'] . ':' . $more['remote'].'* '.$more['local'];
				// if the folder requires manual confirmation
				if($more['confirm']) {
					// ask for it
					Console\Format::raw(
						' Really ? [y/(n)] ', 
						'white', 
						null
					);
					// read the anwser
					$confirm_folder = readline('');
					// if the anwser is valid
					if($confirm_folder == 'y') {
						// create the folder
						shell_exec($mkdir_command);
						// wait to make sure it's done
						usleep(125);
						// sync the folder
						shell_exec($rsync_command);
					}
					else {
						Console\Format::raw(
							"    └── ignored \n", 
							'yellow', 
							null
						);
					}
				}
				else {
					// create folder
					shell_exec($mkdir_command);
					// wait to make sure it's done
					usleep(125);
					// sync folder
					shell_exec($rsync_command);
					// clean line
					Console\Format::raw("\n");
				}
			}
			// skip a line
			Console\Format::line('');
		}

	}

	private static function cleanRemoteCacheCommand() {

		// read the configuration file
		$full_ini = parse_ini_file(self::$_root_path.'Private/Config/Config.ini', true);

		// if our section doesn't exist
		if(!array_key_exists('sync', $full_ini)) {
			// we can't proceed
			return Console\Format::block('Missing [sync] section in Config.ini','red');
		}

		// if any or the required configuration is missing
		if(
			!isset($full_ini['sync']['remote_host']) || 
			!isset($full_ini['sync']['remote_port']) || 
			!isset($full_ini['sync']['remote_user']) || 
			!isset($full_ini['sync']['remote_path']) || 
			!isset($full_ini['sync']['local_path'])) {
			// we can't proceed
			return Console\Format::block(
				'Missing key=value in the [sync] section of Config.ini',
				'red'
			);
		}

		// the remote cleaning command for that folder
		$cleaning_up_command = 
			'ssh -p '.$full_ini['sync']['remote_port'].' '.$full_ini['sync']['remote_user'].'@'.
			$full_ini['sync']['remote_host'].' "cd '.$full_ini['sync']['remote_path'].
			' && Private/Binaries/Console clean-cache"';
		
		// execute it and directly show the feedback
		echo shell_exec($cleaning_up_command);

	}

	private static function cleanCacheCommand() {

		// pretty introduction
		Console\Format::block('Cleaning up caches', 'cyan', null, ['bold']);
		// for each bundle
		foreach(self::$_cache_folders as $cache_folder) {
			// absolute cache folder path
			$absolute_cache_folder_path = self::$_root_path . $cache_folder;
			// some feedback
			Console\Format::line(str_replace('Private/Storage','',$cache_folder), 'white', null);
			// if the cache folder exists
			if(!file_exists($absolute_cache_folder_path)) { continue; }
			// scan for cache files
			foreach(scandir($absolute_cache_folder_path) as $cache_file) {
				// absolute cache file path
				$absolute_cache_file_path = $absolute_cache_folder_path . $cache_file;
				// folder and non-deletable item are skipped
				if(
					is_dir($absolute_cache_file_path) || 
					in_array($cache_file,['.','..','.gitignore'])
				) { continue; }
				// is a file that is deletable in term of write access
				is_writable($absolute_cache_file_path) && 
				// deleting went well
				@unlink($absolute_cache_file_path) ?
					// feedback 
					Console\Format::line('  └── ✓ '.$cache_file, 'green', null) : 
					Console\Format::line('  └── X '.$cache_file, 'red', null);

			}

		}

	}

	private static function backupDatabaseCommand(
		string $environment
	) {

		// read the configuration file
		$full_ini = array_replace(
			parse_ini_file(self::$_root_path.'Private/Config/Config.ini', true),
			parse_ini_file(self::$_root_path.'Private/Config/'.ucfirst($environment).'.ini', true)
		);

		// pretty introduction
		Console\Format::block('Backing up the database (SQLite only)', 'cyan', null, ['bold']);

		// database path
		$database_path = realpath(self::$_root_path . 'Public/'.$full_ini['database']['database']);

		// get the current size
		$database_original_size = Format::size(filesize($database_path));

		// backup folder creation command
		Console\Format::line('  ✓ Creating backup folder', 'green', null, []);

		// Creating backups folders
		shell_exec(self::$_commands['backup-database']['configs']['create_archive_folder_command']);

		// backup destination
		$backup_destination = 'Private/Storage/Data/Backups/Database/Polyfony-'.date('Y-m-d-H-i').'.sqlite.tar.xz';

		// customize the archive command
		$archive_command = str_replace(
			[
				'__destination__',
				'__relative_database_path__'
			], 
			[
				$backup_destination,
				realpath('Public/'.$full_ini['database']['database'])
			], 
			self::$_commands['backup-database']['configs']['archive_command']
		);

		// backup folder creation command
		Console\Format::line('  ✓ Compressing the database (This can take a while...)', 'green', null, []);

		// actually archive
 		shell_exec($archive_command);

 		// final feedback
		Console\Format::block('This backup is ' . Format::size(filesize($backup_destination)), 'black', 'yellow', []);

		// cleanup the terminal
		Console\Format::line(' ','white','black',[]);

	}

	private static function checkConfigCommand() {

		// pretty introduction
		Console\Format::block('Sorry, not yet implemented', 'purple', null, []);

	}

	private static function vacuumDatabaseCommand() {

		// pretty introduction
		Console\Format::block('Sorry, not yet implemented', 'purple', null, []);

	}

	private static function defineProjectRootPath() {

		// define the project root path so that we can work more easily
		self::$_root_path = realpath(__DIR__.'/../../../../../../').'/';

	}

}

?>
