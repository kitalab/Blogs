<?php
/**
 * BlogEntry Model
 *
 * @property BlogCategory $BlogCategory
 * @property BlogEntryTagLink $BlogEntryTagLink
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @link     http://www.netcommons.org NetCommons Project
 * @license  http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('BlogsAppModel', 'Blogs.Model');
App::uses('NetCommonsTime', 'NetCommons.Utility');

/**
 * Summary for BlogEntry Model
 */
class BlogEntry extends BlogsAppModel {

/**
 * @var int recursiveはデフォルトアソシエーションなしに
 */
	public $recursive = -1;

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.Trackable',
		'Tags.Tag',
		'NetCommons.OriginalKey',
		//'NetCommons.Publishable',
		'Workflow.Workflow',
		'Likes.Like',
		'Workflow.WorkflowComment',
		//'Categories.Category',
		'ContentComments.ContentComment'
		);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Category' => array(
			'className' => 'Categories.Category',
			'foreignKey' => 'category_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	//	'CategoryOrder' => array(
	//		'className' => 'Categories.CategoryOrder',
	//		'foreignKey' => false,
	//		'conditions' => 'CategoryOrder.category_key=Category.key',
	//		'fields' => '',
	//		'order' => ''
	//	)
	);

/**
 * バリデーションルールを返す
 *
 * @return array
 */
	protected function _getValidateSpecification() {
		$validate = array(
			'title' => array(
				'notBlank' => [
					'rule' => array('notBlank'),
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('blogs', 'Title')),
					//'allowEmpty' => false,
					'required' => true,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				],
			),
			'body1' => array(
				'notBlank' => [
					'rule' => array('notBlank'),
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('blogs', 'Body1')),
					//'allowEmpty' => false,
					'required' => true,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				],
			),
			'publish_start' => array(
				'notBlank' => [
					'rule' => array('notBlank'),
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('blogs', 'Published datetime')),
					//'allowEmpty' => false,
					'required' => true,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				],
			),
			'category_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					//'message' => 'Your custom message here',
					'allowEmpty' => true,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			//'key' => array(
			//	'notBlank' => array(
			//		'rule' => array('notBlank'),
			//		//'message' => 'Your custom message here',
			//		//'allowEmpty' => false,
			//		//'required' => false,
			//		//'last' => false, // Stop validation after this rule
			//		//'on' => 'create', // Limit validation to 'create' or 'update' operations
			//	),
			//),
			'status' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					//'message' => 'Your custom message here',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'is_auto_translated' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					//'message' => 'Your custom message here',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
		);
		return $validate;
	}

/**
 * 空の新規データを返す
 *
 * @return array
 */
	public function getNew() {
		$new = parent::getNew();
		$netCommonsTime = new NetCommonsTime();
		$new['BlogEntry']['publish_start'] = $netCommonsTime->getNowDatetime();
		return $new;
	}
/**
 * UserIdと権限から参照可能なEntryを取得するCondition配列を返す
 *
 * @param int $blockId ブロックId
 * @param int $userId アクセスユーザID
 * @param array $permissions 権限
 * @param datetime $currentDateTime 現在日時
 * @return array condition
 */
	public function getConditions($blockId, $userId, $permissions, $currentDateTime) {
		// contentReadable falseなら何も見えない
		if ($permissions['content_readable'] === false) {
			$conditions = array('BlogEntry.id' => 0); // ありえない条件でヒット0にしてる
			return $conditions;
		}

		// デフォルト絞り込み条件
		$conditions = array(
			'BlogEntry.block_id' => $blockId
		);

		$conditions = $this->getWorkflowConditions($conditions);

		//if ($permissions['content_editable']) {
		////if ($this->canEditW/orkflowContent()) {
		//	// 編集権限
		//	$conditions['BlogEntry.is_latest'] = 1;
		//	return $conditions;
		//}
		//
		//if ($permissions['content_creatable']) {
		////if ($this->canCreateWorkflowContent()) {
		//	// 作成権限
		//	$conditions['OR'] = array(
		//		array_merge(
		//			$this->_getPublishedConditions($currentDateTime),
		//			array('BlogEntry.created_user !=' => $userId)
		//		),
		//		array('BlogEntry.created_user' => $userId,
		//				'BlogEntry.is_latest' => 1)
		//	);
		//	return $conditions;
		//}
		//
		//if ($permissions['content_readable']) {
		////if ($this->canReadWorkflowContent()) {
		//	 //公開中コンテンツだけ
		//	$conditions = array_merge(
		//		$conditions,
		//		$this->_getPublishedConditions($currentDateTime));
		//	return $conditions;
		//}

		return $conditions;
	}

/**
 * 年月毎の記事数を返す
 *
 * @param int $blockId ブロックID
 * @param int $userId ユーザID
 * @param array $permissions 権限
 * @param datetime $currentDateTime 現在日時
 * @return array
 */
	public function getYearMonthCount($blockId, $userId, $permissions, $currentDateTime) {
		$conditions = $this->getConditions($blockId, $userId, $permissions, $currentDateTime);
		// 年月でグループ化してカウント→取得できなかった年月をゼロセット
		$this->virtualFields['year_month'] = 0; // バーチャルフィールドを追加
		$this->virtualFields['count'] = 0; // バーチャルフィールドを追加
		$result = $this->find(
			'all',
			array(
				'fields' => array(
					'DATE_FORMAT(BlogEntry.publish_start, \'%Y-%m\') AS BlogEntry__year_month',
					'count(*) AS BlogEntry__count'
				),
				'conditions' => $conditions,
				'group' => array('BlogEntry__year_month'), //GROUP BY YEAR(record_date), MONTH(record_date)
			)
		);
		// 使ったバーチャルFieldを削除
		unset($this->virtualFields['year_month']);
		unset($this->virtualFields['count']);

		$ret = array();
		// $retをゼロ埋め
		//　一番古い記事を取得
		$oldestEntry = $this->find('first',
			array(
				'conditions' => $conditions,
				'order' => 'publish_start ASC',
			)
		);

		// 一番古い記事の年月から現在までを先にゼロ埋め
		if (isset($oldestEntry['BlogEntry'])) {
			$currentYearMonthDay = date('Y-m-01', strtotime($oldestEntry['BlogEntry']['publish_start']));
		} else {
			// 記事がなかったら今月だけ
			$currentYearMonthDay = date('Y-m-01', strtotime($currentDateTime));
		}
		while ($currentYearMonthDay <= $currentDateTime) {
			$ret[substr($currentYearMonthDay, 0, 7)] = 0;
			$currentYearMonthDay = date('Y-m-01', strtotime($currentYearMonthDay . ' +1 month'));
		}
		// 記事がある年月は記事数を上書きしておく
		foreach ($result as $yearMonth) {
			$ret[$yearMonth['BlogEntry']['year_month']] = $yearMonth['BlogEntry']['count'];
		}

		//年月降順に並び替える
		krsort($ret);
		return $ret;
	}

/**
 * 記事の保存。タグも保存する
 *
 * @param int $blockId ブロックID
 * @param int $frameId frame ID
 * @param array $data 登録データ
 * @return bool
 * @throws InternalErrorException
 */
	public function saveEntry($blockId, $frameId, $data) {
		$this->begin();
		try {
			$this->create(); // 常に新規登録
			// 先にvalidate 失敗したらfalse返す
			$this->set($data);
			if (!$this->validates($data)) {
				$this->rollback();
				return false;
			}
			if (($savedData = $this->save($data, false)) === false) {
				//このsaveで失敗するならvalidate以外なので例外なげる
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			$this->commit();
			return $savedData;

		} catch (Exception $e) {
			$this->rollback();
			//エラー出力
			CakeLog::error($e);
			throw $e;
		}
	}

/**
 * 記事削除
 *
 * @param int $key オリジンID
 * @throws InternalErrorException
 * @return bool
 */
	public function deleteEntryByKey($key) {
		// ε(　　　　 v ﾟωﾟ)　＜タグリンク削除
		$this->begin();
		try{
			//コメントの削除
			$deleteEntry = $this->findByKey($key);
			//コメントの削除
			$this->deleteCommentsByContentKey($deleteEntry['BlogEntry']['key']);

			// 記事削除
			$conditions = array('key' => $key);
			if ($result = $this->deleteAll($conditions, true, true)) {
				$this->commit();
				return $result;
			} else {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
		} catch (Exception $e) {
			$this->rollback();
			//エラー出力
			CakeLog::error($e);
			throw $e;
		}
	}

/**
 * 過去に一度も公開されてないか
 *
 * @param array $blogEntry チェック対象記事
 * @return bool true:公開されてない false: 公開されたことあり
 */
	public function yetPublish($blogEntry) {
		$conditions = array(
			'BlogEntry.key' => $blogEntry['BlogEntry']['key'],
			'BlogEntry.is_active' => 1
		);
		$count = $this->find('count', array('conditions' => $conditions));
		return ($count == 0);
	}

/**
 * 公開データ取得のconditionsを返す
 *
 * @param datetime $currentDateTime 現在の日時
 * @return array
 */
	protected function _getPublishedConditions($currentDateTime) {
		return array(
			$this->name . '.is_active' => 1,
			'BlogEntry.publish_start <=' => $currentDateTime,
		);
	}

}
