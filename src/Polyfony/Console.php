<?php

namespace Polyfony;

// the class itself
class Console {

	// color for CLI prettiness
	const COLOR_NORMAL 			= "\033[0m";
	const COLOR_RED 			= "\033[0;31m";
	const COLOR_RED_NEGATIVE 	= "\033[41m";
	const COLOR_GREEN 			= "\033[0;32m";
	const COLOR_GREEN_NEGATIVE 	= "\033[0;30m\033[42m";

	// list of available command, syntax, configs, and usage
	protected static $_commands 		= [
		'help'			=>[
			'usage'			=>'Console help',
			'description'	=>'Shows the available commands'
		],
		'sync'					=>[
			// usage for that command
			'usage'			=>'Console sync [up/down]',
			// description for that command
			'description'	=>'Synchronizes your project from or to a remote server thru SSH',
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
			'description'	=>'Generate a bundle with full CRUD capabilities based on all the tables present in the database',
			'arguments'		=>[
				0	=>'Bundle'
			]
		],
		'generate-models'		=>[
			'usage'			=>'Console generate-models',
			'description'	=>'Generates all the Private/Models/{Table} based on all the tables present in the database'
		],
		'generate-model'		=>[
			'usage'			=>'Console generate-model [table-name]',
			'arguments'		=>[
				0	=>'Table'
			]
		],
		'generate-controllers'	=>[
			'usage'			=>'Console generate-controllers [bundle-name]',
			'arguments'		=>[
				0	=>'Bundle'
			]
		],
		'generate-controller'	=>[
			'usage'			=>'Console generate-controller [table-name] [bundle-name]',
			'arguments'		=>[
				0	=>'Table',
				1	=>'Bundle'
			]
		],
		'generate-views'		=>[
			'usage'			=>'Console generate-views [table-name] [bundle-name]',
			'arguments'		=>[
				0	=>'Table',
				1	=>'Bundle'
			]
		],
		'generate-view'			=>[
			'usage'			=>'Console generate-view [index/edit/delete/create] [table-name] [bundle-name]',
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

	private static function block($string, $color) {
		// get the length of the message to show
		$empty_line =  str_repeat(' ', strlen($string) + 4) . "\n";
		// first line of the block
		echo "\n" . $color . $empty_line;
		// actual message of the block
		echo "  {$string}  \n";
		// ending line of the block
		echo $empty_line . self::COLOR_NORMAL . "\n";
	}

	public static function run() {

		// if no command is provided incorrect
		if(!isset($_SERVER['argv'][1]) || !in_array($_SERVER['argv'][1], array('up', 'down'))) {
			// show an error
			self::block('Usage Private/Binaries/Sync [up, down]', self::COLOR_RED_NEGATIVE);
			// stop execution
			return;
		}
	
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


}
*/
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
