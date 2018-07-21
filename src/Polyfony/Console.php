<?php

/*
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 * Don't look at the source code of this class
 *  It is utterly disgusting, vomit may occur
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 *
 */

namespace Polyfony;

// the class itself
class Console {

	// the root path of the project
	protected static $_root_path = '';

	// list of available command, syntax, configs, and usage
	protected static $_commands 		= [
		'help'			=>[
			'usage'			=>'Console help',
			'description'	=>'Display this help message'
		],
		'sync'					=>[
			// usage for that command
			'usage'			=>'Console sync [up/down]',
			// description for that command
			'description'	=>'Synchronizes your project from or to a remote server via SSH',
			// mapping of cli arguments to their name
			'arguments'		=>[
				'Direction'
			],
			// configs element for that command
			'configs'=>[
				'rsync_options'	=>'-avzlp --delete --chmod=ugo+rwX --exclude=".DS_Store" -e "ssh -p _port_" ',
				'folders'		=>[]
			]
		],
		'check-config'			=>[
			'usage'			=>'Console check-config',
			'description'	=>'Checks if the configuration of the framework is optimal'
		],
		'vacuum-database'		=>[
			'usage'			=>'Console vacuum-database',
			'description'	=>'Executes a vacuum command on the database to free up space'
		],
		'clean-cache'			=>[
			'usage'			=>'Console clean-cache',
			'description'	=>'Empties the Private/Storage/Cache folder and it subdirectories'
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
		'generate-models'		=>[
			'usage'			=>'Console generate-models',
			'description'	=>'Generates all the Private/Models/{Table} based on current database tables'
		],
		'generate-model'		=>[
			'usage'			=>'Console generate-model [table-name]',
			'description'	=>'Generates a model file for a given table name',
			'arguments'		=>[
				'Table'
			]
		],
		'generate-controllers'	=>[
			'usage'			=>'Console generate-controllers [bundle-name]',
			'description'	=>'Generates all controllers in a bundle based on current database tables',
			'arguments'		=>[
				'Bundle'
			]
		],
		'generate-controller'	=>[
			'usage'			=>'Console generate-controller [table-name] [bundle-name]',
			'description'	=>'Generates a controller for a given table name',
			'arguments'		=>[
				'Table',
				'Bundle'
			]
		],
		'generate-views'		=>[
			'usage'			=>'Console generate-views [table-name] [bundle-name]',
			'description'	=>'Generates all views for a given table name',
			'arguments'		=>[
				'Table',
				'Bundle'
			]
		],
		'generate-view'			=>[
			'usage'			=>'Console generate-view [index/edit/delete/create] [table-name] [bundle-name]',
			'description'	=>'Generates a view for a given table name and action',
			'arguments'		=>[
				'Action',
				'Table',
				'Bundle'
			]
		]

	];

	public static function run() {

		// if a command is provided and is incorrect, or if no command is provided
		if(
			!isset($_SERVER['argv'][1]) || 
			(isset($_SERVER['argv'][1]) && !array_key_exists($_SERVER['argv'][1], self::$_commands))
		) {
			
			// show the help
			self::help();

			// stop execution
			return;

		}
		// a command is provided and it exists
		elseif(isset($_SERVER['argv'][1]) && array_key_exists($_SERVER['argv'][1], self::$_commands)) {

			// get the command
			$command = $_SERVER['argv'][1];

			// get the total number of arguments required by that command
			$required_arguments_count = isset(self::$_commands[$command]['arguments']) ? count(self::$_commands[$command]['arguments']) : 0;

			// if that command requires parameters and that we don't have enough
			if($required_arguments_count && count($_SERVER['argv']) < ($required_arguments_count + 2)) {

				// show the usage for that specific command
				Console\Format::line('Not enough arguments provided' , 'red', null, []);

				// show that command's info
				Console\Format::raw('  '.self::$_commands[$command]['usage'] . ' : ', 'green');
				Console\Format::raw(self::$_commands[$command]['description'], 'white', null, ['italic']);
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

					case 'sync':

						if(in_array($_SERVER['argv'][2], ['up','down'])) {

							self::syncCommand($_SERVER['argv'][2]);

						}
						else {

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

					break;

					case 'run-test':

					break;

					case 'generate-bundle':

					break;

					case 'generate-controllers':

					break;

					case 'generate-controller':

					break;

					case 'generate-models':

					break;

					case 'generate-model':

					break;

					case 'generate-views':

					break;

					case 'generate-view':

					break;

					case 'help':
					default:

					self::help();

					break;

				}

			}

		}

	}

	private static function art() {

		Console\Format::line(
			"   _           _           \n".
			"  |_) _  |   _|_ _  ._     \n".
			"  |  (_) | \/ | (_) | | \/ \n".
			"           /            /  \n",
			'cyan',
			null
		);

	}

	private static function help() {
		
		// greatings
		self::art();

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

	private static function generateSymlinksCommand() {
		// pretty introduction
		Console\Format::block('Generating assets symlinks', 'cyan', null, ['bold']);
		// define the bundles dir
		$bundles_dir = '../Private/Bundles/';
		// for each bundle
		foreach(scandir($bundles_dir) as $bundle_name) {
			// bundle assets folder
			$bundles_assets_dir = $bundles_dir . $bundle_name .'/Assets/';
			// if the is no assets folder in that bundle
			if(!is_dir($bundles_assets_dir) || in_array($bundle_name,['.','..'])) {
				continue;
			} 
			Console\Format::line('Bundles/'.$bundle_name, 'white', null, ['bold']);
			// get assets for that bundle
			foreach(scandir($bundles_assets_dir) as $assets_type) {
				// the directory for theses assets in this bundle
				$bundle_assets_type_dir = $bundles_assets_dir . $assets_type . '/';
				// skip non valid directories
				if(!is_dir($bundle_assets_type_dir) || in_array($assets_type,['.','..'])) {
					continue;
				}
				// set the root path
				$assets_root_path = "Assets"."/{$assets_type}/";
				// if it doesn't already exist 
				if(!is_dir($assets_root_path)) {
					// create the path
					if(@mkdir($assets_root_path, 0777, true)) {
						// feedback
						Console\Format::line('  + Public/Assets/'.$assets_type.'/', 'green', null);
					}
					else {
						// feedback
						Console\Format::line('  X Public/Assets/'.$assets_type.'/ (failed)', 'red', null);
					}
				}
				// remove previous symlink 
				$assets_symbolic_path = $assets_root_path . $bundle_name;
				if(is_link($assets_symbolic_path)){
					if(@unlink($assets_symbolic_path)){
						Console\Format::line('  - '.$assets_symbolic_path.'/ deleted', 'gray', null);
					}
					else {
						// feedback
						Console\Format::line('  x '.$assets_symbolic_path.'/ NOT deleted', 'red', null);
					}
				}
				// set the symlink 
				// if the symlink does not already exists
				if(!is_link($assets_symbolic_path)) {
					if(@symlink("../../" . $bundle_assets_type_dir . "/", $assets_root_path.$bundle_name)) {
						// feedback
						Console\Format::line('  + Public/Assets/'.$assets_type.'/'.$bundle_name.'/', 'green', null);
					}
					else {
						// feedback
						Console\Format::line('  X Public/Assets/'.$assets_type.'/'.$bundle_name.'/ (failed)', 'red', null);
					}
				}
				else {
					// feedback
					Console\Format::line('  ! symlink : '.$assets_symbolic_path.' (Already exists)', 'cyan', null);
				}
			}
		}
	}

	private static function syncCommand(string $direction) {

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
			return Console\Format::block('Missing key=value in the [sync] section of Config.ini','red');
		}

		// set the local root
		$full_ini['sync']['local_path'] 	= '/' . trim($full_ini['sync']['local_path'], '/') . '/';
		// set the remote root
		$full_ini['sync']['remote_path'] 	= '/' . trim($full_ini['sync']['remote_path'], '/') . '/';
		// set the options and port
		self::$_commands['sync']['configs']['rsync_options']	= str_replace('_port_', $full_ini['sync']['remote_port'], self::$_commands['sync']['configs']['rsync_options']);

		// folder to sync without confirmation
		if(isset($full_ini['sync']['always_sync_folders']) && is_array($full_ini['sync']['always_sync_folders'])) {
			// for each of these folders
			foreach($full_ini['sync']['always_sync_folders'] as $folder) {
				// add them to the pool
				// set the folder name
				$folder_path = trim($folder, '/') . '/';
				// spool the folder
				self::$_commands['sync']['configs']['folders'][$folder_path] = array(
					'local'		=> $full_ini['sync']['local_path'] . 	$folder_path,
					'remote'	=> $full_ini['sync']['remote_path'] . 	$folder_path,
					'relative'	=> $folder_path,
					'confirm'	=> false
				);
			}

		}
		// folder to sync with confirmation
		if(isset($full_ini['sync']['ask_sync_folders']) && is_array($full_ini['sync']['ask_sync_folders'])) {
			// for each of these folders
			foreach($full_ini['sync']['ask_sync_folders'] as $folder) {
				// set the folder name
				$folder_path = trim($folder, '/') . '/';
				// spool the folder
				self::$_commands['sync']['configs']['folders'][$folder_path] = array(
					'local'		=> $full_ini['sync']['local_path'] . 	$folder_path,
					'remote'	=> $full_ini['sync']['remote_path'] . 	$folder_path,
					'relative'	=> $folder_path,
					'confirm'	=> true
				);
			}

		}

		if($direction == 'up') {
			// pretty introduction
			Console\Format::line('  Direction is UP', 'red', null, ['bold']);
			Console\Format::line('  Your project will be uploaded to production', 'red', null, ['italic']);
			Console\Format::line('  ___________________________________________', 'red', null, ['italic']);
			Console\Format::line('');
			Console\Format::line('  127.0.0.1:'.$full_ini['sync']['local_path'].' -- >>> -- '.$full_ini['sync']['remote_user'].'@'.$full_ini['sync']['remote_host'].':'.$full_ini['sync']['remote_path'] , 'white');

			Console\Format::line('');
			Console\Format::line('  Are you sure ?', 'white', null, ['bold']);
			//Console\Format::line('  Type strictly "UP" to confirm', 'white', null, ['italic']);
			$confirm = readline('  Type "up" to confirm ');
			if($confirm != 'up') {

				Console\Format::line('');
				return Console\Format::line('  Cancelled', 'red', null);

			}

			foreach(self::$_commands['sync']['configs']['folders'] as $folder => $more) {

				Console\Format::raw('  > '.str_pad($folder,28,' '), 'green', null);

				// the rsync command for that folder
				$rsync_command = 'rsync '.self::$_commands['sync']['configs']['rsync_options'].$more['local'].'* '.$full_ini['sync']['remote_user'].'@'.$full_ini['sync']['remote_host'] . ':' . $more['remote'];

				// the mkdir command for that folder
				$mkdir_command = 'ssh -p '.$full_ini['sync']['remote_port'].' '.$full_ini['sync']['remote_user'].'@'.$full_ini['sync']['remote_host'].' "mkdir -m 0777 -p '.$more['remote'].'"';

				// if the folder doesn't even exist locally
				if(!is_dir($more['local'])) {
					// skip to the next
					continue;
				}

				if($more['confirm']) {
					Console\Format::raw(' Really ? [y/(n)] ', 'white', null);
					$confirm_folder = readline('');
					if($confirm_folder == 'y') {

						// create the local folder if needed
						shell_exec($mkdir_command);
						//Console\Format::line($mkdir_command, null, 'gray');

						// wait to make sure it's done
						usleep(125);

						// rsync down
						shell_exec($rsync_command);
						//Console\Format::line($rsync_command, null, 'gray');

					}
					else {
						Console\Format::raw("    └── ignored \n", 'yellow', null);
					}
				}
				else {

					// create the local folder if needed
					shell_exec($mkdir_command);
					//Console\Format::line($mkdir_command, null, 'gray');

					// wait to make sure it's done
					usleep(125);

					// execute
					shell_exec($rsync_command);
					//Console\Format::line($rsync_command, null, 'gray');

					Console\Format::raw("\n");
				}

			}

			// skip a line
			Console\Format::line('');

		}
		else {
			// pretty introduction
			Console\Format::line('  Direction is DOWN', 'red', null, ['bold']);
			Console\Format::line('  Your project will be downloaded locally', 'red', null, ['italic']);
			Console\Format::line('  _______________________________________', 'red', null, ['italic']);
			Console\Format::line('');
			Console\Format::line('  127.0.0.1:'.$full_ini['sync']['local_path'].' -- <<< -- '.$full_ini['sync']['remote_user'].'@'.$full_ini['sync']['remote_host'].':'.$full_ini['sync']['remote_path'] , 'white');

			Console\Format::line('');
			Console\Format::line('  Are you sure ?', 'white', null, ['bold']);
			//Console\Format::line('  Type strictly "DOWN" to confirm', 'white', null, ['italic']);
			$confirm = readline('  Type "down" to confirm ');

			if($confirm != 'down') {

				Console\Format::line('');
				return Console\Format::line('  Cancelled.', 'red', null);

			}

			foreach(self::$_commands['sync']['configs']['folders'] as $folder => $more) {

				Console\Format::raw('  < '.str_pad($folder,28,' '), 'green', null);

				$mkdir_command = 'mkdir -m 0777 -p '.$more['local'];

				$rsync_command = 'rsync '.self::$_commands['sync']['configs']['rsync_options'].
					$full_ini['sync']['remote_user'].'@'.$full_ini['sync']['remote_host'] . ':' . $more['remote'].'* '.$more['local'];

				if($more['confirm']) {
					Console\Format::raw(' Really ? [y/(n)] ', 'white', null);
					$confirm_folder = readline('');
					if($confirm_folder == 'y') {

						shell_exec($mkdir_command);
					//	Console\Format::line($mkdir_command, null, 'gray');

						// wait to make sure it's done
						usleep(125);

						shell_exec($rsync_command);
					//	Console\Format::line($rsync_command, null, 'gray');

					}
					else {
						Console\Format::raw("    └── ignored \n", 'yellow', null);
					}
				}
				else {

					shell_exec($mkdir_command);
				//	Console\Format::line($mkdir_command, null, 'gray');

					// wait to make sure it's done
					usleep(125);

					shell_exec($rsync_command);
				//	Console\Format::line($rsync_command, null, 'gray');

					Console\Format::raw("\n");
				}
				

			}

			// skip a line
			Console\Format::line('');

		}

	}

	private static function defineProjectRootPath() {

		// define the project root path so that we can work more easily
		self::$_root_path = realpath(__DIR__.'/../../../../../../').'/';

	}

	protected static function rrmdir($path){
		//Console\Format::block('rrmdir : ' . $path, 'cyan', null, ['bold']);
		if (is_dir($path)) {
			array_map( "self::rrmdir", glob($path . DIRECTORY_SEPARATOR . '{,.[!.]}*', GLOB_BRACE) );

			//Console\Format::line('! @@@@@@@@[' . $path .'] should BE DELETED', 'RED', null);
			rmdir($path);
			Console\Format::line('- directory...deleted : ' . $path, 'red', null);
		}
		else {
			unlink($path);
			Console\Format::line('- file........deleted : ' . $path .'file deleted', 'red', null);
		}
	}
}

?>
