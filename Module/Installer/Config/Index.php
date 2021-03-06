<?php

namespace ezRPG\Module\Installer\Config;
use ezRPG\Library\Module;

/**
 * Config Index
 * @see Library\Module
 */
class Index extends Module
{
	/**
	 * Default Action
	 */
	public function index()	{
		$data['guessUrl'] = 'http://'.$_SERVER['HTTP_HOST'].dirname(str_ireplace('installer/','', $_SERVER['REQUEST_URI']));
		if ( isset($_POST['submit']) ) {
			$dbconfig = array(
				'db' => array(
					'driver'   => $_POST['dbtype'],
					'host'	 => $_POST['dbhost'],
					'database' => $_POST['dbname'],
					'username' => $_POST['dbuser'],
					'password' => $_POST['dbpass'],
					'port' => '',
					'prefix' => $_POST['dbpref']
				),
			);
			$newConfig = array_merge($this->container->offsetGet('config'), $dbconfig);
			$this->container->offsetSet('config', $newConfig);
			try {
				$installer = $this->app->getModel('installer');
				foreach ( glob("./Module/Installer/Config/Sql/*.sql") as $query ) {
					$installer->runSqlFile($query, $_POST['dbpref']);
				}
			} catch(\Exception $e) {
				$this->container['view']->setMessage("Please check your database configurations.", 'fail');
				$error = 1;
			}
			if ( !isset($error) ) {
				$dbtype = $_POST['dbtype'];
				$dbhost = $_POST['dbhost'];
				$dbname = $_POST['dbname'];
				$dbuser = $_POST['dbuser'];
				$dbpass = $_POST['dbpass'];
				$dbpref = $_POST['dbpref'];
				$gamename = $_POST['gamename'];
				$gameurl = $_POST['gameurl'];
				/* Generate configuration file */
				$config = <<<CONFIG
<?php

/**
 * Database Configuration
 */ 

\$config['db'] = array(
	'driver'   => '$dbtype',
	'host'	 => '$dbhost',
	'database' => '$dbname',
	'username' => '$dbuser',
	'password' => '$dbpass',
	'port' => '',
	'prefix' => '$dbpref'
);
CONFIG;
				$fh = fopen('config.php', 'w+');
				fwrite($fh, $config);
				fclose($fh);
				/* Generate Settings */
				$settings = $this->app->getModel('setting');
				$settings->update(2, $gamename);
				$settings->update(3, $gameurl);
				
				$settings->buildCache();

				$routes = $this->app->getModel('route');
				try {
					$routes->buildCache();
				} catch(\Exception $e) {
					printf('<div><strong>ezRPG Exception</strong></div>%s<pre>', $e->getMessage());
					var_dump($e);
					die();
				}
				header("location: {$gameurl}/installer/admin");
			}
			
		}

		$this->container['view']->set('data', $data);
		$this->view->name = "config";
	}
}
