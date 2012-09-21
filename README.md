# MessageBoard

特定のメンバだけでやりとりできるメッセージボードと呼ばれるコメント欄をどこにでも設置できるモジュールです。

## 特徴

* 特定のメンバだけがコメントできるメッセージボードができる。
* 特定のメンバ以外にはメッセージボードの閲覧を制限できる。
* 添付ファイルが添付できる。

## 用途

* 運営陣のお客様対応ログ (管理画面のユーザ詳細ページに設置する場合を想定)
* スタッフ専用の備考欄

## 要件

* PHP 5.3以上
* XCL 2.2以上
* UTF-8の日本語

## インストール

1. モジュール管理でインストール
2. 登録ユーザにアクセス権限を付与
3. 添付ファイル保存用ディレクトリを作る

```
chmod 777 xoops_trust_path/uploads/messageboard
chmod 777 xoops_trust_path/uploads/messageboard/attachment
```

## 使い方

スレッドを表示するテンプレートに以下を追記:

```
<{xoops_explaceholder control="Commentin.ShowThread" clientKey="クライアントキー" userId=$xoops_userid|intval}>
```

`クライアントキー` はコメント対象の記事を一意に特定する文字列にする。例えば、"bulletin.story.\`$storyId`" など。


## API

スレッドを利用するには、クライアントモジュール側で、「スレッド作成」と「メンバーの追加」を実装する必要があります。

#### MessageBoard.CreateBoard: メッセージボード新規作成デリゲート

クライアントキーを指定してメッセージボードを新規作成するデリゲートです。クライアントモジュール側で、このデリゲートを呼び出します。

インタフェース:

```
	/**
	 * @param string $clientKey クライアントキー
	 * @param null $board 生成されたBoardオブジェクトが格納される
	 * @throws \RuntimeException
	 */
	"MessageBoard.CreateBoard" ($clientKey, &$boar = null)
```

こんな風に呼びだす:

```
try {
	$clientKey = 'bulletin.story.123';
	$board = null;
	XCube_DelegateUtils::call('MessageBoard.CreateBoard', $clientKey, new XCube_Ref($board));
} cacth (Exception $e) {
	// エラー処理
}
```

#### MessageBoard.AddMember: メンバー追加デリゲート

メッセージボードに参加するメンバーを追加するデリゲートです。クライアントモジュール側で、このデリゲートを呼び出します。

インタフェース:

```
	/**
	 * @param string $clientKey クライアントキー
	 * @param int $userId 追加したいユーザのID
	 * @throws \RuntimeException
	 */
	"MessageBoard.AddMember" ($clientKey, $userId)
``` 

こんな風に呼びだす:

```
try {
	$clientKey = 'bulletin.story.123';
	$userId = 3456;
	XCube_DelegateUtils::call('MessageBoard.AddMember', $clientKey, $userId);
} cacth (Exception $e) {
	// エラー処理
}
```


## 今後

* メンバじゃなくても閲覧・投稿できるようにしたい
* プログラムレスでコメント機能を埋め込めるようにしたい 
