* Generate publics symlinks to access assets stored in the bundles

![Demo video](https://github.com/polyfony-inc/console/blob/master/doc/generate-symlinks.gif)

* Synchronize your Polyfony project to/form a remote production server

![Demo video](https://github.com/polyfony-inc/console/blob/master/doc/sync.gif)

```
projet-directory$ Private/Binaries/Console 
   _           _           
  |_) _  |   _|_ _  ._     
  |  (_) | \/ | (_) | | \/ 
           /            /  

Usage
  command [arguments]

Available commands
  help                  Display this help message
  sync                  Synchronizes your project from or to a remote server via SSH and cleans its remote cache
  backup-database       Creates a compressed copy of the local database (you must specify the current environment)
  clean-cache           Empties the Private/Storage/Cache folder and it subdirectories
  clean-remote-cache    Empties the Private/Storage/Cache folder and it subdirectories on the remote server
  generate-symlinks     Generates symlinks from Private/Bundles/{$1}/Assets/{$2} to Public/Assets/{$2}/{$1}
  generate-model        Generates a model file for a given table name
  generate-controller   Generates a controller for a given table name
  generate-views        Generates all views for a given table name


```
