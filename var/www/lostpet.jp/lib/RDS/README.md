# RDS
- MySQL関連の処理を行なう。
- 第二引数でPlaceholderの実値を配列で指定。

## examples
1つの列を取得。
```php
$count = RDS::fetchColumn("SELECT COUNT(*) FROM `hoge` WHERE `color`=?;", [
  "red",
]);
```

1つの行を取得。
```php
$row = RDS::fetch("SELECT * FROM `hoge` WHERE `id`=?;", [
  1,
]);
```
複数の行を取得。
```php
$rows = RDS::fetchAll("SELECT * FROM `hoge` WHERE ? > `id` AND `id` > ?;", [
  1000, 50,
]);
```
挿入してIDを取得。
```php
$id = RDS::insert("INSERT INTO `hoge` (`title`, `created_at`) VALUES (?, ?);", [
  "表題", time(),
]);
```
多様な処理を実行。
```php
RDS::execute("UPDATE `hoge` SET `updated_at`=? WHERE `id`=?;", [
  time(), 5,
]);
```

## DB
### `case`
案件の情報を保存する。

```typescript
{
  *id: number  // ID
  status: number  // 0=削除, 1=有効
  view: number  // 0=終了, 1=継続中 (検索に使用), 2=アーカイブ(操作不可)
  matter: number  // 事例の種類 (検索に使用)
  animal: number  // 動物の種類 (検索に使用)
  prefecture: number  // 都道府県の種類 (検索に使用)
  created_at: number  // 作成日時
  updated_at: number  // 更新日時  
  starts_at: number // 発生日時
  ends_at: number // 終了日時
  expires_at: number  // 掲載期限
  head: string  // JSON string (一覧表示に必要)
  body: string  // JSON string (head + bodyが個別表示に必要)
  password: string  // パスワード
  email: string // メールアドレス
}
```

#### データ
- 案件の内容データと、それが必要になる箇所をまとめたもの。
- 個別表示では全てのデータが必要となる。
- 一覧表示用に必要なデータ(`head`)と不要なデータ(`body`)を別にする。
- 拡張性を持たせるために、JSON文字列(decode、encodeはアプリ側)で保存する。

| 項目 | 迷子 | 保護 | 目撃 | 一覧表示 |
| -- | -- | -- | -- | -- |
| タイトル (`title`) | y | y | y | y |
| ペット名 (`pet`) | y | n | n | y |
| 市区町村以下 (`location`) | y | y | y | y |
| 写真 (`photos`) | y | y | y | y |
| 性別 (`sex`) | y | n | n | n |
| 年齢 (`age`) | y | n | n | n |
| 投稿者名 (`author`) | y | y | y | n |
| 連絡先URL (`contact`) | y | y | y | n |
| 動画 (`videos`) | y | y | y | n |
| オープングラフ (`opengraph`) | y | y | y | n |
| 詳細 (`description`) | y | y | y | n |
| 終了報告 (`report`) | y | y | y | n |

##### head
```typescript
{
  title: string // 案件のタイトル
  pet: string // ペット名
  location: string  // 市区町村以下
  photos: Array<number> // 写真ID
}
```

##### body
```typescript
{
  sex: number // 性別ID
  age: string // 年齢の文字表現
  author: string  // 投稿者名
  contact: string // 連絡先の文字表現
  videos: Array<number> // 動画IDの配列
  opengraph: number // 写真ID
  description: string // 詳細
  report: string  // 終了時の報告
}
```

### `comment`
案件の掲示板のコメントを保存する。

```typescript
{
  *id: number  // コメントID
  status: number  // 0=削除, 1=有効
  private: number // 0=公開, 1=非公開
  case: number  // 案件ID
  parent: number  // スレッドのコメントID
  created_at: number  // 作成日時
  updated_at: number  // 更新日時
  head: string  // JSON string (一覧表示に必要)
  body: string  // JSON string (head + bodyが個別表示に必要)
  password: string  // パスワード
}
```

#### データ
- コメント内容のデータと、それが必要な箇所をまとめたもの。

| 項目 | スレッド | 返信 | 一覧表示 |
| -- | -- | -- | -- |
| タイトル (`title`) | y | n | y |
| 投稿者 (`author`) | y | y | y |
| 写真 (`photos`) | y | y | n |
| 動画 (`videos`) | y | y | n |
| 詳細 (`description`) | y | y | n |

##### head
```typescript
{
  title: string // スレッドのタイトル
  author: string  // 投稿者名
}
```

##### body
```typescript
{
  photos: Array<number> // 写真IDの配列
  videos: Array<number> // 動画IDの配列
  description: string // 詳細
}
```

### `media`
写真と動画の情報を管理する。

#### 写真
1. 案件の写真。
2. 案件のオープングラフに使用する写真。
3. 掲示板のコメントに添付する写真。

このうち、1は「元の写真」と「注目範囲の切り抜き写真」の2種類を1つのデータとして扱う必要がある。

#### 動画
1. 案件の動画。
2. 掲示板のコメントに添付する動画。

```typescript
{
  *id: number  // メディアID
  status: number  // 0=削除, 1=有効
  type: number  // 1=写真, 2=動画
  name: string  // ファイル名
  created_at: number  // 作成日時
  updated_at: number  // 更新日時
}
```

### `media-relation`
メディアがどこに関連付けられているかを管理する。

- 関連付けが1つも存在しないメディアは削除できる。

```typescript
{
  *root: number  // メディアID
  *table: number // 関連付けの種類
  *content: number // 関連付け先のコンテンツID (案件ID、コメントID)
  status: number  // 0=削除, 1=有効
  updated_at: number  // 更新日時
}
```

| table | content | 説明 |
| -- | -- | -- |
| `1` | 案件ID | 対象案件IDに添付したメディアとして使用中。 |
| `2` | メディアID | 対象メディアIDを切り抜いたメディアとして使用中。 |
| `3` | コメントID | 対象コメントIDに添付したメディアとして使用中。 |
| `4` | 案件ID | 対象案件IDのオープングラフ画像として使用中。 |

### `queue`
同期的に実行する必要がない処理や遅れて実行させたい処理を、バッチで実行するためのキューを管理する。  
同種類、同コンテンツのキューが発生した場合、`starts_at`を遅延させる。

```typescript
{
  *table: number // キューの種類
  *content: number // 対象コンテンツのID
  created_at: number  // 作成日時
  updated_at: number  // 更新日時
  starts_at: number // この時間以降にキューが実行される
  status: number  // 0=削除, 1=有効
}
```

| table | content | 説明 |
| -- | -- | -- |

### `contact`
問い合わせより送られたメッセージを管理する。  

```typescript
{
  id: number
  title: string
  description: string
  created_at: number
  updated_at: number
}
```