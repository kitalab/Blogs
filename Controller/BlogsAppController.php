<?php
/**
 * BlogsApp
 */
App::uses('AppController', 'Controller');

/**
 * Class BlogsAppController
 *
 * @property BlogFrameSetting $BlogFrameSetting
 * @property BlogSetting $BlogSetting
 */
class BlogsAppController extends AppController {

/**
 * @var array ブログ名
 */
	protected $_blogTitle;

/**
 * @var array ブログ設定
 */
	protected $_blogSetting;

/**
 * @var array フレーム設定
 */
	protected $_frameSetting;

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		//'NetCommons.NetCommonsBlock',
		//'NetCommons.NetCommonsFrame',
		'Pages.PageLayout',
		'Security',
	);

/**
 * @var array use model
 */
	public $uses = array(
		'Blogs.Blog',
		'Blogs.BlogSetting',
		'Blogs.BlogFrameSetting'
	);

/**
 * ブロック名をブログタイトルとしてセットする
 *
 * @return void
 */
	protected function _setupBlogTitle() {
		$this->loadModel('Blocks.Block');
		$block = $this->Block->findById(Current::read('Block.id'));
		$this->_blogTitle = $block['Block']['name'];
	}

/**
 * フレーム設定を読みこむ
 *
 * @return void
 */
	protected function _loadFrameSetting() {
		$this->_frameSetting = $this->BlogFrameSetting->getBlogFrameSetting();
	}

/**
 * 設定等の呼び出し
 *
 * @return void
 */
	protected function _prepare() {
		$this->_setupBlogTitle();
		$this->_initBlog(['blogSetting']);
		$this->_loadFrameSetting();
	}

/**
 * initBlog
 *
 * @param array $contains Optional result sets
 * @return bool True on success, False on failure
 */
	protected function _initBlog($contains = []) {
		if (! $blog = $this->Blog->getBlog(Current::read('Block.id'), Current::read('Room.id'))) {
			return $this->throwBadRequest();
		}
		$this->_blogTitle = $blog['Blog']['name'];
		$this->set('blog', $blog);

		if (! $blogSetting = $this->BlogSetting->getBlogSetting()) {
			$blogSetting = $this->BlogSetting->createBlockSetting();
			$blogSetting['BlogSetting']['blog_key'] = null;
		} else {
			$blogSetting['BlogSetting']['blog_key'] = $blog['Blog']['key'];
		}
		$this->_blogSetting = $blogSetting;
		$this->set('blogSetting', $blogSetting['BlogSetting']);

		$this->set('userId', (int)$this->Auth->user('id'));

		return true;
	}

}
