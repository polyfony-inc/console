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
				Console\Format::line('Not enough arguments provided' , 'red', null, []);

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

					case 'clean-remote-cache':

						self::cleanRemoteCacheCommand();

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

	private static function defineProjectRootPath() {

		// define the project root path so that we can work more easily
		self::$_root_path = realpath(__DIR__.'/../../../../../../').'/';

	}

}

?>
