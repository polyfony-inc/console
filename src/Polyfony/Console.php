<?php

namespace Polyfony;

// the class itself
class Console {

	// list of available command, syntax, configs, and usage
	protected static $_commands 		= [
		'help'			=>[
			'usage'			=>'Console help',
			'description'	=>'Shows this help'
		],
		'sync'					=>[
			// usage for that command
			'usage'			=>'Console sync [up/down]',
			// description for that command
			'description'	=>'Synchronizes your project from or to a remote server via SSH',
			// mapping of cli arguments to their name
			'arguments'		=>[
				0	=>'Direction'
			],
			// configs element for that command
			'configs'=>[
				'rsync_options'	=>'-avzlp --delete --chmod=ugo+rwX --exclude=".DS_Store" -e "ssh -p _port_" ',
				'folders'		=>[],
				'local_path'	=>null,
				'remote_path'	=>null,
				'remote_user'	=>null,
				'remote_host'	=>null
			]
		],
		'check-config'			=>[
			'usage'			=>'Console check-config',
			'description'	=>'Checks if the configuration of the framework is optimal'
		],
		'vacuum-database'		=>[
			'usage'			=>'Console vacuum-database',
			'description'	=>'Executed a vacuum command on the database to free up space'
		],
		'clean-cache'			=>[
			'usage'			=>'Console clean-cache',
			'description'	=>'Empties the Private/Storage/Cache folder and it subdirectories'
		],
		'generate-symlinks'		=>[
			'usage'			=>'Console generate-syminks',
			'description'	=>'Generates symlinks from Private/Bundles/{$1}/Assets/{$2} to Public/Assets/{$2}/{$1}'
		],
		'generate-bundle'		=>[
			'usage'			=>'Console generate-bundle [bundle-name]',
			'description'	=>'Generate a bundle with full CRUD capabilities based on current database tables',
			'arguments'		=>[
				0	=>'Bundle'
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
				0	=>'Table'
			]
		],
		'generate-controllers'	=>[
			'usage'			=>'Console generate-controllers [bundle-name]',
			'description'	=>'Generates all controllers in a bundle based on current database tables',
			'arguments'		=>[
				0	=>'Bundle'
			]
		],
		'generate-controller'	=>[
			'usage'			=>'Console generate-controller [table-name] [bundle-name]',
			'description'	=>'Generates a controller for a given table name',
			'arguments'		=>[
				0	=>'Table',
				1	=>'Bundle'
			]
		],
		'generate-views'		=>[
			'usage'			=>'Console generate-views [table-name] [bundle-name]',
			'description'	=>'Generates all views for a given table name',
			'arguments'		=>[
				0	=>'Table',
				1	=>'Bundle'
			]
		],
		'generate-view'			=>[
			'usage'			=>'Console generate-view [index/edit/delete/create] [table-name] [bundle-name]',
			'description'	=>'Generates a view for a given table name and action',
			'arguments'		=>[
				0	=>'Action',
				1	=>'Table',
				2	=>'Bundle'
			]
		]

	];

	// color a string
	private static function color($string, $color, $negative = false) {
		return($string);
	}

	// add a folder to sync
	public static function folder($folder, $confirm = false) {
		// set the folder name
		$folder_path = trim($folder, '/') . '/';
		// spool the folder
		self::$_folders[] = array(
			'local'		=> self::$_local_root . 	$folder_path,
			'remote'	=> self::$_remote_root . 	$folder_path,
			'relative'	=> $folder_path,
			'confirm'	=> (boolean) $confirm
		);
	}

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
			if($required_arguments_count && $required_arguments_count < (count($_SERVER['argv']) + 1)) {

				// show the usage for that specific command
				Console\Format::line('Not enough arguments provided' , 'red', null, []);

				// show that command's info
				Console\Format::raw('  '.self::$_commands[$command]['usage'] . ' : ', 'green');
				Console\Format::raw(self::$_commands[$command]['description'], 'white', null, ['italic']);
				echo "\n";


			}
			// the arguments seem ok
			else {

				switch($command) {

					case 'clean-cache':

					break;

					case 'sync':

					break;

					case 'check-config':

					break;

					case 'vacuum-database':

					break;

					case 'generate-symlinks':

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

	private static function help() {
		
		Console\Format::line(
			"    _           _           \n".
			"   |_) _  |   _|_ _  ._     \n".
			"   |  (_) | \/ | (_) | | \/ \n".
			"            /            /  \n",
			'cyan',
			null
		);

		// main usage
		Console\Format::line('Available commands' , 'white', null, ['bold']);

		// build the list of available commands
		foreach(self::$_commands as $command => $infos) {

			// show that command's info
			Console\Format::raw('  '. str_pad($command,28,' '), 'green');
			Console\Format::raw($infos['description'], 'white', null, ['italic']);
			echo "\n";
		}

		// skip a line
		Console\Format::line('');

	}
/*
	private static function syncCommand() {


		// read the configuration file
		$full_ini = parse_ini_file(__DIR__.'/../../../../Config/Config.ini', true);

		// if our section doesn't exist
		if(!array_key_exists('sync', $full_ini)) {
			// we can't proceed
			Throw new Exception('Missing [sync] section in Config.ini');
		}

		// if any or the required configuration is missing
		if(
			!isset($full_ini['sync']['remote_host']) || 
			!isset($full_ini['sync']['remote_port']) || 
			!isset($full_ini['sync']['remote_user']) || 
			!isset($full_ini['sync']['remote_path']) || 
			!isset($full_ini['sync']['local_path'])) {
			// we can't proceed
			Throw new Exception('Missing configuration in the [sync] section of Config.ini');
		}

		// set the port
		self::$_remote_port 	= $full_ini['sync']['remote_port'];
		// set the user
		self::$_remote_user 	= $full_ini['sync']['remote_user'];
		// set the server
		self::$_remote_server 	= $full_ini['sync']['remote_host'];
		// set the local root
		self::$_local_root 		= '/' . trim($full_ini['sync']['local_path'], '/') . '/';
		// set the remote root
		self::$_remote_root 	= '/' . trim($full_ini['sync']['remote_path'], '/') . '/';
		// set the options and port
		self::$_rsync_options 	= str_replace('_port_', $full_ini['sync']['remote_port'], self::$_rsync_options);

		// folder to sync without confirmation
		if(isset($full_ini['sync']['always_sync_folders']) && is_array($full_ini['sync']['always_sync_folders'])) {
			// for each of these folders
			foreach($full_ini['sync']['always_sync_folders'] as $folder) {
				// add them to the pool
				self::folder($folder, false);
			}

		}
		// folder to sync with confirmation
		if(isset($full_ini['sync']['ask_sync_folders']) && is_array($full_ini['sync']['ask_sync_folders'])) {
			// for each of these folders
			foreach($full_ini['sync']['ask_sync_folders'] as $folder) {
				// add them to the pool
				self::folder($folder, true);
			}

		}

		// assign the source and destination depending on the direction
		$source 		= $_SERVER['argv'][1] == 'up' ? 
			self::$_local_root : 
			self::$_remote_user . '@' . self::$_remote_server . ':' . self::$_remote_root;
		$destination 	= $_SERVER['argv'][1] == 'up' ? 
			self::$_remote_user . '@' . self::$_remote_server . ':' . self::$_remote_root : 
			self::$_local_root;
		
		// skip a line
		echo "\n";

		// confirm the direction and the source/destination
		echo "  Direction is " . self::COLOR_RED . strtoupper($_SERVER['argv'][1]) .self::COLOR_NORMAL. " !\n";
		echo "  From --> {$source} \n";
		echo "  To ----> {$destination} \n";
		echo "  Port ----> ".self::$_remote_port." \n\n";

		// ask for a confirmation
		self::block('Are you okay with this ?', self::COLOR_RED_NEGATIVE);
		// the confirm itself
		$confirm = readline("  Type strictly 'YES' to confirm : ");

		echo "\n";

		// if we did not confirm
		if($confirm != 'YES') {
			// show a feedback
			self::block('Cancelled, nothing has been transfered', self::COLOR_RED_NEGATIVE);
			// stop here
			exit;
		}

		// for each folder to sync
		foreach(self::$_folders as $folder) {

			// if the folder required a confirmation
			if($folder['confirm']) {
				// ask for a confirmation
				self::block("Do you want to sync {$folder['relative']} ?", self::COLOR_RED_NEGATIVE);
				// the confirm itself
				$confirm = readline("  Type strictly 'YES' to confirm : ");
				// if the confirmation is refused
				if($confirm != 'YES') {
					// say that we skipped
					echo "  Skipped folder       | {$folder['relative']}\n\n";
					// skip
					continue;
				}
			}

			// sync command
			$rsync_command = 'rsync ' . self::$_rsync_options . ' ' .
				$source . $folder['relative'] . '* ' . $destination . $folder['relative'];

			// pre create folders command
			$mkdir_command = $_SERVER['argv'][1] == 'up' ? 
				'ssh ' . self::$_remote_user . '@' . self::$_remote_server . ' mkdir -p ' . $folder['remote'] : 
				'mkdir ' . $folder['local'];

			// output a message
			echo "  Synchronizing        | {$folder['relative']} \n";
		
			// if a ssh pre rsync command exists, execute it
			shell_exec($mkdir_command);

			// actually execute the command
			shell_exec($rsync_command);



		}

		// end of script
		self::block('Sync is complete !', self::COLOR_GREEN_NEGATIVE);

	}
*/

}

/*

// has error
		$has_error = false;
		// for each bundle
		foreach(Pf\Bundles::getAvailable() as $bundle_name) {
			// get assets for that bundle
			foreach(Pf\Bundles::getAssets($bundle_name) as $assets_type => $assets_path) {
				// get the correct relativeness
				$assets_path = "../../{$assets_path}";
				// set the root path
				$assets_root_path = "./Assets/{$assets_type}/";
				// create the public root path
				Pf\Filesystem::mkdir($assets_root_path, 0777, true) ?: $has_error = true;
				// set the symlink 
				$assets_symbolic_path = $assets_root_path . $bundle_name;
				// if the symlink does not already exists
				if(!Pf\Filesystem::isSymbolic($assets_symbolic_path, true)) {
					// create the symlink
					Pf\Filesystem::symlink($assets_path, $assets_symbolic_path, true) ?: $has_error = true;
				}
			}
		}

*/

?>