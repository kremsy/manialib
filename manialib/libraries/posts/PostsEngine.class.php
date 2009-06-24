<?php
/**
 * Posts engine example
 * @author Maxime Raoust
 * @package posts
 */

define ("POST_TYPE_NULL", 0);
define ("POST_TYPE_NEWS", 1);
 
class PostsEngine 
{
	private static $instance;
	private static $engineLoadedId = "posts_engine_loaded";
	
	private $posts = array();
	
	public $postsTable;
	public $contentTable;
	public $metaTagsTable;
	
	public static function getInstance()
	{
		if (!self :: $instance)
		{
			$class = __CLASS__;
			self :: $instance = new $class;
		}
		return self :: $instance;
	} 
	
	public function getPosts()
	{
		if(empty($this->posts))
		{
			$this->dbGetPosts();
		}
		return $this->posts;
	}
	
	public function getPost($postId)
	{
		$postId = (int) $postId;
		$this->dbGetPosts("WHERE p.post_id=$postId");
		return reset($this->posts);
	}
	
	private function __construct()
	{
		$session = SessionEngine::getInstance();
		
		// TODO Mettre les noms des tables ailleurs
		$this->postsTable = DATABASE_PREFIX . "posts";
		$this->metaTagsTable = DATABASE_PREFIX . "posts_meta_tags";
		$this->contentTable = DATABASE_PREFIX . "posts_content";
		
		if(!$session->get(self::$engineLoadedId))
		{
			if($this->dbInstall() === true)
			{
				$session->set(self::$engineLoadedId, 1);
			}
		}
	}
	
	private function dbGetPosts($filter = "ORDER BY date DESC LIMIT 0, 5")
	{
		$db = DatabaseEngine::getInstance();
		
		$db->query = 	"SELECT * FROM $this->postsTable AS p " .
						"INNER JOIN $this->contentTable AS c " .
						"ON c.post_id = p.post_id " .
						$filter;
		$db->query();
		
		while($arr = $db->fetchArray())
		{
			$post = new Post($arr["post_id"]);
			$post->setPostType($arr["post_type"]);
			$post->setAuthor($arr["author"]);
			$post->setDate($arr["date"]);
			$post->setTitle($arr["title"]);
			$post->setContent($arr["content"]);
			
			$this->posts[$arr["post_id"]] = $post;
		}
		
		$postIds = array_keys($this->posts);
		$postIds = implode("," , $postIds);
		$postIds = "($postIds)";
		
		$db->query = 	"SELECT * FROM $this->postsTable AS p " .
						"INNER JOIN $this->metaTagsTable AS t " .
						"ON t.post_id = p.post_id " .
						"WHERE p.post_id IN $postIds";
		$db->query();
		
		while($arr = $db->fetchArray())
		{
			$this->posts[$arr["post_id"]]->addMetaTag($arr["name"], $arr["value"]);
		}
		
	}
	
	private function dbInstall()
	{
		$db = DatabaseEngine::getInstance();
		
		// Check if the tables exists
		$like = DATABASE_PREFIX . "posts%";
		$like = quote_smart($like);
		
		$db->query = "SHOW TABLES LIKE $like";
		$db->query();
		
		$tables = array();
		while($row = $db->fetchRow())
		{
			$tables[] = $row[0];
		}
		
		if(	in_array($this->postsTable, $tables) &&
			in_array($this->contentTable, $tables) &&
			in_array($this->metaTagsTable, $tables)
		)
		{
			return true;
		}
		
		// If one of them is not found, we create them
		$db->query = 	"CREATE TABLE IF NOT EXISTS $this->postsTable " .
						"( " .
							"post_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY , " .
							"post_type TINYINT NOT NULL DEFAULT 0, " .
							"date TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , " .
							"author VARCHAR(25) NOT NULL , " .
							"title VARCHAR(255) NOT NULL , " .
							"INDEX ( date ), " .
							"INDEX ( post_type ), " .
							"INDEX ( author ) " .
						") " .
						"ENGINE = InnoDB " .
						"CHARACTER SET utf8 " .
						"COLLATE utf8_general_ci";
		$db->query();
		
		$db->query =	"CREATE TABLE IF NOT EXISTS $this->metaTagsTable " .
						"( " .
							"post_id INT NOT NULL ," .
							"name VARCHAR( 255 ) NOT NULL , " .
							"value VARCHAR( 255 ) NULL , " .
							"INDEX ( post_id ), " .
							"UNIQUE (post_id, name, value), " .
							"CONSTRAINT fk_posts_meta_tags " .
								"FOREIGN KEY (post_id) REFERENCES $this->postsTable (post_id) " .
								"ON UPDATE CASCADE " .
								"ON DELETE CASCADE " .
						") " .
						"ENGINE = InnoDB " .
						"CHARACTER SET utf8 " .
						"COLLATE utf8_general_ci";
		$db->query();
		
		$db->query =	"CREATE TABLE IF NOT EXISTS $this->contentTable " .
						"( " .
							"post_id INT NOT NULL PRIMARY KEY , " .
							"content TEXT NOT NULL, " .
							"CONSTRAINT fk_posts_content " .
								"FOREIGN KEY (post_id) REFERENCES $this->postsTable (post_id) " .
								"ON UPDATE CASCADE " .
								"ON DELETE CASCADE " .
						") " .
						"ENGINE = InnoDB " .
						"CHARACTER SET utf8 " .
						"COLLATE utf8_general_ci";
		$db->query();
		
		// Post example
		$post = new Post;
		$post->setAuthor("manialib");
		$post->setPostType(POST_TYPE_NEWS);
		$post->setTitle("Hello world !");
		$post->addMetaTag("image", "bg_coast.dds");
		$post->addMetaTag("tag", "welcome");
		$post->addMetaTag("tag", "manialib");
		$post->setContent("Lorem ipsum dolor sit amet, consectetur adipiscing " .
				"elit. Donec sit amet nulla magna. Etiam consequat porttitor " .
				"magna ac ultrices. Donec sed mattis ante. Aenean a felis nec " .
				"mauris venenatis vehicula. Cras tempor pellentesque justo, et " .
				"dapibus eros pharetra bibendum. Sed vitae auctor eros. Quisque " .
				"et lectus est, ac fermentum massa.");
		$post->dbUpdate();
		
		return true;
	}
}
?>